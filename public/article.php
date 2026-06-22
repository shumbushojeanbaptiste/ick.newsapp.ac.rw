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
          <h3 class="title"><span class="bg-1">Article not found</span></h3>
          <h3 class="subtitle">This story may have been moved or unpublished.</h3>
        </div>
        <p><a href="index.php" class="read-more">&laquo; Back to home</a></p>

      <?php else:
        $links   = article_links((int) $article['id']);
        $gallery = article_images((int) $article['id']);
        $media   = render_media($article);
      ?>
        <div class="row no-gutter">
          <!--========== BEGIN .COL-MD-8 (the article) ==========-->
          <div class="col-md-8">
            <!--========== BEGIN .POST ==========-->
            <div class="post post-full clearfix">

              <?php if ($media): ?>
                <div class="entry-media"><?= $media ?></div>
              <?php elseif (a_image($article)): ?>
                <div class="entry-media"><img src="<?= e(a_image($article)) ?>" alt="<?= e(a_title($article, $lang)) ?>" class="img-responsive"></div>
              <?php endif; ?>

              <div class="entry-main">
                <div class="entry-title">
                  <h4 class="entry-title"><?= e(a_title($article, $lang)) ?></h4>
                </div>

                <div class="post-meta-elements">
                  <?php if (!empty($article['author_name'])): ?>
                    <div class="post-meta-author"> <i class="fa fa-user"></i> <?= e($article['author_name']) ?> </div>
                  <?php endif; ?>
                  <?php if (!empty($article['category_name'])): ?>
                    <div class="post-meta-cat"> <i class="fa fa-folder-o"></i> <a href="section.php?cat=<?= e($article['category_slug']) ?>"><?= e($article['category_name']) ?></a> </div>
                  <?php endif; ?>
                  <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($article['published_at'] ?: $article['created_at']))) ?> </div>
                </div>

                <?php if (count($langs) > 1): ?>
                <!-- Language switcher (only languages this article has) -->
                <div class="post-tags" style="margin-bottom:14px">
                  <span class="post-tags_title">Read in:</span>
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
                  <span class="post-tags_title">References:</span>
                  <?php foreach ($links as $ln): ?>
                    <a class="post-tags_link" href="<?= e($ln['url']) ?>" target="_blank" rel="noopener"><?= e($ln['label'] ?: $ln['url']) ?></a>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>

              </div>
            </div>
            <!--========== END .POST ==========-->
          </div>
          <!--========== END .COL-MD-8 ==========-->

          <!--========== BEGIN .COL-MD-4 (sidebar) ==========-->
          <div class="col-md-4">
            <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?></div>
            <div class="block-title-1">
              <h3><a href="index.php"><strong>Related</strong> Stories</a></h3>
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
