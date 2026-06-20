<?php
/**
 * UR KAMINUZA - Public section (category) listing page.
 * One reusable template for EVERY category/sub-category in the menu:
 *   section.php?cat=<slug>
 * Shows the category's published articles (including its sub-categories),
 * using the theme's existing content classes + the shared header/footer.
 */
require_once __DIR__ . '/include/content.php';

$slug = trim($_GET['cat'] ?? '');
$category = $slug !== '' ? category_by_slug($slug) : null;

// Highlight the right top-level menu item (the section or its parent).
if ($category) {
    $GLOBALS['active_section'] = $category['parent_id']
        ? (category_parent_slug((int) $category['id']) ?? $category['slug'])
        : $category['slug'];
}

// Pagination.
$perPage = 12;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

[$articles, $total] = $category
    ? published_in_category((int) $category['id'], $perPage, $offset)
    : [[], 0];
$totalPages = (int) ceil($total / $perPage);

require __DIR__ . '/header.php';
?>

<!--========== BEGIN #MAIN-SECTION ==========-->
<section id="main-section">
  <!--========== BEGIN .MODULE ==========-->
  <section class="module">
    <div class="container">

      <?php if (!$category): ?>
        <div class="module-title">
          <h3 class="title"><span class="bg-1">Section not found</span></h3>
          <h3 class="subtitle">The page you’re looking for doesn’t exist.</h3>
        </div>
        <p><a href="index.php" class="read-more">&laquo; Back to home</a></p>

      <?php else: ?>
        <div class="module-title">
          <h3 class="title"><span class="bg-1"><?= e($category['name']) ?></span></h3>
          <h3 class="subtitle">Latest in <?= e($category['name']) ?></h3>
        </div>

        <div class="row no-gutter">
          <!--========== BEGIN .COL-MD-8 (article list, news.html .article style) ==========-->
          <div class="col-md-8">
            <!--========== BEGIN .ARTICLE ==========-->
            <div class="article">
              <?php if (!$articles): ?>
                <p class="text-muted" style="padding:20px 0;">No articles published in this section yet.</p>
              <?php else: foreach ($articles as $a): ?>
              <div class="entry-block-small">
                <div class="entry-image"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/news_800x400-image05.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
                <div class="entry-content">
                  <h4><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?> <span class="day"><?= e(date('M j, Y', strtotime($a['published_at'] ?: $a['created_at']))) ?></span></a></h4>
                  <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                  <div><a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more"><?= e($a['category_name'] ?? $category['name']) ?></span></a></div>
                </div>
              </div>
              <?php endforeach; endif; ?>
            </div>
            <!--========== END .ARTICLE ==========-->

            <?php if ($totalPages > 1): ?>
            <!--========== BEGIN .PAGINATION ==========-->
            <ul class="pagination">
              <?php if ($page > 1): ?>
                <li><a href="section.php?cat=<?= e($slug) ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
              <?php endif; ?>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="<?= $p === $page ? 'active' : '' ?>"><a href="section.php?cat=<?= e($slug) ?>&page=<?= $p ?>"><?= $p ?></a></li>
              <?php endfor; ?>
              <?php if ($page < $totalPages): ?>
                <li><a href="section.php?cat=<?= e($slug) ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
              <?php endif; ?>
            </ul>
            <?php endif; ?>
          </div>
          <!--========== END .COL-MD-8 ==========-->

          <!--========== BEGIN .COL-MD-4 (sidebar) ==========-->
          <div class="col-md-4">
            <!-- Advert (managed from Admin settings) -->
            <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?></div>
            <!-- Editor's picks feed -->
            <div class="block-title-1">
              <h3><a href="index.php"><strong>Editor's</strong> Picks</a></h3>
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
      <?php endif; ?>

    </div>
  </section>
  <!--========== END .MODULE ==========-->
</section>
<!--========== END #MAIN-SECTION ==========-->

<?php require __DIR__ . '/footer.php'; ?>
