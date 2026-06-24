<?php include"header.php"?>
  <!--========== BEGIN #MAIN-SECTION ==========-->
  <section id="main-section">
    <!-- ===== Hero section removed for now (full version in index_backup.php) ===== -->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module"> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN .BREAKING-NEWS ==========--> 
        <!-- Begin .outer -->
        <div class="outer">
          <div class="breaking-ribbon">
            <h4><?= e(t("breaking_news")) ?></h4>
          </div>
          <!-- Begin .newsticker -->
          <div class="newsticker">
            <ul>
              <?php $breaking = published_articles(8, 'breaking'); ?>
              <?php if ($breaking): foreach ($breaking as $b): ?>
              <li>
                <h4><span class="category"><?= e(cat_name($b) ?: 'News') ?>:</span><a href="article?slug=<?= e($b['slug']) ?>"> <?= e(a_title($b)) ?></a></h4>
              </li>
              <?php endforeach; else: ?>
              <li><h4><span class="category">News:</span><a href="#"> No breaking news right now.</a></h4></li>
              <?php endif; ?>
            </ul>
            <div class="navi">
              <button class="up"><i class="fa fa-caret-left"></i></button>
              <button class="down"><i class="fa fa-caret-right"></i></button>
            </div>
          </div>
          <!-- End .newsticker --> 
        </div>
        <!-- End .outer --> 
        <!--========== END .BREAKING-NEWS ==========--> 
        <!--========== BEGIN #NEWS-SLIDER ==========-->
        <div id="news-slider" class="owl-carousel">
          <?php
          // Featured articles, shown 4 per slide. Position classes + colour tags
          // cycle to match the theme's original styling.
          $featured = published_articles(12, 'featured');
          $positions = ['first', 'second', 'third', 'fourth'];
          $tags = ['bg-1', 'bg-6', 'bg-4', 'bg-2'];
          if ($featured):
              foreach (array_chunk($featured, 4) as $slide): ?>
          <div class="news-slide">
            <?php foreach ($slide as $i => $a): ?>
            <div class="news-slider-layer <?= $positions[$i % 4] ?>"> <a href="article?slug=<?= e($a['slug']) ?>" target="_blank">
              <div class="content"> <span class="category-tag <?= $tags[$i % 4] ?>"><?= e(cat_name($a) ?: 'News') ?></span>
                <p><?= e(a_title($a)) ?></p>
              </div>
              <img src="<?= e(a_image($a) ?: 'img/index_slider-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"> </a> </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; else: ?>
          <div class="news-slide"><div class="news-slider-layer first"><a href="#"><div class="content"><span class="category-tag bg-1">News</span><p>No featured stories yet.</p></div><img src="img/index_slider-image01.jpg" alt=""></a></div></div>
          <?php endif; ?>
        </div>
        <!--========== END .NEWS-SLIDER ==========--> 
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE ==========-->
    <?php
    // Latest published articles, split into two columns of two (same 2x2 layout
    // as the original theme block). Label colours cycle like the demo did.
    $latest = published_articles(4);
    $latestCols = array_chunk($latest, 2);
    $labelClass = ['label-1', 'label-3', 'label-5', 'label-2'];
    $li = 0;
    ?>
    <section class="module highlight">
      <div class="container">
        <div class="module-title">
          <h3 class="title"><span class="bg-1"><?= e(t("latest_news")) ?></span></h3>
          <h3 class="subtitle"><?= e(t("newest_stories")) ?> <?= e(site_name()) ?></h3>
        </div>
        <!--========== BEGIN .ROW ==========-->
        <div class="row no-gutter">
          <?php if ($latest): foreach ($latestCols as $col): ?>
          <!--========== BEGIN .COL-MD-6 ==========-->
          <div class="col-sm-6 col-md-6">
            <!--========== BEGIN .NEWS ==========-->
            <div class="news">
              <?php foreach ($col as $a): $cls = $labelClass[$li % 4]; $li++; ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a><span><a class="<?= $cls ?>" href="section?cat=<?= e($a['category_slug'] ?? '') ?>"><?= e(cat_name($a) ?: 'News') ?></a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                  </div>
                  <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                  <div><a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more"><?= e(t("read_more")) ?></span></a></div>
                </div>
              </div>
              <!-- End .item -->
              <?php endforeach; ?>
            </div>
            <!--========== END .NEWS ==========-->
          </div>
          <!--========== END .COL-MD-6 ==========-->
          <?php endforeach; endif; ?>
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========-->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module">
      <div class="container">
        <div class="row no-gutter"> 
          <!--========== BEGIN .COL-MD-8 ==========-->
          <div class="col-md-8"> 
            <!--========== BEGIN .NEWS ==========-->
            <div class="news">
              <div class="module-title">
                <h3 class="title"><span class="bg-11"><?= e(t("national_news")) ?></span></h3>
                <h3 class="subtitle"><?= e(t("latest_in_detail")) ?></h3>
              </div>
              <?php
              // Next batch of published articles (skip the 4 already shown above).
              $national = published_articles(8);
              $national = array_slice($national, 4, 4);
              $natLabels = ['label-2', 'label-1', 'label-5', 'label-6'];
              foreach ($national as $ni => $a): ?>
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image05.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a><span><a class="<?= $natLabels[$ni % 4] ?>" href="section?cat=<?= e($a['category_slug'] ?? '') ?>"><?= e(cat_name($a) ?: 'News') ?></a></span> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                  </div>
                  <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                  <div> <a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more"><?= e(t("read_more")) ?></span></a> </div>
                </div>
              </div>
              <!-- End .item-->
              <?php endforeach; ?>
            </div>
            <!--========== End .NEWS ==========--> 
          </div>
          <!--========== End .COL-MD-8 ==========--> 
          <!--========== BEGIN .COL-MD-4 ==========-->
          <div class="col-md-4"> 
            <!-- Begin .sidebar-add-place -->
            <div class="sidebar-add-place"><?= ad_slot('sidebar', 'img/banner_400x270.jpg') ?> </div>
            <!-- End .sidebar-add-place --> 
            <!-- Begin .block-title-1 -->
            <div class="block-title-1">
              <h3><a href="index"><strong><?= e(site_name()) ?></strong> Feed</a></h3>
            </div>
            <!-- End .block-title-1 -->
            <!--========== BEGIN .SIDEBAR-NEWSFEED ==========-->
            <div class="sidebar-newsfeed">
              <!-- Begin .newsfeed -->
              <div class="newsfeed-3">
                <ul>
                  <?php
                  // Recent published stories for the side feed (kept separate from
                  // the main grids above by taking a fresh recent set).
                  $feed = published_articles(6);
                  foreach ($feed as $a): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h4>
                        <p class="ellipsis"><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_excerpt($a)) ?></a></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <!-- End .newsfeed --> 
            </div>
            <!--========== END .SIDEBAR-NEWSFEED ==========--> 
          </div>
          <!--========== END .COL-MD-4 ==========--> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========-->
    <!-- ===== Other homepage sections removed for now (full version in index_backup.php) ===== -->
  </section>
  <!--========== END #MAIN-SECTION ==========--> 
<?php include"footer.php"?>