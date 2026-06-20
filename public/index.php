<?php include"header.php"?>
  <!--========== BEGIN #MAIN-SECTION ==========-->
  <section id="main-section"> 
    <!-- ========== BEGIN PARALLAX ========== -->
    <?php
    // Hero slides = top 3 featured stories (fall back to latest published).
    $heroSlides = published_articles(3, 'featured');
    if (!$heroSlides) { $heroSlides = published_articles(3); }
    ?>
    <div id="parallax-section">
      <?php if ($heroSlides): ?>
      <div id="hero-slider" class="owl-carousel">
        <?php foreach ($heroSlides as $hero):
            $heroImg = a_image($hero);
            $heroStyle = $heroImg ? ' style="background:url(' . e($heroImg) . ') center / cover no-repeat;"' : '';
        ?>
        <div class="image1 img-overlay1 hero-slide"<?= $heroStyle ?>>
          <div class="container">
            <div class="caption text-center">
                <div class="color-white text-center weight-300 medium-caption"><?= e($hero['category_name'] ?: (site_tagline() ?: 'Top story')) ?></div>
                <a href="article?slug=<?= e($hero['slug']) ?>" class="color-white">
                  <div class="color-white text-center weight-800 large-caption"><?= e(a_title($hero)) ?></div>
                </a>
                <h5><?= e(a_excerpt($hero)) ?></h5>
              </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="image1 img-overlay1">
        <div class="container">
          <div class="caption text-center">
              <div class="color-white text-center weight-300 medium-caption"><?= e(site_tagline()) ?></div>
              <div class="color-white text-center weight-800 large-caption"><?= e(site_name()) ?></div>
              <h5>Your trusted source for University of Rwanda news and stories.</h5>
            </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <!-- ========== END PARALLAX ========== -->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module"> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN .BREAKING-NEWS ==========--> 
        <!-- Begin .outer -->
        <div class="outer">
          <div class="breaking-ribbon">
            <h4>Breaking News</h4>
          </div>
          <!-- Begin .newsticker -->
          <div class="newsticker">
            <ul>
              <?php $breaking = published_articles(8, 'breaking'); ?>
              <?php if ($breaking): foreach ($breaking as $b): ?>
              <li>
                <h4><span class="category"><?= e($b['category_name'] ?? 'News') ?>:</span><a href="article?slug=<?= e($b['slug']) ?>"> <?= e(a_title($b)) ?></a></h4>
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
              <div class="content"> <span class="category-tag <?= $tags[$i % 4] ?>"><?= e($a['category_name'] ?? 'News') ?></span>
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
          <h3 class="title"><span class="bg-1">Latest News</span></h3>
          <h3 class="subtitle">The newest stories from <?= e(site_name()) ?></h3>
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
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a><span><a class="<?= $cls ?>" href="section.php?cat=<?= e($a['category_slug'] ?? '') ?>"><?= e($a['category_name'] ?? 'News') ?></a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                  </div>
                  <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                  <div><a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more">Read more</span></a></div>
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
                <h3 class="title"><span class="bg-11">National News</span></h3>
                <h3 class="subtitle">Latest news in detail</h3>
              </div>
              <?php
              // Next batch of published articles (skip the 4 already shown above).
              $national = published_articles(8);
              $national = array_slice($national, 4, 4);
              $natLabels = ['label-2', 'label-1', 'label-5', 'label-6'];
              foreach ($national as $ni => $a): ?>
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image05.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a><span><a class="<?= $natLabels[$ni % 4] ?>" href="section.php?cat=<?= e($a['category_slug'] ?? '') ?>"><?= e($a['category_name'] ?? 'News') ?></a></span> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                  </div>
                  <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                  <div> <a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more">Read more</span></a> </div>
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
              <h3><a href="index.php"><strong><?= e(site_name()) ?></strong> Feed</a></h3>
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
    <!--========== BEGIN .MODULE (TV schedule promo hidden: demo content) ==========-->
    <?php if (false): ?>
    <section class="module dark">
      <div class="container">
        <div class="show-info">
          <h4 class="schedule-logo bg-1"><a href="tv-schedule.html" target="_blank">TV Schedule</a></h4>
          <div class="show-title">
            <h2>News Insight</h2>
            <h3>Hosted by Neal Bailey</h3>
          </div>
          <h4><a class="show-info-button bg-1" href="#"> Watch the final presidential debate, moderated by Neal Bailey, Tonight at 10 </a></h4>
          <div class="figure"><img src="img/schedule_figure.png" alt=""></div>
        </div>
        <div class="schedule-squares"><span class="square2"></span><span class="square3"></span><span class="square4"></span> <span class="square5"></span><span class="square6"></span><span class="square7"></span><span class="square8"></span><span class="square9"></span><span class="square10"></span><span class="square11"></span></div>
      </div>
    </section>
    <?php endif; ?>
    <!--========== END .MODULE ==========-->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module highlight">
      <div class="container">
        <div class="row no-gutter"> 
          <!--========== BEGIN .COL-MD-8 ==========-->
          <div class="col-md-8">
            <div class="module-title">
              <h3 class="title"><span class="bg-1">More News</span></h3>
              <h3 class="subtitle">Stories from across the university</h3>
            </div>
            <!--========== BEGIN .ROW ==========-->
            <div class="row no-gutter"> 
              <!--========== BEGIN .COL-MD-3 ==========-->
              <div class="col-xs-12 col-sm-3 col-md-3"> 
                <!-- Begin .list list-mark-1 (all site sections + sub-sections) -->
                <ul class="list list-mark-1">
                  <li><a href="index.php">Home</a></li>
                  <?php foreach (nav_top_sections() as $navSec): ?>
                    <li><a href="section.php?cat=<?= e($navSec['slug']) ?>"><?= e($navSec['name']) ?></a></li>
                  <?php endforeach; ?>
                </ul>
                <!-- End .list list-mark-1 -->
              </div>
              <!--========== END .COL-MD-3 ==========--> 
              <!--========== BEGIN .COL-MD-9 ==========-->
              <div class="col-xs-12 col-sm-9 col-md-9"> 
                <!--========== BEGIN .NEWS ==========-->
                <div class="news">
                  <?php
                  // A MIX of stories across all categories (offset so it differs
                  // from the top blocks), so this section shows varied content.
                  $localList = array_slice(published_articles(20), 10, 5);
                  if (!$localList) { $localList = published_articles(5); }
                  $localLabels = ['label-1', 'label-4', 'label-9', 'label-3', 'label-6'];
                  foreach ($localList as $li => $a): ?>
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image09.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a><span><a class="<?= $localLabels[$li % 5] ?>" href="section.php?cat=<?= e($a['category_slug'] ?? '') ?>"><?= e($a['category_name'] ?? 'News') ?></a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                      </div>
                      <p><a href="article?slug=<?= e($a['slug']) ?>" class="external-link"><?= e(a_excerpt($a)) ?></a></p>
                      <div> <a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more">Read more</span></a> </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <!--========== END .NEWS ==========-->
              </div>
              <!--========== END .COL-MD-9 ==========--> 
            </div>
            <!--========== END .ROW ==========--> 
          </div>
          <!--========== END .COL-MD-8 ==========--> 
          <!--========== BEGIN COL-MD-4 ==========-->
          <div class="col-md-4"> 
            <!-- Begin .title-style02 -->
            <div class="title-style02">
              <h3><a href="#">Recent Posts</a></h3>
            </div>
            <!-- End .title-style02 --> 
            <!--========== BEGIN .SIDEBAR-SCROLL ==========-->
            <div class="sidebar-scroll">
              <?php
              // "Recent Posts" feed in the theme's varied rhythm: a lead big-image
              // story, then a mix of with-image and text-only scroll items, with a
              // second big image partway down. All dynamic, mixed categories.
              $rpAll  = published_articles(13);
              $rpLead = $rpAll[0] ?? null;
              $rpRest = array_slice($rpAll, 1);
              ?>
              <?php if ($rpLead): ?>
              <!-- Lead post (big image) -->
              <div class="item">
                <div class="item-image-full"><a class="img-link" href="article?slug=<?= e($rpLead['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($rpLead) ?: 'img/index_800x400-image02.jpg') ?>" alt="<?= e(a_title($rpLead)) ?>"></a></div>
              </div>
              <div class="item">
                <div class="item-content-1">
                  <h3><a href="article?slug=<?= e($rpLead['slug']) ?>"><?= e(a_title($rpLead)) ?></a></h3>
                </div>
              </div>
              <?php endif; ?>
              <?php foreach ($rpRest as $idx => $rp):
                  $withImage = ($idx % 3 === 0);   // every 3rd item shows an image
              ?>
              <div class="scroll-item">
                <div class="item">
                  <?php if ($withImage): ?>
                  <div class="item-image"><a class="img-link" href="article?slug=<?= e($rp['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($rp) ?: 'img/index_800x400-image40.jpg') ?>" alt="<?= e(a_title($rp)) ?>"></a></div>
                  <div class="item-content">
                    <p><a href="article?slug=<?= e($rp['slug']) ?>"><i class="fa fa-clock-o"></i> <span class="day"><?= e(date('M j', strtotime($rp['published_at'] ?: $rp['created_at']))) ?></span> <br>
                      <?= e(a_title($rp)) ?></a></p>
                  </div>
                  <?php else: ?>
                  <div class="item-content-1">
                    <p><a href="article?slug=<?= e($rp['slug']) ?>"><i class="fa fa-clock-o"></i> <span class="day"><?= e(date('M j', strtotime($rp['published_at'] ?: $rp['created_at']))) ?></span> <br>
                      <?= e(a_title($rp)) ?></a></p>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <!--========== END .SIDEBAR-SCROLL ==========-->
          </div>
          <!--========== END .COL-MD-4 ==========--> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE ==========-->
    <section class="module"> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN .ROW ==========-->
        <div class="row no-gutter"> 
          <?php
          // Photo-feature block: big photo = a Politics story (fall back to
          // Environment, then featured). Half-block = a different category (Arts).
          $photoCat = category_by_slug('politics') ?: category_by_slug('environment');
          [$photoList] = $photoCat ? published_in_category((int) $photoCat['id'], 1, 0) : [[]];
          $photoLead = $photoList[0] ?? (published_articles(1, 'featured') ?: published_articles(1))[0] ?? null;
          $artsCat = category_by_slug('arts-culture');
          [$halfList] = $artsCat ? published_in_category((int) $artsCat['id'], 1, 0) : [[]];
          $halfStory = $halfList[0] ?? (array_slice(published_articles(3), 2, 1)[0] ?? null);
          ?>
          <!-- Begin .full-block-three-columns -->
          <div class="full-block-three-columns">
            <!-- Begin .container-full-->
            <?php if ($photoLead): ?>
            <a href="article?slug=<?= e($photoLead['slug']) ?>" class="container-full bottom-text full-photo" style="text-decoration:none;color:inherit;display:block">
              <div class="entry-media">
                <div class="image" style="display: block; background-image: url(<?= e(a_image($photoLead) ?: 'img/index_875x656.jpg') ?>);"></div>
              </div>
              <div class="content">
                <h2><?= e(a_title($photoLead)) ?></h2>
                <h4><?= e(a_excerpt($photoLead)) ?></h4>
              </div>
            </a>
            <?php endif; ?>
            <!-- End .container-full-->
          </div>
          <!-- End .full-block-three-columns -->
          <!-- Begin .full-block-three-columns -->
          <div class="full-block-three-columns">
            <!-- Begin .container-half -->
            <?php if ($halfStory): ?>
            <div class="container-half">
              <div class="entry-media">
                <div class="image" style="background-image: url(<?= e(a_image($halfStory) ?: 'img/index_800x600.jpg') ?>);">
                <span><a class="label-1" href="section.php?cat=<?= e($halfStory['category_slug'] ?? '') ?>"><?= e($halfStory['category_name'] ?? 'News') ?></a></span> <a href="article?slug=<?= e($halfStory['slug']) ?>"><span class="play-icon"></span></a></div>
                </div>
              <div class="content">
                <h4><a href="article?slug=<?= e($halfStory['slug']) ?>"><?= e(a_title($halfStory)) ?></a></h4>
              </div>
            </div>
            <?php endif; ?>
            <!-- End .container- entry- -->
            <!-- Begin .entry-post -->
            <div class="entry-post">
              <?php
              // 3 more recent stories with category badges.
              $entryPost = array_slice(published_articles(15), 12, 3);
              $epBg = ['bg-1', 'bg-2', 'bg-4'];
              foreach ($entryPost as $ei => $a): ?>
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_464x232-image01.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
                <div class="item-content">
                  <div class="entry-meta <?= $epBg[$ei % 3] ?>"><?= e($a['category_name'] ?? 'News') ?></div>
                  <p class="ellipsis"><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></p>
                </div>
              </div>
              <!-- End .item-->
              <?php endforeach; ?>
            </div>
            <!-- End .entry-post -->
          </div>
          <!-- End .full-block-three-columns --> 
          <!-- Begin .full-block-three-columns -->
          <div class="full-block-three-columns">
            <div class="sidebar-add-place"> <?= ad_slot('sidebar_tall', 'img/banner_300x600.jpg') ?> </div>
          </div>
          <!-- End .full-block-three-columns --> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!-- Begin #parallax-section1 -->
    <div id="parallax-section1">
      <div class="image3 img-overlay1">
        <div class="container">
          <div class="caption text-center">
            <h2 class="color-white weight-300 small-caption">Meet the team behind <strong><?= e(site_name()) ?></strong> — learn more about who we are.</h2>
            <a href="page.php?slug=about-us" class="btn btn-default">About Us</a> </div>
        </div>
      </div>
    </div>
    <!-- End #parallax-section1 --> 
    <!-- Begin .add-place -->
    <div class="add-place"> <?= ad_slot('midpage', 'img/banner_820x100.jg') ?> </div>
    <!-- End .add-place --> 
    <!--========== BEGIN .MODULE ==========-->
    <?php
    // Multimedia / Pictorial stories for the gallery carousel (podcasts,
    // vodcasts, videos, photo essays). Fall back to recent if those are empty.
    $mmGalleryCat  = category_by_slug('multimedia');
    $picGalleryCat = category_by_slug('pictorial');
    $galleryItems = [];
    if ($mmGalleryCat)  { [$g1] = published_in_category((int) $mmGalleryCat['id'], 6, 0);  $galleryItems = array_merge($galleryItems, $g1); }
    if ($picGalleryCat) { [$g2] = published_in_category((int) $picGalleryCat['id'], 6, 0); $galleryItems = array_merge($galleryItems, $g2); }
    if (count($galleryItems) < 4) { $galleryItems = published_articles(6); }
    $galleryItems = array_slice($galleryItems, 0, 8);
    ?>
    <?php if ($galleryItems): ?>
    <section class="module">
      <h2 class="title-style05 style-02">multimedia from <span><a href="section.php?cat=multimedia"><?= e(site_name()) ?></a></span></h2>
      <!-- Begin .title-style05-bg -->
      <div class="center-title"> <span class="title-line-left"></span>
        <h4 class="title-style05 style-01">watch &amp; listen</h4>
        <span class="title-line-right"></span> </div>
      <!-- End .title-style05-bg -->
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container">
        <!--========== BEGIN BIG-GALLERY==========-->
        <!-- Begin .carousel-title -->
        <h3 class="carousel-title-gray">Podcasts, Video &amp; Pictorial</h3>
        <!-- End .carousel-title -->
        <!-- Begin .gallery-slider owl-carousel -->
        <div id="big-gallery-slider-3" class="owl-carousel">
          <?php foreach ($galleryItems as $gi): ?>
          <div class="big-gallery"> <img src="<?= e(a_image($gi) ?: 'img/index_slider-large-image01.jpg') ?>" alt="<?= e(a_title($gi)) ?>"> <a href="article?slug=<?= e($gi['slug']) ?>"><span class="play-icon"></span></a> </div>
          <?php endforeach; ?>
        </div>
        <!-- End .gallery-slider owl-carousel -->
        <!--========== END BIG-GALLERY==========-->
      </div>
      <!--========== BEGIN .CONTAINER ==========-->
    </section>
    <?php endif; ?>
    <!--========== END .MODULE ==========-->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module highlight"> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN .ROW ==========-->
        <div class="row no-gutter"> 
          <!--==========BEGIN .COL-MD-8 ==========-->
          <div class="col-md-8"> 
            <!--========== BEGIN .NEWS ==========-->
            <div class="news">
              <div class="module-title">
                <h3 class="title"><span class="bg-11">Around UR</span></h3>
                <h3 class="subtitle">More stories from across the university</h3>
              </div>
              <?php
              // A varied mix of recent stories (offset so it differs from the
              // top blocks). Multilingual-ready: shows each in its primary language.
              $aroundCat = category_by_slug('research-innovation');
              [$around] = $aroundCat ? published_in_category((int) $aroundCat['id'], 3, 0) : [[]];
              if (count($around) < 3) { $around = array_slice(published_articles(9), 6, 3); }
              foreach ($around as $a): ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-3"><a class="img-link" href="article?slug=<?= e($a['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($a) ?: 'img/index_800x400-image14.jpg') ?>" alt="<?= e(a_title($a)) ?>"></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_title($a)) ?></a></h3>
                  </div>
                  <p><a href="article?slug=<?= e($a['slug']) ?>"> <i class="fa fa-clock-o"></i> <span class="day"><?= e(date('M j, Y', strtotime($a['published_at'] ?: $a['created_at']))) ?></span></a></p>
                  <p><a href="article?slug=<?= e($a['slug']) ?>"><?= e(a_excerpt($a)) ?></a></p>
                  <div> <a href="article?slug=<?= e($a['slug']) ?>"><span class="read-more">Read more</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <?php endforeach; ?>
            </div>
            <!--========== END NEWS ==========--> 
          </div>
          <!--========== END COL-MD-9 ==========--> 
          <!--========== BEGIN COL-MD-4 ==========-->
          <div class="col-md-4"> 
            <!--========== BEGIN .TV SCHEDULE (hidden: no data source yet) ==========-->
            <?php if (false): ?>
            <div class="sidebar-schedule">
              <!-- Begin .block-title-2 -->
              <div class="block-title-2">
                <h3><a href="tv-schedule.html"><strong>TV</strong> Schedule</a></h3>
              </div>
              <!-- End .block-title-2 -->
              <div id="sidebar-schedule-slider" class="owl-carousel"> 
                <!-- Begin .schedule-slide -->
                <div class="sidebar-schedule-slide">
                  <div class="sidebar-schedule-slider-layer full"> <a href="news.html">
                    <div class="content">
                      <h3 class="hour-tag">18:00</h3>
                      <h4 class="sidebar-show-title bg-1">Around the World</h4>
                      <p>New global rules on firms' tax disclosure urged by economists</p>
                    </div>
                    <img src="img/sidebar-schedule_slider-image01.jpg" alt=""> </a> </div>
                </div>
                <!-- End .schedule-slide --> 
                <!-- Begin .schedule-slide -->
                <div class="sidebar-schedule-slide">
                  <div class="sidebar-schedule-slider-layer full"> <a href="news.html">
                    <div class="content">
                      <h3 class="hour-tag">18:45</h3>
                      <h4 class="sidebar-show-title bg-1">Sport Headlines</h4>
                      <p>All the latest sports news from around the world.</p>
                    </div>
                    <img src="img/sidebar-schedule_slider-image02.jpg" alt=""> </a> </div>
                </div>
                <!-- End .schedule-slide --> 
                <!-- Begin .schedule-slide -->
                <div class="sidebar-schedule-slide">
                  <div class="sidebar-schedule-slider-layer full"> <a href="news.html">
                    <div class="content">
                      <h3 class="hour-tag">22:00</h3>
                      <h4 class="sidebar-show-title bg-1">Happening Now</h4>
                      <p>Kerry Thomas will take you to news when and where it happens.</p>
                    </div>
                    <img src="img/sidebar-schedule_slider-image03.jpg" alt=""> </a> </div>
                </div>
                <!-- End .schedule-slide --> 
              </div>
            </div>
            <?php endif; ?>
            <!--========== END .TV SCHEDULE ==========-->
            <!--========== BEGIN .ANNOUNCEMENTS (fills the Around-UR sidebar) ==========-->
            <?php
            $aurAnnCat = category_by_slug('announcements');
            [$aurAnn] = $aurAnnCat ? published_in_category((int) $aurAnnCat['id'], 5, 0) : [[]];
            if (!$aurAnn) { $aurAnn = published_articles(5); }
            if ($aurAnn): ?>
            <div class="block-title-2">
              <h3><a href="section.php?cat=announcements"><strong>Announcements</strong></a></h3>
            </div>
            <div class="sidebar-newsfeed">
              <div class="newsfeed-3">
                <ul>
                  <?php foreach ($aurAnn as $an): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($an['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($an) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($an)) ?>"></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="article?slug=<?= e($an['slug']) ?>"><?= e(a_title($an)) ?></a></h4>
                        <p class="ellipsis"><i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($an['published_at'] ?: $an['created_at']))) ?></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <?php endif; ?>
            <!--========== END .ANNOUNCEMENTS ==========-->
            <!--========== BEGIN SIDEBAR-VIDEO (hidden: no data source yet) ==========-->
            <?php if (false): ?>
            <!-- Begin .title-style01 -->
            <div class="title-style01">
              <h3><strong>Video</strong> News</h3>
            </div>
            <!-- End .title-style01 -->
            <!-- Begin .sidebar-block -->
            <div class="sidebar-block">
              <div class="video-container">
                <iframe src="https://player.vimeo.com/video/66388105?title=0&byline=0&portrait=0" class="video" title="Advertisement"></iframe>
              </div>
              <div class="sidebar-content">
                <p>24 h News Films and Digital Studios present original short films directed by established and rising filmmakers. </p>
              </div>
            </div>
            <!-- End  .sidebar-block -->
            <?php endif; ?>
            <!--========== END SIDEBAR-VIDEO ==========-->
          </div>
          <!--========== END COL-MD-4 ==========--> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE (video module hidden: no data source yet) ==========-->
    <?php if (false): ?>
    <section class="module dark">
      <!--========== BEGIN VIDEO ==========-->
      <!-- Begin .container-->
      <div class="container">
        <div class="row no-gutter">
          <!-- Begin .col-md-9 -->
          <div class="col-sm-9 col-md-9">
            <div class="video-full">
              <div class="video-container">
                <iframe src="https://player.vimeo.com/video/97744717?title=0&byline=0&portrait=0" class="video" title="Advertisement"></iframe>
              </div>
            </div>
          </div>
          <!-- End .col-md-9-->
          <!-- Begin .col-md-3-->
          <div class="col-xs-12 col-sm-3 col-md-3">
            <div class="title-left title-style03 underline03">
              <h4>Related Videos</h4>
            </div>
            <div class="module-media">
              <div class="image"><img class="img-responsive" src="img/index_620x465-image04.jpg" alt=""></div>
              <a href="video.html"><span class="play-icon"></span></a> </div>
            <div class="module-media">
              <div class="image"><img class="img-responsive" src="img/index_620x465-image02.jpg" alt=""></div>
              <a href="video.html"><span class="play-icon"></span></a> </div>
          </div>
          <!-- End .col-md-3-->
        </div>
      </div>
      <!--End .container-->
      <!--========== END VIDEO ==========-->
    </section>
    <?php endif; ?>
    <!--========== END .MODULE ==========-->
    <!--========== BEGIN .MODULE ==========-->
    <section class="module highlight"> 
      <!--========== BEGIN.CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN .ROW ==========-->
        <div class="row no-gutter"> 
          <!--========== BEGIN .C0L-MD-8 ==========-->
          <div class="col-md-8"> 
            <!-- Begin .news -->
            <div class="news">
              <div class="module-title">
                <h3 class="title"><span class="bg-1">In Depth</span></h3>
                <h3 class="subtitle">Features &amp; analysis from <?= e(site_name()) ?></h3>
              </div>
              <?php
              // Two in-depth/feature articles for the highlighted slots.
              $featCat = category_by_slug('features');
              [$featList] = $featCat ? published_in_category((int) $featCat['id'], 2, 0) : [[]];
              if (count($featList) < 2) { $featList = published_articles(2, 'editor_pick') ?: published_articles(2); }
              $rethink = $featList[0] ?? null;
              if ($rethink): ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($rethink['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($rethink) ?: 'img/index_800x400-image17.jpg') ?>" alt="<?= e(a_title($rethink)) ?>"></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($rethink['slug']) ?>"><?= e(a_title($rethink)) ?></a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <?php if (!empty($rethink['category_name'])): ?><div class="post-meta-author"> <i class="fa fa-folder-o"></i> <?= e($rethink['category_name']) ?> </div><?php endif; ?>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($rethink['published_at'] ?: $rethink['created_at']))) ?> </div>
                  </div>
                  <p><a href="article?slug=<?= e($rethink['slug']) ?>" class="external-link"><?= e(a_excerpt($rethink)) ?></a></p>
                  <div> <a href="article?slug=<?= e($rethink['slug']) ?>"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <?php endif; ?>
              <!-- Begin .news-block -->
              <div class="news-block">
                <?php
                // 4 more recent stories under the lead feature.
                $strip = array_slice(published_articles(20), 4, 4);
                foreach ($strip as $sb): ?>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="article?slug=<?= e($sb['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($sb) ?: 'img/index_800x400-image21.jpg') ?>" alt="<?= e(a_title($sb)) ?>"></a></div>
                  <div class="item-content"><span class="day"><?= e(date('D, M j, Y', strtotime($sb['published_at'] ?: $sb['created_at']))) ?></span>
                    <p><a href="article?slug=<?= e($sb['slug']) ?>" class="external-link"><?= e(a_title($sb)) ?></a></p>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <!-- End .news-block -->
              <div class="module-title">
                <h3 class="title"><span class="bg-4">Sport News</span></h3>
                <h3 class="subtitle">Watch the latest sport news</h3>
              </div>
              <?php $feat2 = $featList[1] ?? null; if ($feat2): ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($feat2['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($feat2) ?: 'img/index_800x400-image18.jpg') ?>" alt="<?= e(a_title($feat2)) ?>"></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($feat2['slug']) ?>"><?= e(a_title($feat2)) ?></a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <?php if (!empty($feat2['category_name'])): ?><div class="post-meta-author"> <i class="fa fa-folder-o"></i> <?= e($feat2['category_name']) ?> </div><?php endif; ?>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($feat2['published_at'] ?: $feat2['created_at']))) ?> </div>
                  </div>
                  <p><a href="article?slug=<?= e($feat2['slug']) ?>" class="external-link"><?= e(a_excerpt($feat2)) ?></a></p>
                  <div> <a href="article?slug=<?= e($feat2['slug']) ?>"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <?php endif; ?>
              <div class="news-block">
                <?php
                // Mini grid of more recent stories (offset to vary from above).
                $blockList = array_slice(published_articles(16), 8, 8);
                foreach ($blockList as $b): ?>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="article?slug=<?= e($b['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($b) ?: 'img/index_800x400-image25.jpg') ?>" alt="<?= e(a_title($b)) ?>"></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i><span class="day"><?= e(date('M j', strtotime($b['published_at'] ?: $b['created_at']))) ?></span><a href="article?slug=<?= e($b['slug']) ?>"><?= e($b['category_name'] ?? 'News') ?></a></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <!-- End .news --> 
          </div>
          <!--========== END .C0L-MD-8 ==========--> 
          <!--========== BEGIN .C0L-MD-4 ==========-->
          <div class="col-md-4"> 
            <!-- Begin .title-style02 -->
            <div class="title-style02">
              <h3><a href="#">Headlines</a></h3>
            </div>
            <!-- End .title-style02 --> 
            <!--========== BEGIN .SIDEBAR-POST ==========-->
            <div class="sidebar-post">
              <ul>
                <?php
                // Numbered "Headlines" list = editor's picks (fallback recent).
                $headlines = published_articles(9, 'editor_pick');
                if (count($headlines) < 9) { $headlines = published_articles(9); }
                foreach ($headlines as $hi => $hl): ?>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="article?slug=<?= e($hl['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($hl) ?: 'img/index_370x185-image07.jpg') ?>" alt="<?= e(a_title($hl)) ?>"></a></div>
                    <div class="item-content">
                      <h3><?= str_pad((string) ($hi + 1), 2, '0', STR_PAD_LEFT) ?></h3>
                      <p class="ellipsis"><a href="article?slug=<?= e($hl['slug']) ?>"><?= e(a_title($hl)) ?></a></p>
                    </div>
                  </div>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <!--========== END .SIDEBAR-POST ==========-->
             <!--========== BEGIN #WEATHER (hidden: no data source) ==========-->
            <?php if (false): ?>
            <div id='weather' class='sidebar-weather'>
              <!-- Begin .block-title-1 -->
              <div class="block-title-1">
                <div class='weather-city-text'></div>
              </div>
              <!-- End .block-title-1 -->
              <div class='weather-card'>
                <div class="temp"> <i class="weather-icon wi"></i>
                  <div class='temperature'></div>
                  <button class='btn btn-primary'> <span class="switch">F</span> </button>
                </div>
                <div id='description'>
                  <div id='type' class='desc-text'> </div>
                  <i class="wi wi-humidity"></i>
                  <div id='humidity' class='desc-text'> </div>
                  <i class="wi wi-strong-wind"></i>
                  <div id='wind' class='desc-text'> </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <!--========== END  #WEATHER ==========-->
          </div>
          <!--========== END .COL-MD-4 ==========--> 
        </div>
        <!--========== BEGIN ON-AIR (live radio/TV streams) ==========-->
          <?php $streams = active_streams(); if ($streams): ?>
          <!-- Begin .outer -->
          <div class="outer">
            <div class="breaking-ribbon">
              <h5>On Air Now</h5>
            </div>
            <!-- Begin .newsticker -->
            <div class="news-on-air">
              <ul>
                <?php foreach ($streams as $st): ?>
                <li>
                  <h4><i class="fa fa-microphone" aria-hidden="true"></i> <a href="<?= e($st['stream_url']) ?>" target="_blank" rel="noopener">Listen live: <?= e($st['name']) ?></a></h4>
                </li>
                <?php endforeach; ?>
              </ul>
              <div class="navi">
                <button class="up"><i class="fa fa-caret-left"></i></button>
                <button class="down"><i class="fa fa-caret-right"></i></button>
              </div>
            </div>
            <!-- End .outer -->
          </div>
          <?php endif; ?>
          <!--========== END ON-AIR ==========-->
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE ==========-->
    <section class="module">
      <div class="container">
        <div class="row no-gutter"> 
          <!--========== BEGIN .COL-MD-8 ==========-->
          <div class="col-md-8">
            <div class="news">
              <div class="module-title">
                <h3 class="title"><span class="bg-2">Health &amp; Lifestyle</span></h3>
                <h3 class="subtitle">Wellbeing on campus</h3>
              </div>
              <?php
              $healthCat = category_by_slug('health-lifestyle');
              [$healthList] = $healthCat ? published_in_category((int) $healthCat['id'], 5, 0) : [[]];
              if (!$healthList) { $healthList = published_articles(5); }
              $healthLead = $healthList[0] ?? null;
              if ($healthLead): ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($healthLead['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($healthLead) ?: 'img/index_800x400-image19.jpg') ?>" alt="<?= e(a_title($healthLead)) ?>"></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($healthLead['slug']) ?>"><?= e(a_title($healthLead)) ?></a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($healthLead['published_at'] ?: $healthLead['created_at']))) ?> </div>
                  </div>
                  <p><a href="article?slug=<?= e($healthLead['slug']) ?>" class="external-link"><?= e(a_excerpt($healthLead)) ?></a></p>
                  <div> <a href="article?slug=<?= e($healthLead['slug']) ?>"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <div class="news-block">
                <?php foreach (array_slice($healthList, 1, 4) as $hb): ?>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="article?slug=<?= e($hb['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($hb) ?: 'img/index_800x400-image32.jpg') ?>" alt="<?= e(a_title($hb)) ?>"></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"><?= e(date('M j', strtotime($hb['published_at'] ?: $hb['created_at']))) ?></span>
                    <p><a href="article?slug=<?= e($hb['slug']) ?>"><?= e(a_title($hb)) ?></a></p>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <div class="module-title">
                <h3 class="title"><span class="bg-9">Research &amp; Innovation</span></h3>
                <h3 class="subtitle">Academic work and student projects</h3>
              </div>
              <?php
              $resCat = category_by_slug('research-innovation');
              [$resList] = $resCat ? published_in_category((int) $resCat['id'], 5, 0) : [[]];
              if (!$resList) { $resList = published_articles(5); }
              $resLead = $resList[0] ?? null;
              if ($resLead): ?>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="article?slug=<?= e($resLead['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($resLead) ?: 'img/index_800x400-image20.jpg') ?>" alt="<?= e(a_title($resLead)) ?>"></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="article?slug=<?= e($resLead['slug']) ?>"><?= e(a_title($resLead)) ?></a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($resLead['published_at'] ?: $resLead['created_at']))) ?> </div>
                  </div>
                  <p><a href="article?slug=<?= e($resLead['slug']) ?>" class="external-link"><?= e(a_excerpt($resLead)) ?></a></p>
                  <div> <a href="article?slug=<?= e($resLead['slug']) ?>"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <div class="news-block">
                <?php foreach (array_slice($resList, 1, 4) as $rb): ?>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="article?slug=<?= e($rb['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($rb) ?: 'img/index_800x400-image36.jpg') ?>" alt="<?= e(a_title($rb)) ?>"></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"><?= e(date('M j', strtotime($rb['published_at'] ?: $rb['created_at']))) ?></span>
                    <p><a href="article?slug=<?= e($rb['slug']) ?>"><?= e(a_title($rb)) ?></a></p>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
            <!--========== BEGIN NEWS==========--> 
            <!--========== BEGIN .PAGINATION ==========-->
            <ul class="pagination">
              <li><a href="#">&lsaquo;</a></li>
              <li class="active"><a href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
              <li><a href="#">&rsaquo;</a></li>
            </ul>
            <!--========== END .PAGINATION ==========--> 
          </div>
          <!--========== END .C0L-MD-8 ==========--> 
          <!--========== BEGIN .C0L-MD-4 ==========-->
          <div class="col-md-4"> 
            <!--========== BEGIN OPPORTUNITIES ==========-->
            <?php
            $oppCat = category_by_slug('opportunities');
            [$oppList] = $oppCat ? published_in_category((int) $oppCat['id'], 5, 0) : [[]];
            // Include Career Hub items too if Opportunities is thin.
            if (count($oppList) < 5) {
                $careerCat = category_by_slug('career-hub');
                if ($careerCat) { [$careerList] = published_in_category((int) $careerCat['id'], 5, 0); $oppList = array_merge($oppList, $careerList); }
            }
            $oppList = array_slice($oppList, 0, 5);
            if ($oppList): ?>
            <div class="block-title-1">
              <h3><a href="section?cat=opportunities"><strong>Opportunities</strong></a></h3>
            </div>
            <div class="sidebar-newsfeed">
              <div class="newsfeed-3">
                <ul>
                  <?php foreach ($oppList as $op): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($op['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($op) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($op)) ?>"></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="article?slug=<?= e($op['slug']) ?>"><?= e(a_title($op)) ?></a></h4>
                        <p class="ellipsis"><i class="fa fa-graduation-cap"></i> <?= e($op['category_name'] ?? 'Opportunity') ?></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <?php endif; ?>
            <!--========== END OPPORTUNITIES ==========-->
            <!--========== BEGIN CURRENCY CONVERTER (hidden: not relevant) ==========-->
            <?php if (false): ?>
            <!-- Begin .block-title-3 -->
            <div class="block-title-3">
              <h3>currency converter</h3>
            </div>
            <!-- End .block-title-3 -->
            <form class="conversionForm">
              <div class="conversionForm-amount">
                <label for="amount">Amount:</label>
                <input type="number" class="currencyValue form-control no-spinners" title="Amount" id="amount">
              </div>
              <div class="conversionForm-currencies">
                <div class="conversionForm__from">
                  <label for="from">From:</label>
                  <select class="convertFrom selectpicker show-menu-arrow form-control" data-style="btn btn-primary" title="From" id="from">
                  </select>
                </div>
                <div class="conversionForm__to">
                  <label for="to">To:</label>
                  <select class="convertTo selectpicker show-menu-arrow form-control" data-style="btn btn-primary" title="To" id="to">
                  </select>
                </div>
              </div>
              <input type="submit" value="convert" class="currencySubmit btn btn-default">
              <p class="output"></p>
            </form>
            <?php endif; ?>
            <!--========== END CURRENCY CONVERTER ==========-->
            <!--========== BEGIN .CALENDAR (hidden: no data source) ==========-->
            <?php if (false): ?><div id='calendar'></div><?php endif; ?>
            <!--========== END .CALENDAR ==========-->
            <!--========== BEGIN .ANNOUNCEMENTS (replaces the calendar) ==========-->
            <?php
            $annCat = category_by_slug('announcements');
            [$annList] = $annCat ? published_in_category((int) $annCat['id'], 5, 0) : [[]];
            if (!$annList) { $annList = published_articles(5); }
            if ($annList): ?>
            <div class="block-title-1">
              <h3><a href="section.php?cat=announcements"><strong>Announcements</strong></a></h3>
            </div>
            <div class="sidebar-newsfeed">
              <div class="newsfeed-3">
                <ul>
                  <?php foreach ($annList as $an): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($an['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($an) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($an)) ?>"></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="article?slug=<?= e($an['slug']) ?>"><?= e(a_title($an)) ?></a></h4>
                        <p class="ellipsis"><i class="fa fa-calendar"></i> <?= e(date('M j, Y', strtotime($an['published_at'] ?: $an['created_at']))) ?></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <?php endif; ?>
            <!--========== END .ANNOUNCEMENTS ==========-->
            <!--========== BEGIN #SIDEBAR-NEWSLETTER ==========-->
            <!-- <div id="sidebar-newsletter">  -->
              <!-- Begin .title-style01 -->
              <!-- <div class="title-style01"> -->
                <!-- <h3><strong>Newsletter</strong></h3> -->
              <!-- </div> -->
              <!-- End .title-style01 --> 
              <!-- Begin .sidebar-newsletter-form -->
              <!-- <div class="sidebar-newsletter-form">
                <form class="form-horizontal" action="include/subscribe.php" id="subscribeForm" method="POST">
                  <div class="input-group">
                    <input title="Newsletter" class="form-control" name="email" type="email" id="address" placeholder="Enter Your Email Address" data-validate="validate(required, email)" required="">
                    <span class="input-group-btn">
                    <button type="submit" class="btn btn-success">Subscribe</button>
                    </span> </div>
                </form>
                <span id="result" class="alertMsg"></span> </div> -->
              <!-- End .sidebar-newsletter-form -->  
            <!-- </div> -->
            <!--========== END #SIDEBAR-NEWSLETTER ==========--> 
            <!--========== BEGIN #SIDEBAR-SOCIAL-BUTTONS ==========-->
            <!-- <div class="sidebar-social-icons">  -->
              <!-- Begin .title-style01 -->
              <!-- <div class="title-style01">
                <h3><strong>Stay</strong> Connected</h3>
              </div> -->
              <!-- End .title-style01 -->
              <!-- <ul>
                <li> <a href="#" class="facebook"><i class="fa fa-facebook"></i></a> </li>
                <li> <a href="#" class="youtube"><i class="fa fa-youtube"></i></a> </li>
                <li> <a href="#" class="twitter"><i class="fa fa-twitter"></i></a> </li>
                <li> <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a> </li>
                <li> <a href="#" class="pinterest"><i class="fa fa-pinterest"></i></a> </li>
                <li> <a href="#" class="google-plus"><i class="fa fa-google-plus"></i></a> </li>
                <li> <a href="#" class="rss"><i class="fa fa-rss"></i></a> </li>
                <li> <a href="#" class="tumblr"><i class="fa fa-tumblr"></i></a> </li>
              </ul> -->
            <!-- </div> -->
            <!--========== END #SIDEBAR-SOCIAL-BUTTONS ==========--> 
          </div>
          <!--========== END .C0L-MD-4 ==========--> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE  ==========-->
    <section class="module highlight"> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN SMALL-GALLERY ==========--> 
        <!-- Begin .carousel-title -->
        <h3 class="carousel-title">More Stories</h3>
        <!-- End .carousel-title -->
        <!-- Begin .gallery-slider owl-carousel -->
        <div id="small-gallery-slider" class="owl-carousel">
          <?php foreach (published_articles(10) as $sg): ?>
          <div class="small-gallery"> <img class="img-responsive" src="<?= e(a_image($sg) ?: 'img/index_108x108_slider-image01.jpg') ?>" alt="<?= e(a_title($sg)) ?>">
            <div class="post-content"> <a href="article?slug=<?= e($sg['slug']) ?>"><?= e($sg['category_name'] ?? 'News') ?></a>
              <p><a href="article?slug=<?= e($sg['slug']) ?>"><?= e(a_title($sg)) ?></a></p>
              <i class="fa fa-clock-o"></i><span class="day"> <?= e(date('D, j M, Y', strtotime($sg['published_at'] ?: $sg['created_at']))) ?></span> </div>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- End .gallery-slider owl-carousel -->
        <!--========== END SMALL-GALLERY ==========--> 
        <!-- Begin .bottom-add-place-->
        <div class="bottom-add-place"></div>
        <!-- End .bottom-add-place --> 
      </div>
      <!--========== END .CONTAINER ==========--> 
    </section>
    <!--========== END .MODULE ==========--> 
  </section>
  <!--========== END #MAIN-SECTION ==========--> 
<?php include"footer.php"?>