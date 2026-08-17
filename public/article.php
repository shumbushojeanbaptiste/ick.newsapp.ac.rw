<?php
/**
 * UR KAMINUZA - Single article page.   article.php?slug=<slug>[&lang=rw|fr|en]
 * Reuses the theme's single-post structure + the shared header/footer.
 * Shows the article in the chosen language (falls back to primary), with its
 * featured image, media, body, external reference links, and a sidebar.
 */
require_once __DIR__ . '/include/content.php';

$slug    = trim($_GET['slug'] ?? '');
$article = $slug !== '' ? article_by_slug($slug) : null;

// ---- Handle a posted comment (goes to "pending" until an admin approves) ----
$commentNotice = null;
if ($article && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'comment') {
    $name = trim($_POST['c_name'] ?? '');
    $body = trim($_POST['c_body'] ?? '');
    $mail = trim($_POST['c_email'] ?? '');
    // Honeypot: bots fill the hidden "website" field; humans leave it empty.
    $isBot = trim($_POST['website'] ?? '') !== '';
    if ($name === '' || $body === '' || $isBot) {
        $commentNotice = ['type' => 'error', 'msg' => t('comment_error')];
    } else {
        $stmt = db()->prepare(
            'INSERT INTO article_comments (article_id, author_name, author_email, body, ip)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $article['id'],
            mb_substr($name, 0, 100),
            $mail !== '' ? mb_substr($mail, 0, 150) : null,
            mb_substr($body, 0, 5000),
            @inet_pton($_SERVER['REMOTE_ADDR'] ?? '') ?: null,
        ]);
        $commentNotice = ['type' => 'ok', 'msg' => t('comment_thanks')];
    }
}

// Language: requested ?lang, else the article's primary language.
$langs = $article ? a_languages($article) : [];
$lang  = $_GET['lang'] ?? '';
if (!isset($langs[$lang])) {
    $lang = $article['primary_lang'] ?? 'en';
}

// Highlight the right top-level menu item.
if ($article && $article['category_id']) {
    $GLOBALS['active_section'] = category_parent_slug((int) $article['category_id']);
}

require __DIR__ . '/header.php';
?>

<!--========== BEGIN #MAIN-SECTION ==========-->
<section id="main-section">
  <!--========== BEGIN .MODULE ==========-->
  <section class="module">
    <div class="container">

      <?php if (!$article): ?>
        <div class="module-title">
          <h3 class="title"><span class="bg-1"><?= e(t("article_not_found")) ?></span></h3>
          <h3 class="subtitle"><?= e(t("article_not_found_sub")) ?></h3>
        </div>
        <p><a href="." class="read-more">&laquo; <?= e(t("back_home")) ?></a></p>

      <?php else:
        $links   = article_links((int) $article['id']);
        $gallery = article_images((int) $article['id']);
        $media   = render_media($article);
        $aid                 = (int) $article['id'];
        [$likes, $dislikes]  = reaction_counts($aid);
        $mine                = my_reaction($aid);
        $comments            = article_comments($aid);
      ?>
        <div class="row no-gutter">
          <!--========== BEGIN .COL-MD-8 (the article) ==========-->
          <div class="col-md-8">
            <!--========== BEGIN .POST ==========-->
            <div class="post post-full clearfix">

              <!-- Headline sits ABOVE the photo -->
              <div class="entry-title kz-entry-head">
                <h1 class="entry-title"><?= e(a_title($article, $lang)) ?></h1>
              </div>

              <?php if ($media): ?>
                <div class="entry-media"><?= $media ?></div>
              <?php elseif (a_image($article)): ?>
                <div class="entry-media"><img src="<?= e(a_image($article)) ?>" alt="<?= e(a_title($article, $lang)) ?>" class="img-responsive"></div>
              <?php endif; ?>

              <div class="entry-main">
                <div class="post-meta-elements">
                  <div class="post-meta-author"> <i class="fa fa-user"></i> <?= e(t("by")) ?> <strong><?= e(a_byline($article)) ?></strong> </div>
                  <?php if (!empty($article['category_name'])): ?>
                    <div class="post-meta-cat"> <i class="fa fa-folder-o"></i> <a href="section?cat=<?= e($article['category_slug']) ?>"><?= e(cat_name($article)) ?></a> </div>
                  <?php endif; ?>
                  <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($article['published_at'] ?: $article['created_at']))) ?> </div>
                </div>

                <?php if (count($langs) > 1): ?>
                <!-- Language switcher (only languages this article has) -->
                <div class="post-tags" style="margin-bottom:14px">
                  <span class="post-tags_title"><?= e(t("read_in")) ?></span>
                  <?php foreach ($langs as $code => $label): ?>
                    <a class="post-tags_link<?= $code === $lang ? ' active' : '' ?>" href="article?slug=<?= e($article['slug']) ?>&lang=<?= e($code) ?>"><?= e($label) ?></a>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="entry-content">
                  <?php
                  // Body is editor HTML; render as-is. (Authored in the trusted Admin.)
                  $body = a_body($article, $lang);
                  echo $body !== '' ? $body : '<p class="text-muted">No content available in this language.</p>';
                  ?>
                </div>

                <?php if ($gallery): ?>
                <!-- Image gallery -->
                <div class="row no-gutter" style="margin-top:16px">
                  <?php foreach ($gallery as $g): ?>
                  <div class="col-xs-6 col-md-4">
                    <div class="item-image-3" style="margin:4px"><img class="img-responsive img-full" src="<?= e(upload_url($g['path'])) ?>" alt="<?= e($g['caption'] ?? '') ?>"></div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($links): ?>
                <!-- External references / related links -->
                <div class="post-tags">
                  <span class="post-tags_title"><?= e(t("references")) ?></span>
                  <?php foreach ($links as $ln): ?>
                    <a class="post-tags_link" href="<?= e($ln['url']) ?>" target="_blank" rel="noopener"><?= e($ln['label'] ?: $ln['url']) ?></a>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Bottom byline -->
                <div class="kz-byline-foot">
                  <i class="fa fa-pencil"></i>
                  <span><?= e(t("by")) ?> <strong><?= e(a_byline($article)) ?></strong>
                  &middot; <?= e(date('F j, Y', strtotime($article['published_at'] ?: $article['created_at']))) ?></span>
                </div>

                <!-- Like / dislike (instant) -->
                <div class="kz-reactions" id="kz-reactions" data-article="<?= $aid ?>">
                  <span class="kz-reactions__label"><?= e(t("was_this_useful")) ?></span>
                  <button type="button" class="kz-react kz-like<?= $mine === 'like' ? ' is-active' : '' ?>" data-reaction="like">
                    <i class="fa fa-thumbs-up"></i> <span class="kz-react__txt"><?= e(t("like")) ?></span>
                    <b class="kz-count" data-count="like"><?= $likes ?></b>
                  </button>
                  <button type="button" class="kz-react kz-dislike<?= $mine === 'dislike' ? ' is-active' : '' ?>" data-reaction="dislike">
                    <i class="fa fa-thumbs-down"></i> <span class="kz-react__txt"><?= e(t("dislike")) ?></span>
                    <b class="kz-count" data-count="dislike"><?= $dislikes ?></b>
                  </button>
                </div>

              </div>
            </div>
            <!--========== END .POST ==========-->

            <!--========== BEGIN COMMENTS ==========-->
            <div class="kz-comments">
              <div class="block-title-1"><h3><strong><?= e(t("comments")) ?></strong> <span class="kz-comments__n">(<?= count($comments) ?>)</span></h3></div>

              <?php if ($commentNotice): ?>
                <div class="kz-note kz-note--<?= $commentNotice['type'] === 'ok' ? 'ok' : 'err' ?>"><?= e($commentNotice['msg']) ?></div>
              <?php endif; ?>

              <!-- Comment list (approved only) -->
              <?php if ($comments): ?>
                <ul class="kz-comment-list">
                  <?php foreach ($comments as $c): ?>
                  <li class="kz-comment">
                    <div class="kz-comment__avatar"><?= e(mb_strtoupper(mb_substr($c['author_name'], 0, 1))) ?></div>
                    <div class="kz-comment__body">
                      <div class="kz-comment__head">
                        <span class="kz-comment__name"><?= e($c['author_name']) ?></span>
                        <span class="kz-comment__date"><?= e(date('M j, Y', strtotime($c['created_at']))) ?></span>
                      </div>
                      <p class="kz-comment__text"><?= nl2br(e($c['body'])) ?></p>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted kz-no-comments"><?= e(t("no_comments")) ?></p>
              <?php endif; ?>

              <!-- Comment form -->
              <div class="kz-comment-form">
                <h4><?= e(t("leave_comment")) ?></h4>
                <form method="post" action="article?slug=<?= e($article['slug']) ?>#kz-reactions">
                  <input type="hidden" name="do" value="comment">
                  <!-- honeypot (hidden from humans) -->
                  <input type="text" name="website" class="kz-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                  <div class="row">
                    <div class="col-sm-6"><input type="text" name="c_name" class="form-control" placeholder="<?= e(t("your_name")) ?>" required></div>
                    <div class="col-sm-6"><input type="email" name="c_email" class="form-control" placeholder="<?= e(t("your_email")) ?>"></div>
                  </div>
                  <textarea name="c_body" class="form-control" rows="4" placeholder="<?= e(t("your_comment")) ?>" required></textarea>
                  <button type="submit" class="kz-btn-submit"><?= e(t("post_comment")) ?></button>
                </form>
              </div>
            </div>
            <!--========== END COMMENTS ==========-->
          </div>
          <!--========== END .COL-MD-8 ==========-->

          <!--========== BEGIN .COL-MD-4 (sidebar) ==========-->
          <div class="col-md-4">
            <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?></div>
            <div class="block-title-1">
              <h3><a href="."><strong><?= e(t("related_stories")) ?></strong></a></h3>
            </div>
            <div class="sidebar-newsfeed">
              <div class="newsfeed-3">
                <ul>
                  <?php
                  // Other published stories from the same category.
                  $related = $article['category_id']
                      ? published_articles(6, null, (int) $article['category_id'])
                      : published_articles(6);
                  $shown = 0;
                  foreach ($related as $r):
                      if ($r['slug'] === $article['slug']) { continue; }   // skip current
                      if ($shown++ >= 5) { break; }
                  ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($r['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($r) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($r)) ?>"></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="article?slug=<?= e($r['slug']) ?>"><?= e(a_title($r)) ?></a></h4>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
          <!--========== END .COL-MD-4 ==========-->
        </div>
      <?php endif; ?>

    </div>
  </section>
  <!--========== END .MODULE ==========-->
</section>
<!--========== END #MAIN-SECTION ==========-->

<?php require __DIR__ . '/footer.php'; ?>
