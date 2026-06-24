<?php
require_once __DIR__ . '/../config/articles.php';
require_can('view_articles');

$basePath   = '../';
$activeMenu = 'articles';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare(
    'SELECT a.*, c.name AS category_name, u.name AS author_name, r.label AS author_role, rs.name AS radio_name, rs.stream_url
       FROM articles a
       LEFT JOIN categories c ON c.id = a.category_id
       LEFT JOIN users u      ON u.id = a.author_id
       LEFT JOIN roles r      ON r.id = u.role_id
       LEFT JOIN radio_streams rs ON rs.id = a.radio_stream_id
      WHERE a.id = ? LIMIT 1'
);
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    set_flash('danger', 'Article not found.');
    redirect('articles/index.php');
}

// Languages this article actually has (title present), primary first.
$available = article_langs($article);
$primary   = $article['primary_lang'];
if (in_array($primary, $available, true)) {
    $available = array_merge([$primary], array_values(array_diff($available, [$primary])));
}
if (!$available) {
    $available = [$primary]; // fallback
}

// Gallery + links
$gs = db()->prepare('SELECT path, caption FROM article_images WHERE article_id = ? ORDER BY sort_order, id');
$gs->execute([$id]);
$gallery = $gs->fetchAll();

$ls = db()->prepare('SELECT label, url FROM article_links WHERE article_id = ? ORDER BY id');
$ls->execute([$id]);
$links = $ls->fetchAll();

$mediaHtml = render_article_media($article);
$pageTitle = article_title($article);

$pageStyles = '<style>.article-body img{max-width:100%;height:auto;border-radius:.25rem;margin:.75rem 0;}'
    . '.article-body{line-height:1.7;font-size:1.02rem;}</style>';

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Read Article</h4>
                </div>
                <div class="col-auto">
                    <a href="articles/index" class="btn btn-light"><i class="ri-arrow-left-line align-bottom"></i> Back to list</a>
                    <?php if (can_on_article('edit', $article)): ?>
                        <a href="articles/edit?id=<?= (int) $article['id'] ?>" class="btn btn-primary"><i class="ri-pencil-line align-bottom"></i> Edit</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="card">
                        <?php if ($article['featured_image']): ?>
                            <img src="<?= e($article['featured_image']) ?>" alt="" class="card-img-top object-fit-cover" style="max-height:380px">
                        <?php endif; ?>
                        <div class="card-body">

                            <!-- Meta -->
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 text-muted">
                                <span class="badge <?= status_badge_class($article['status']) ?> text-uppercase"><?= e($article['status']) ?></span>
                                <?php if ($article['category_name']): ?>
                                    <span><i class="ri-price-tag-3-line"></i> <?= e($article['category_name']) ?></span>
                                <?php endif; ?>
                                <span><i class="ri-user-line"></i> By <strong><?= e($article['author_name'] ?? 'Unknown') ?></strong><?= $article['author_role'] ? ' (' . e($article['author_role']) . ')' : '' ?></span>
                                <span><i class="ri-calendar-line"></i> <?= e(date('d M Y', strtotime($article['published_at'] ?: $article['created_at']))) ?></span>
                            </div>

                            <!-- Language toggle (only languages that exist) -->
                            <?php if (count($available) > 1): ?>
                                <div class="btn-group mb-3" role="group" aria-label="Language">
                                    <?php foreach ($available as $i => $lc): ?>
                                        <button type="button" class="btn btn-sm btn-soft-primary lang-btn <?= $i === 0 ? 'active' : '' ?>"
                                                data-lang="<?= $lc ?>" onclick="switchLang('<?= $lc ?>', this)">
                                            <?= e(lang_label($lc)) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Media -->
                            <?php if ($mediaHtml): ?>
                                <div class="mb-3"><?= $mediaHtml ?></div>
                            <?php endif; ?>

                            <!-- Per-language content blocks -->
                            <?php foreach ($available as $i => $lc): ?>
                                <div class="lang-content" data-lang="<?= $lc ?>" <?= $i === 0 ? '' : 'style="display:none"' ?>>
                                    <h2 class="mb-3"><?= e($article["title_$lc"]) ?></h2>
                                    <?php if (!empty($article["excerpt_$lc"])): ?>
                                        <p class="lead text-muted"><?= e($article["excerpt_$lc"]) ?></p>
                                    <?php endif; ?>
                                    <div class="article-body">
                                        <?= $article["body_$lc"] ?: '<p class="text-muted"><em>No content in this language.</em></p>' ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Embedded radio -->
                            <?php if (!empty($article['radio_name']) && !empty($article['stream_url'])): ?>
                                <div class="mt-4">
                                    <h6 class="text-uppercase text-muted"><i class="ri-radio-line"></i> Listen: <?= e($article['radio_name']) ?></h6>
                                    <audio controls class="w-100"><source src="<?= e($article['stream_url']) ?>"></audio>
                                </div>
                            <?php endif; ?>

                            <!-- Gallery -->
                            <?php if ($gallery): ?>
                                <div class="mt-4">
                                    <h6 class="text-uppercase text-muted mb-2">Gallery</h6>
                                    <div class="row g-2">
                                        <?php foreach ($gallery as $g): ?>
                                            <div class="col-6 col-md-4">
                                                <a href="<?= e($g['path']) ?>" target="_blank">
                                                    <img src="<?= e($g['path']) ?>" alt="<?= e($g['caption'] ?? '') ?>" class="img-fluid rounded" style="height:140px;width:100%;object-fit:cover">
                                                </a>
                                                <?php if (!empty($g['caption'])): ?><small class="text-muted d-block"><?= e($g['caption']) ?></small><?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Related links -->
                            <?php if ($links): ?>
                                <div class="mt-4">
                                    <h6 class="text-uppercase text-muted mb-2">Related Links & References</h6>
                                    <ul class="mb-0">
                                        <?php foreach ($links as $ln): ?>
                                            <li><a href="<?= e($ln['url']) ?>" target="_blank" rel="noopener"><?= e($ln['label'] ?: $ln['url']) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>

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
<script>
    function switchLang(lang, btn) {
        document.querySelectorAll('.lang-content').forEach(function (el) {
            el.style.display = el.getAttribute('data-lang') === lang ? '' : 'none';
        });
        document.querySelectorAll('.lang-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
