<?php
/**
 * UR KAMINUZA - Search results page.   search.php?q=<query>
 * Finds published articles in the current language whose title/excerpt/body
 * match the query, shown in the section-page layout.
 */
require_once __DIR__ . '/include/content.php';

$q = trim($_GET['q'] ?? '');

// Pagination.
$perPage = 12;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

[$articles, $total] = search_articles($q, $perPage, $offset);
$totalPages = (int) ceil($total / $perPage);

require __DIR__ . '/header.php';
?>

<!--========== BEGIN #MAIN-SECTION ==========-->
<section id="main-section">
  <!--========== BEGIN .MODULE ==========-->
  <section class="module">
    <div class="container">

      <div class="module-title">
        <h3 class="title"><span class="bg-1"><?= e(t("search_results")) ?></span></h3>
        <?php if ($q !== ''): ?>
          <h3 class="subtitle"><?= e(t("search_for")) ?> “<?= e($q) ?>” (<?= (int) $total ?>)</h3>
        <?php endif; ?>
      </div>

      <div class="row no-gutter">
        <!--========== BEGIN .COL-MD-8 ==========-->
        <div class="col-md-8">
          <div class="article">
            <?php if ($q === ''): ?>
              <p class="text-muted" style="padding:20px 0;"><?= e(t('search_empty')) ?></p>
            <?php elseif (!$articles): ?>
              <p class="text-muted" style="padding:20px 0;"><?= e(t('search_none')) ?></p>
            <?php else: foreach ($articles as $a): ?>
            <div class="entry-block-small">
              <div class="entry-image"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/news_800x400-image05.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
              <div class="entry-content">
                <h4><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?> <span class="day"><?= e(date('M j, Y', strtotime($a['published_at'] ?: $a['created_at']))) ?></span></a></h4>
                <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                <div><a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more"><?= e(cat_name($a) ?: 'News') ?></span></a></div>
              </div>
            </div>
            <?php endforeach; endif; ?>
          </div>

          <?php if ($totalPages > 1): ?>
          <ul class="pagination">
            <?php $qs = 'q=' . urlencode($q); ?>
            <?php if ($page > 1): ?>
              <li><a href="search?<?= $qs ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
            <?php endif; ?>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <li class="<?= $p === $page ? 'active' : '' ?>"><a href="search?<?= $qs ?>&page=<?= $p ?>"><?= $p ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
              <li><a href="search?<?= $qs ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
            <?php endif; ?>
          </ul>
          <?php endif; ?>
        </div>
        <!--========== END .COL-MD-8 ==========-->

        <!--========== BEGIN .COL-MD-4 (sidebar) ==========-->
        <div class="col-md-4">
          <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?></div>
          <div class="block-title-1">
            <h3><a href="index"><strong><?= e(t("editors_picks")) ?></strong></a></h3>
          </div>
          <div class="sidebar-newsfeed">
            <div class="newsfeed-3">
              <ul>
                <?php foreach (published_articles(5, 'editor_pick') ?: published_articles(5) as $pick): ?>
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
  <!--========== END .MODULE ==========-->
</section>
<!--========== END #MAIN-SECTION ==========-->

<?php require __DIR__ . '/footer.php'; ?>
