<?php
require_once __DIR__ . '/../config/articles.php';
require_once __DIR__ . '/../config/image.php';
require_can('create_article');

$basePath   = '../';
$activeMenu = 'articles';

$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;
$article = null;
$links = [];
$gallery = [];

if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM articles WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    if (!$article) {
        set_flash('danger', 'Article not found.');
        redirect('articles/index.php');
    }
    // Only people who can edit this article may open the editor.
    if (!can_on_article('edit', $article)) {
        http_response_code(403);
        die('403 - You cannot edit this article.');
    }
    $ls = db()->prepare('SELECT label, url FROM article_links WHERE article_id = ? ORDER BY id');
    $ls->execute([$id]);
    $links = $ls->fetchAll();

    $gs = db()->prepare('SELECT id, path, caption FROM article_images WHERE article_id = ? ORDER BY sort_order, id');
    $gs->execute([$id]);
    $gallery = $gs->fetchAll();
}

$pageTitle = $isEdit ? 'Edit Article' : 'New Article';

// Active categories as a nested tree so the dropdown shows "Section -> Sub-section".
// Also include the category this article is already filed under, even if it has
// since been deactivated, so the editor never shows a blank/lost selection.
$currentCat = (int) ($article['category_id'] ?? 0);
$catStmt = db()->prepare(
    'SELECT id, parent_id, name, sort_order FROM categories WHERE is_active = 1 OR id = ?'
);
$catStmt->execute([$currentCat]);
$categories = $catStmt->fetchAll();
$catOptions = build_category_options($categories);
$radios     = db()->query('SELECT id, name FROM radio_streams WHERE is_active = 1 ORDER BY name')->fetchAll();
$authors    = can_reassign_author() ? authorable_users() : [];

// ---- Handle submit ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'category_id'  => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
        'primary_lang' => in_array($_POST['primary_lang'] ?? '', ['rw', 'fr', 'en'], true) ? $_POST['primary_lang'] : 'en',
        'radio_stream_id' => ($_POST['radio_stream_id'] ?? '') !== '' ? (int) $_POST['radio_stream_id'] : null,
        'media_type'   => in_array($_POST['media_type'] ?? '', ['none', 'video', 'audio'], true) ? $_POST['media_type'] : 'none',
        'media_url'    => trim($_POST['media_url'] ?? '') ?: null,
        'title_rw' => trim($_POST['title_rw'] ?? '') ?: null,
        'title_fr' => trim($_POST['title_fr'] ?? '') ?: null,
        'title_en' => trim($_POST['title_en'] ?? '') ?: null,
        'excerpt_rw' => trim($_POST['excerpt_rw'] ?? '') ?: null,
        'excerpt_fr' => trim($_POST['excerpt_fr'] ?? '') ?: null,
        'excerpt_en' => trim($_POST['excerpt_en'] ?? '') ?: null,
        'body_rw' => trim($_POST['body_rw'] ?? '') ?: null,
        'body_fr' => trim($_POST['body_fr'] ?? '') ?: null,
        'body_en' => trim($_POST['body_en'] ?? '') ?: null,
        // Homepage curation flags (editors only; ignored for others below).
        'is_featured'    => !empty($_POST['is_featured']) ? 1 : 0,
        'is_breaking'    => !empty($_POST['is_breaking']) ? 1 : 0,
        'is_editor_pick' => !empty($_POST['is_editor_pick']) ? 1 : 0,
    ];

    // Only publishers may set the homepage curation flags. For everyone else,
    // keep whatever the article already had (don't let the UI grant it).
    if (!can('publish')) {
        $data['is_featured']    = (int) ($article['is_featured'] ?? 0);
        $data['is_breaking']    = (int) ($article['is_breaking'] ?? 0);
        $data['is_editor_pick'] = (int) ($article['is_editor_pick'] ?? 0);
    }

    // "save_draft" keeps/sets draft; "submit_review" sets pending (if permitted).
    $intent = $_POST['intent'] ?? 'save_draft';
    $errors = [];

    // The primary language needs both a title and a body.
    $pl = $data['primary_lang'];
    if (empty($data["title_$pl"])) {
        $errors[] = 'The title in the primary language (' . lang_label($pl) . ') is required.';
    }
    if (empty($data["body_$pl"])) {
        $errors[] = 'The body in the primary language (' . lang_label($pl) . ') is required.';
    }

    // Featured image (optional).
    $imagePath = $article['featured_image'] ?? null;
    try {
        if (!empty($_FILES['featured_image']['name'])) {
            $newImg = save_article_image($_FILES['featured_image']);
            if ($newImg) {
                delete_upload_file($article['featured_image'] ?? null);
                $imagePath = $newImg;
            }
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    // Normalise embed URL (add https:// if the user omitted the scheme).
    if ($data['media_url'] && !preg_match('~^https?://~i', $data['media_url'])) {
        $data['media_url'] = 'https://' . $data['media_url'];
    }

    // Media file (optional). Keep the existing file unless a new one is uploaded
    // or media is switched to "none".
    $mediaFile = $article['media_file'] ?? null;
    try {
        if (!empty($_FILES['media_file']['name'])) {
            $newMedia = save_article_media($_FILES['media_file']);
            if ($newMedia) {
                delete_upload_file($article['media_file'] ?? null);
                $mediaFile = $newMedia;
                // Auto-classify the media type from the uploaded file if unset.
                if ($data['media_type'] === 'none') {
                    $data['media_type'] = media_kind_from_path($newMedia);
                }
            }
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    // If media is "none", drop any url/file so the article is plain text.
    if ($data['media_type'] === 'none') {
        delete_upload_file($mediaFile);
        $mediaFile = null;
        $data['media_url'] = null;
    }

    // Author: editors may reassign; everyone else keeps the existing/own author.
    $authorId = $article['author_id'] ?? current_user()['id'];
    if (can_reassign_author() && !empty($_POST['author_id'])) {
        $authorId = (int) $_POST['author_id'];
    }

    if ($errors) {
        foreach ($errors as $err) {
            set_flash('danger', $err);
        }
        // Fall through to re-render with submitted values.
        $article = array_merge($article ?? [], $data, [
            'featured_image' => $imagePath,
            'media_file'     => $mediaFile,
            'author_id'      => $authorId,
            'id'             => $id,
        ]);
        // Rebuild links from POST so they aren't lost.
        $links = [];
        foreach (($_POST['link_url'] ?? []) as $i => $u) {
            $u = trim($u);
            if ($u !== '') {
                $links[] = ['label' => trim($_POST['link_label'][$i] ?? ''), 'url' => $u];
            }
        }
    } else {
        // Decide status.
        $status = $article['status'] ?? 'draft';
        if ($intent === 'submit_review' && can('submit_review')) {
            $status = 'pending';
        } elseif (!$isEdit) {
            $status = 'draft';
        }

        // Slug from the primary-language title (stable on edit unless empty).
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            $slug = slugify($data["title_$pl"]) . '-' . substr(bin2hex(random_bytes(3)), 0, 4);
        }

        if ($isEdit) {
            $sql = 'UPDATE articles SET category_id=:category_id, author_id=:author_id, primary_lang=:primary_lang,
                        radio_stream_id=:radio_stream_id, media_type=:media_type, media_url=:media_url, media_file=:media_file,
                        title_rw=:title_rw, title_fr=:title_fr, title_en=:title_en,
                        excerpt_rw=:excerpt_rw, excerpt_fr=:excerpt_fr, excerpt_en=:excerpt_en,
                        body_rw=:body_rw, body_fr=:body_fr, body_en=:body_en,
                        featured_image=:featured_image, status=:status,
                        is_featured=:is_featured, is_breaking=:is_breaking, is_editor_pick=:is_editor_pick
                    WHERE id=:id';
            $params = $data + [
                'author_id' => $authorId,
                'featured_image' => $imagePath,
                'media_file' => $mediaFile,
                'status' => $status,
                'id' => $id,
            ];
            db()->prepare($sql)->execute($params);
            $articleId = $id;
        } else {
            $sql = 'INSERT INTO articles (category_id, author_id, slug, primary_lang, radio_stream_id,
                        media_type, media_url, media_file,
                        title_rw, title_fr, title_en, excerpt_rw, excerpt_fr, excerpt_en,
                        body_rw, body_fr, body_en, featured_image, status,
                        is_featured, is_breaking, is_editor_pick)
                    VALUES (:category_id, :author_id, :slug, :primary_lang, :radio_stream_id,
                        :media_type, :media_url, :media_file,
                        :title_rw, :title_fr, :title_en, :excerpt_rw, :excerpt_fr, :excerpt_en,
                        :body_rw, :body_fr, :body_en, :featured_image, :status,
                        :is_featured, :is_breaking, :is_editor_pick)';
            $params = $data + [
                'author_id' => $authorId,
                'slug' => $slug,
                'media_file' => $mediaFile,
                'featured_image' => $imagePath,
                'status' => $status,
            ];
            db()->prepare($sql)->execute($params);
            $articleId = (int) db()->lastInsertId();
        }

        // Replace related links.
        db()->prepare('DELETE FROM article_links WHERE article_id = ?')->execute([$articleId]);
        $linkStmt = db()->prepare('INSERT INTO article_links (article_id, label, url) VALUES (?, ?, ?)');
        foreach (($_POST['link_url'] ?? []) as $i => $u) {
            $u = trim($u);
            if ($u === '') {
                continue;
            }
            if (!preg_match('~^https?://~i', $u)) {
                $u = 'https://' . $u;
            }
            $linkStmt->execute([$articleId, trim($_POST['link_label'][$i] ?? '') ?: null, $u]);
        }

        // ---- Gallery images ----
        // 1) Delete images the user removed (only those belonging to this article).
        $removeIds = array_filter(array_map('intval', $_POST['remove_image'] ?? []));
        if ($removeIds) {
            $in = implode(',', array_fill(0, count($removeIds), '?'));
            $sel = db()->prepare("SELECT id, path FROM article_images WHERE article_id = ? AND id IN ($in)");
            $sel->execute(array_merge([$articleId], $removeIds));
            foreach ($sel->fetchAll() as $row) {
                delete_upload_file($row['path']);
            }
            $del = db()->prepare("DELETE FROM article_images WHERE article_id = ? AND id IN ($in)");
            $del->execute(array_merge([$articleId], $removeIds));
        }

        // 2) Persist the new order/captions of EXISTING images.
        if (!empty($_POST['existing_image_id'])) {
            $ord = db()->prepare('UPDATE article_images SET sort_order = ?, caption = ? WHERE id = ? AND article_id = ?');
            foreach ($_POST['existing_image_id'] as $order => $imgId) {
                $cap = trim($_POST['existing_caption'][$imgId] ?? '') ?: null;
                $ord->execute([(int) $order, $cap, (int) $imgId, $articleId]);
            }
        }

        // 3) Upload newly added gallery files, appended after existing ones.
        try {
            $newPaths = save_gallery_images(normalize_files_array($_FILES['gallery'] ?? []));
            if ($newPaths) {
                $base = (int) (db()->query('SELECT COALESCE(MAX(sort_order), -1) FROM article_images WHERE article_id = ' . $articleId)->fetchColumn());
                $ins = db()->prepare('INSERT INTO article_images (article_id, path, sort_order) VALUES (?, ?, ?)');
                foreach ($newPaths as $p) {
                    $ins->execute([$articleId, $p, ++$base]);
                }
            }
        } catch (RuntimeException $ex) {
            set_flash('warning', 'Some gallery images were not added: ' . $ex->getMessage());
        }

        set_flash('success', $status === 'pending' ? 'Article submitted for review.' : 'Article saved.');
        redirect('articles/edit.php?id=' . $articleId);
    }
}

// Values for the form (existing article, or empty defaults).
$v = static fn(string $k) => $article[$k] ?? '';
$primaryLang = $article['primary_lang'] ?? 'en';

$pageStyles = '<link href="assets/libs/quill/quill.snow.css" rel="stylesheet" type="text/css">'
    . '<style>.ql-editor img{max-width:100%;height:auto;border-radius:.25rem;margin:.5rem 0;}</style>';

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0"><?= $isEdit ? 'Edit Article' : 'New Article' ?></h4>
                    <?php if ($isEdit): ?>
                        <span class="badge <?= status_badge_class($article['status']) ?> text-uppercase"><?= e($article['status']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-auto">
                    <a href="articles/index.php" class="btn btn-light"><i class="ri-arrow-left-line align-bottom"></i> Back</a>
                </div>
            </div>

            <form method="post" enctype="multipart/form-data" id="articleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="intent" id="intent" value="save_draft">

                <div class="row">
                    <!-- Main column: multilingual content -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-en" role="tab">🇬🇧 English</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-fr" role="tab">🇫🇷 French</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-rw" role="tab">🇷🇼 Kinyarwanda</a></li>
                                </ul>

                                <div class="tab-content">
                                    <?php foreach (['en' => 'English', 'fr' => 'French', 'rw' => 'Kinyarwanda'] as $code => $label): ?>
                                        <div class="tab-pane <?= $code === 'en' ? 'show active' : '' ?>" id="tab-<?= $code ?>" role="tabpanel">
                                            <div class="mb-3">
                                                <label class="form-label">Title (<?= $label ?>)</label>
                                                <input type="text" name="title_<?= $code ?>" class="form-control"
                                                       value="<?= e($v("title_$code")) ?>" placeholder="Headline in <?= $label ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Short summary (<?= $label ?>)</label>
                                                <textarea name="excerpt_<?= $code ?>" class="form-control" rows="2"
                                                          placeholder="One or two sentences shown on cards"><?= e($v("excerpt_$code")) ?></textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Body (<?= $label ?>)</label>
                                                <div id="editor-<?= $code ?>" class="quill-editor" style="min-height:220px;"></div>
                                                <textarea name="body_<?= $code ?>" id="body-<?= $code ?>" class="d-none"><?= e($v("body_$code")) ?></textarea>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Related external links -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">Related Links & References</h5>
                                <button type="button" class="btn btn-sm btn-soft-primary" onclick="addLinkRow()"><i class="ri-add-line"></i> Add link</button>
                            </div>
                            <div class="card-body">
                                <div id="links-wrap">
                                    <?php if ($links): foreach ($links as $ln): ?>
                                        <div class="row g-2 mb-2 link-row">
                                            <div class="col-md-4"><input type="text" name="link_label[]" class="form-control" placeholder="Label (optional)" value="<?= e($ln['label'] ?? '') ?>"></div>
                                            <div class="col-md-7"><input type="url" name="link_url[]" class="form-control" placeholder="https://..." value="<?= e($ln['url']) ?>"></div>
                                            <div class="col-md-1 d-grid"><button type="button" class="btn btn-soft-danger" onclick="this.closest('.link-row').remove()"><i class="ri-close-line"></i></button></div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                                <small class="text-muted">External sources or related reading. URLs without http(s) get https:// added automatically.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar: settings -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Publish Settings</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Primary language <span class="text-danger">*</span></label>
                                    <select name="primary_lang" class="form-select">
                                        <?php foreach (['en' => 'English', 'fr' => 'French', 'rw' => 'Kinyarwanda'] as $code => $label): ?>
                                            <option value="<?= $code ?>" <?= $primaryLang === $code ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Main language; its title is required and used for filtering.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">— Uncategorised —</option>
                                        <?php foreach ($catOptions as $c): ?>
                                            <option value="<?= (int) $c['id'] ?>" <?= (int) $v('category_id') === (int) $c['id'] ? 'selected' : '' ?>>
                                                <?= str_repeat('&nbsp;&nbsp;&nbsp;', $c['depth']) . ($c['depth'] ? '— ' : '') . e($c['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Pick the specific sub-section so the story lands in the right place.</small>
                                </div>

                                <?php if (can('publish')): ?>
                                <div class="mb-3 p-3 border rounded bg-light-subtle">
                                    <label class="form-label d-block fw-semibold mb-1">
                                        <i class="ri-global-line align-bottom me-1"></i>Public website placement
                                    </label>
                                    <p class="text-muted small mb-3">
                                        Choose where this story appears on the public site. These only take
                                        effect once the article is <span class="fw-medium">Published</span>.
                                    </p>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_featured"
                                               name="is_featured" value="1" <?= $v('is_featured') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured story
                                            <span class="d-block text-muted small">Large highlight at the top of the homepage.</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_breaking"
                                               name="is_breaking" value="1" <?= $v('is_breaking') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_breaking">
                                            Breaking news
                                            <span class="d-block text-muted small">Scrolling ticker across the top of every page.</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_editor_pick"
                                               name="is_editor_pick" value="1" <?= $v('is_editor_pick') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_editor_pick">
                                            Editor's pick
                                            <span class="d-block text-muted small">Listed in the “Editor’s picks” block on the homepage.</span>
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Embedded radio stream</label>
                                    <select name="radio_stream_id" class="form-select">
                                        <option value="">— None —</option>
                                        <?php foreach ($radios as $r): ?>
                                            <option value="<?= (int) $r['id'] ?>" <?= (int) $v('radio_stream_id') === (int) $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-1">
                                    <label class="form-label">Author</label>
                                    <?php if (can_reassign_author()): ?>
                                        <?php $currentAuthor = (int) ($article['author_id'] ?? current_user()['id']); ?>
                                        <select name="author_id" class="form-select">
                                            <?php foreach ($authors as $au): ?>
                                                <option value="<?= (int) $au['id'] ?>" <?= $currentAuthor === (int) $au['id'] ? 'selected' : '' ?>>
                                                    <?= e($au['name']) ?> — <?= e($au['role_label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">As an editor you can publish on behalf of another author.</small>
                                    <?php else: ?>
                                        <input type="text" class="form-control" value="<?= e(current_user()['name']) ?>" disabled>
                                        <small class="text-muted">This article will be credited to you.</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Video / Audio media -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Video / Audio</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Media type</label>
                                    <select name="media_type" id="media_type" class="form-select" onchange="toggleMedia()">
                                        <?php $mt = $v('media_type') ?: 'none'; ?>
                                        <option value="none"  <?= $mt === 'none'  ? 'selected' : '' ?>>None (text article)</option>
                                        <option value="video" <?= $mt === 'video' ? 'selected' : '' ?>>Video</option>
                                        <option value="audio" <?= $mt === 'audio' ? 'selected' : '' ?>>Audio</option>
                                    </select>
                                </div>

                                <div id="media-fields" class="<?= $mt === 'none' ? 'd-none' : '' ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Embed link</label>
                                        <input type="url" name="media_url" class="form-control"
                                               value="<?= e($v('media_url')) ?>"
                                               placeholder="YouTube, Vimeo, SoundCloud or stream URL">
                                        <small class="text-muted">Paste a link to embed a player.</small>
                                    </div>

                                    <div class="text-center text-muted my-2 fs-12">— or upload a file —</div>

                                    <div class="mb-2">
                                        <label class="form-label">Upload media file</label>
                                        <?php if ($v('media_file')): ?>
                                            <div class="alert alert-info py-2 px-2 fs-13 mb-2">
                                                Current file: <a href="<?= e($v('media_file')) ?>" target="_blank">view</a>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="media_file" class="form-control" accept="video/*,audio/*">
                                        <small class="text-muted">MP4/WebM/MOV or MP3/M4A/OGG/WAV. Max <b>40 MB</b>.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Featured Image</h5></div>
                            <div class="card-body">
                                <img src="<?= $v('featured_image') ? e($v('featured_image')) : '' ?>"
                                     alt="featured" id="img-preview"
                                     class="img-fluid rounded mb-2 <?= $v('featured_image') ? '' : 'd-none' ?>">
                                <input type="file" name="featured_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                <small class="text-muted">Recommended 800×400px. JPG/PNG/WebP, max 5 MB.</small>
                            </div>
                        </div>

                        <!-- Image gallery -->
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Image Gallery</h5></div>
                            <div class="card-body">
                                <?php if (!$isEdit): ?>
                                    <div class="alert alert-info py-2 fs-13 mb-2">Save the article first, then add gallery images.</div>
                                <?php endif; ?>

                                <div id="gallery-list" class="row g-2 mb-2">
                                    <?php foreach ($gallery as $g): ?>
                                        <div class="col-6 gallery-item" data-id="<?= (int) $g['id'] ?>">
                                            <div class="border rounded p-1 position-relative">
                                                <input type="hidden" name="existing_image_id[]" value="<?= (int) $g['id'] ?>">
                                                <img src="<?= e($g['path']) ?>" alt="gallery" class="img-fluid rounded mb-1" style="height:90px;width:100%;object-fit:cover;cursor:move;">
                                                <input type="text" name="existing_caption[<?= (int) $g['id'] ?>]" value="<?= e($g['caption'] ?? '') ?>"
                                                       class="form-control form-control-sm" placeholder="Caption">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 py-0 px-1"
                                                        onclick="removeGalleryItem(this, <?= (int) $g['id'] ?>)" title="Remove">&times;</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($isEdit): ?>
                                    <label class="form-label">Add images</label>
                                    <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                                    <small class="text-muted">Select one or more. JPG/PNG/WebP/GIF, max 5 MB each. Drag thumbnails to reorder.</small>
                                <?php endif; ?>

                                <!-- holds remove_image[] hidden inputs -->
                                <div id="gallery-removals"></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body d-grid gap-2">
                                <button type="submit" class="btn btn-light" onclick="document.getElementById('intent').value='save_draft'">
                                    <i class="ri-save-line align-bottom me-1"></i> Save Draft
                                </button>
                                <?php if (can('submit_review')): ?>
                                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('intent').value='submit_review'">
                                        <i class="ri-send-plane-line align-bottom me-1"></i> Submit for Review
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.</div></div>
        </div>
    </footer>
</div>

<?php
$pageScripts = <<<'HTML'
<script src="assets/libs/quill/quill.min.js"></script>
<script src="assets/libs/sortablejs/Sortable.min.js"></script>
<script>
    // CSRF token (from the form's hidden field) used for inline image uploads.
    const CSRF = document.querySelector('#articleForm input[name="csrf"]').value;
    const INLINE_UPLOAD_URL = 'articles/upload-inline.php';

    // Upload a picked image and insert it at the current cursor in a Quill editor.
    function inlineImageHandler(quill) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.click();
        input.onchange = function () {
            const file = input.files && input.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Too large', text: 'Image must be 5 MB or smaller.' });
                return;
            }
            const data = new FormData();
            data.append('image', file);
            data.append('csrf', CSRF);

            const range = quill.getSelection(true);
            // temporary placeholder while uploading
            quill.insertText(range.index, ' Uploading… ', { italic: true });

            fetch(INLINE_UPLOAD_URL, { method: 'POST', body: data })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                .then(function (res) {
                    quill.deleteText(range.index, ' Uploading… '.length);
                    if (res.ok && res.j.url) {
                        quill.insertEmbed(range.index, 'image', res.j.url, 'user');
                        quill.setSelection(range.index + 1);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Upload failed', text: (res.j && res.j.error) || 'Could not upload image.' });
                    }
                })
                .catch(function () {
                    quill.deleteText(range.index, ' Uploading… '.length);
                    Swal.fire({ icon: 'error', title: 'Upload failed', text: 'Network error.' });
                });
        };
    }

    // Initialise a Quill editor per language, syncing into the hidden textarea.
    const editors = {};
    ['en', 'fr', 'rw'].forEach(function (code) {
        const q = new Quill('#editor-' + code, {
            theme: 'snow',
            modules: { toolbar: {
                container: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'link', 'image'],
                    ['clean']
                ],
                handlers: {
                    image: function () { inlineImageHandler(this.quill); }
                }
            } }
        });
        const ta = document.getElementById('body-' + code);
        // Load existing HTML into the editor.
        if (ta.value) { q.root.innerHTML = ta.value; }
        q.on('text-change', function () {
            ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        });
        editors[code] = q;
    });

    // Make sure textareas are synced right before submit.
    document.getElementById('articleForm').addEventListener('submit', function () {
        ['en', 'fr', 'rw'].forEach(function (code) {
            const ta = document.getElementById('body-' + code);
            ta.value = editors[code].root.innerHTML === '<p><br></p>' ? '' : editors[code].root.innerHTML;
        });
    });

    function addLinkRow() {
        const wrap = document.getElementById('links-wrap');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 link-row';
        row.innerHTML =
            '<div class="col-md-4"><input type="text" name="link_label[]" class="form-control" placeholder="Label (optional)"></div>' +
            '<div class="col-md-7"><input type="url" name="link_url[]" class="form-control" placeholder="https://..."></div>' +
            '<div class="col-md-1 d-grid"><button type="button" class="btn btn-soft-danger" onclick="this.closest(\'.link-row\').remove()"><i class="ri-close-line"></i></button></div>';
        wrap.appendChild(row);
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById('img-preview');
                img.src = e.target.result;
                img.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleMedia() {
        const type = document.getElementById('media_type').value;
        document.getElementById('media-fields').classList.toggle('d-none', type === 'none');
    }

    // Remove a gallery image: hide it and add a remove_image[] marker.
    function removeGalleryItem(btn, id) {
        const wrap = document.getElementById('gallery-removals');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_image[]';
        input.value = id;
        wrap.appendChild(input);
        btn.closest('.gallery-item').remove();
    }

    // Drag-to-reorder the gallery (order is read from DOM order of existing_image_id[]).
    (function () {
        const list = document.getElementById('gallery-list');
        if (list && window.Sortable) {
            Sortable.create(list, { animation: 150, handle: 'img' });
        }
    })();
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
