<?php
/**
 * UR KAMINUZA - About Us page.
 * Uses the theme's about-us.html template structure (title-style01 + the four
 * "Who/How/What/Management" cards + Editorial Board), but all content comes
 * from Admin > Settings. The demo TV-schedule sidebar + team scrollspy are
 * replaced with useful, real content (Latest News + Editor's Picks).
 */
require_once __DIR__ . '/include/content.php';

$GLOBALS['active_section'] = 'about';

$intro     = setting('about');
$editorial = setting('about_editorial');

// The four cards: [setting key, image key, heading].
$cards = [
    ['about_who',        'about_who_image',        t('about_who')],
    ['about_how',        'about_how_image',        t('about_how')],
    ['about_what',       'about_what_image',       t('about_what')],
    ['about_management', 'about_management_image', t('about_management')],
];
$cardFallback = ['img/index_800x400-image17.jpg', 'img/watch_800x400-image02.jpg', 'img/watch_800x400-image03.jpg', 'img/news_800x400-image04.jpg'];

require __DIR__ . '/header.php';
?>

<!--========== BEGIN #MAIN-SECTION ==========-->
<section id="main-section">
  <section class="module-top">
    <div class="container">

      <div class="row no-gutter">
        <!--========== BEGIN .COL-MD-8 ==========-->
        <div class="col-md-8">
          <div class="title-style01">
            <h3><strong><?= e(t("about_us")) ?>:</strong> <?= e(t("about_who")) ?> | <?= e(t("about_how")) ?> | <?= e(t("about_what")) ?> | <?= e(t("about_management")) ?></h3>
          </div>

          <?php if ($intro): ?>
          <div class="content" style="margin-bottom:18px;">
            <p><?= nl2br(e($intro)) ?></p>
          </div>
          <?php endif; ?>

          <!-- Four cards (theme .news/.item layout), content from Settings -->
          <div class="row">
            <?php foreach ($cards as $i => [$key, $imgKey, $heading]):
                $text = setting($key);
                // Uploaded images are saved relative to the Admin folder, so run
                // them through upload_url() (img/... theme fallbacks pass through).
                $img  = upload_url(setting($imgKey)) ?: $cardFallback[$i % 4];
                if ($i % 2 === 0): ?>
            <div class="col-xs-12 col-sm-6 col-md-6">
              <div class="news">
            <?php endif; ?>
                <div class="item">
                  <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="<?= e($img) ?>" alt="<?= e($heading) ?>"></a></div>
                  <div class="item-content">
                    <div class="title-left title-style04 underline04">
                      <h3><?= e($heading) ?></h3>
                    </div>
                    <?php if ($text): ?><p><?= nl2br(e($text)) ?></p><?php endif; ?>
                  </div>
                </div>
            <?php if ($i % 2 === 1): ?>
              </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>

          <!-- Editorial Board -->
          <?php if ($editorial): ?>
          <div class="row no-gutter">
            <div class="title-style02">
              <h3><a href="#"><strong><?= e(site_name()) ?></strong> <?= e(t("editorial_board")) ?></a></h3>
            </div>
            <div class="content">
              <p><?= nl2br(e($editorial)) ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <!--========== END .COL-MD-8 ==========-->

        <!--========== BEGIN .COL-MD-4 (sidebar: useful content) ==========-->
        <div class="col-md-4">
          <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?></div>

          <div class="block-title-1">
            <h3><a href="."><strong><?= e(t("latest_news")) ?></strong></a></h3>
          </div>
          <div class="sidebar-newsfeed">
            <div class="newsfeed-3">
              <ul>
                <?php foreach (published_articles(5) as $a): ?>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
                    <div class="item-content">
                      <h4 class="ellipsis"><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h4>
                    </div>
                  </div>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="block-title-1" style="margin-top:24px;">
            <h3><a href="."><strong><?= e(t("editors_picks")) ?></strong></a></h3>
          </div>
          <div class="sidebar-newsfeed">
            <div class="newsfeed-3">
              <ul>
                <?php foreach (published_articles(4, 'editor_pick') ?: published_articles(4) as $pick): ?>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="article?slug=<?= e($pick['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($pick) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($pick)) ?>"></a></div>
                    <div class="item-content">
                      <h4 class="ellipsis"><a href="article?slug=<?= e($pick['slug']) ?>"><?= e(a_title($pick)) ?></a></h4>
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

    </div>
  </section>
</section>
<!--========== END #MAIN-SECTION ==========-->

<?php require __DIR__ . '/footer.php'; ?>
