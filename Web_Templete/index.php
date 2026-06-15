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
                <a href="article.php?slug=<?= e($hero['slug']) ?>" class="color-white">
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
                <h4><span class="category"><?= e($b['category_name'] ?? 'News') ?>:</span><a href="article.php?slug=<?= e($b['slug']) ?>"> <?= e(a_title($b)) ?></a></h4>
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
            <div class="news-slider-layer <?= $positions[$i % 4] ?>"> <a href="article.php?slug=<?= e($a['slug']) ?>" target="_blank">
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
    <section class="module highlight">
      <div class="container">
        <div class="module-title">
          <h3 class="title"><span class="bg-1">World News</span></h3>
          <h3 class="subtitle">Watch the latest news</h3>
        </div>
        <!--========== BEGIN .ROW ==========-->
        <div class="row no-gutter"> 
          <!--========== BEGIN .COL-MD-6 ==========-->
          <div class="col-sm-6 col-md-6"> 
            <!--========== BEGIN .NEWS ==========-->
            <div class="news"> 
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image01.jpg" alt=""></a><span><a class="label-1" href="news.html">News</a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Migrant</strong> Crisis</a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">The proposal involves resettling one Syrian refugee in Europe for each</a></p>
                  <p><a href="#" target="_blank" class="external-link">The U.N. says the mass return of refugees to a third country would</a></p>
                  <div><a href="news.html" target="_blank"><span class="read-more">News</span></a></div>
                </div>
              </div>
              <!-- End .item --> 
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image02.jpg" alt=""></a><span><a class="label-3" href="politics.html">Politics</a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Smith's</strong> Team</a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">Democratic presidential candidate Smith speaks with supporters...</a></p>
                  <p><a href="#" target="_blank" class="external-link">On International Women’s Day, we celebrate women around the world...</a></p>
                  <div><a href="politics.html" target="_blank"><span class="read-more">Politics</span></a></div>
                </div>
              </div>
              <!-- End .item --> 
            </div>
            <!--========== END .NEWS ==========--> 
          </div>
          <!--========== END .COL-MD-6 ==========--> 
          <!--========== BEGIN .COL-MD-6 ==========-->
          <div class="col-sm-6 col-md-6"> 
            <!--========== BEGIN .NEWS==========-->
            <div class="news"> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image03.jpg" alt=""></a><span><a class="label-5" href="tech-science.html">Science</a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Indonesia</strong></a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">Image caption Belitung in Indonesia was the best place to witness...</a></p>
                  <p><a href="#" target="_blank" class="external-link">Millions of people across Indonesia and Pacific have experienced a total</a></p>
                  <div><a href="tech-science.html" target="_blank"><span class="read-more">Tech-Science</span></a></div>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image04.jpg" alt=""></a><span><a class="label-2" href="health.html">Health</a></span></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Global</strong> Health</a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">Global health has been defined as an area of study and research, that</a></p>
                  <p><a href="#" target="_blank" class="external-link">Everyone has the right to a standard of living adequate for the health and</a> </p>
                  <div><a href="health.html" target="_blank"><span class="read-more">Health</span></a></div>
                </div>
              </div>
              <!-- End .item --> 
            </div>
            <!--========== END .NEWS ==========--> 
          </div>
          <!--========== END .COL-MD-6 ==========--> 
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
                <h3 class="subtitle">Latest News in details</h3>
              </div>
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image05.jpg" alt=""></a><span><a class="label-2" href="politics.html">Politics</a></span> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Woman</strong> in Mission Hills</a></h3>
                  </div>
                  <p><a href="#"> A man and a woman were arrested after he allegedly fired off several rounds from a moving car</a></p>
                  <p><a href="#" target="_blank" class="external-link">The male passenger was arrested for firing the gun and one of the women</a></p>
                  <div> <a href="politics.html" target="_blank"><span class="read-more">Politics</span></a> </div>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image06.jpg" alt=""></a><span><a class="label-1" href="news.html">News</a></span> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong> Mother</strong> Hopes Renewed </a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">3 Years After Man’s Death, Grieving Mother Hopes Renewed Reward Will Help Find Son’s Killer.</a></p>
                  <p><a href="#" target="_blank" class="external-link">A $20,000 reward was being renewed for information that will help solve the case</a></p>
                  <div> <a href="24-news-store.html" target="_blank"><span class="read-more">24 News Store</span></a> </div>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image07.jpg" alt=""></a><span><a class="label-5" href="business.html">Economy</a></span> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>The Larger</strong> Economy?</a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">How Do Non-Profits Affect The Larger Economy?</a></p>
                  <p><a href="#" target="_blank" class="external-link">According to the Bureau of Labor and Statistics, an aging population will entry-post to a greater need in our societies</a></p>
                  <div> <a href="watch-live.html" target="_blank"><span class="read-more">Watch Live</span></a> </div>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image-2"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image08.jpg" alt=""></a><a class="label-6" href="business.html">Business</a> </div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Technology</strong> of Business </a></h3>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">A mixed business - features, merits and demerits, is a golden mean between a capitalist business and a socialist business.</a></p>
                  <p><a href="#" target="_blank" class="external-link">It is an economic system where the price mechanism</a></p>
                  <div> <a href="business.html" target="_blank"><span class="read-more">Business</span></a> </div>
                </div>
              </div>
              <!-- End .item--> 
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
              <h3><a href="#"><strong>24h News</strong> Feed</a></h3>
            </div>
            <!-- End .block-title-1 --> 
            <!--========== BEGIN .SIDEBAR-NEWSFEED ==========-->
            <div class="sidebar-newsfeed"> 
              <!-- Begin .newsfeed -->
              <div class="newsfeed-3">
                <ul>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image01.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">From propaganda to pop artist</a></h4>
                        <p class="ellipsis"><a href="#">A gift for drawing led to a prestigious career as artist...</a></p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image02.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">One Type of Food You Never Eat</a></h4>
                        <p class="ellipsis"><a href="#">No one has ever felt better after eating plane food.</a></p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image03.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">Preschool</a></h4>
                        <p class="ellipsis"><a href="#">Your child should feel confident in her ability to meet the challenges...</a></p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image04.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">Antarctica Stations</a></h4>
                        <p class="ellipsis"><a href="#">New research factors in collapsing Antarctic ice sheet that could...</a></p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image05.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">Yellow fever outbreak</a></h4>
                        <p class="ellipsis"><a href="#">Drug manufacturers globally produce just enough vaccines for...</a></p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image06.jpg" alt=""></a></div>
                      <div class="item-content">
                        <h4 class="ellipsis"><a href="#">How To Succeed In</a></h4>
                        <p class="ellipsis"><a href="#">How to achieve business success in a certain sector or country.</a></p>
                      </div>
                    </div>
                  </li>
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
    <!--========== BEGIN .MODULE ==========-->
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
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE ==========-->
    <section class="module highlight">
      <div class="container">
        <div class="row no-gutter"> 
          <!--========== BEGIN .COL-MD-8 ==========-->
          <div class="col-md-8">
            <div class="module-title">
              <h3 class="title"><span class="bg-1">Local News</span></h3>
              <h3 class="subtitle">Latest News in details</h3>
            </div>
            <!--========== BEGIN .ROW ==========-->
            <div class="row no-gutter"> 
              <!--========== BEGIN .COL-MD-3 ==========-->
              <div class="col-xs-12 col-sm-3 col-md-3"> 
                <!-- Begin .list list-mark-1 -->
                <ul class="list list-mark-1">
                  <li><a href="index.html">Home</a></li>
                  <li><a href="watch-live.html">Watch Live 24/7</a></li>
                  <li><a href="24-tv-radio.html">24 TV & Radio</a></li>
                  <li><a href="web-shows.html">Web Shows</a></li>
                  <li><a href="24-news-store.html">24 News Store</a></li>
                  <li><a href="tv-schedule.html">TV Schedule</a></li>
                  <li><a href="news.html">News</a></li>
                  <li><a href="politics.html">Politics</a> | <a href="business.html">Business</a></li>
                  <li><a href="tech-science.html">Tech-Science</a></li>
                  <li><a href="lifestyle.html">Lifestyle</a></li>
                  <li><a href="sport.html">Sport</a></li>
                  <li><a href="americanfootball.html">American Football</a></li>
                  <li><a href="soccer.html">Soccer</a></li>
                  <li><a href="basketball.html">Basketball</a></li>
                  <li><a href="formula-1.html">Formula 1</a></li>
                  <li><a href="tennis.html">Tennis</a></li>
                  <li><a href="health.html">Healt</a></li>
                  <li><a href="men-health.html">Men's Health</a></li>
                  <li><a href="women-health.html">Women's Health</a></li>
                  <li><a href="children-health.html">Children's Health</a></li>
                  <li><a href="world.html">World</a></li>
                  <li><a href="world.html">Africa</a> | <a href="world.html">Antarctica</a></li>
                  <li><a href="world.html">Asia </a> | <a href="world.html">Australia</a></li>
                  <li><a href="world.html">Europe</a> | <a href="world.html">Middle East</a></li>
                  <li><a href="world.html">North America</a></li>
                  <li><a href="world.html">South America</a></li>
                  <li><a href="travel.html">Travel</a></li>
                  <li><a href="travel.html">Destinations</a></li>
                  <li><a href="travel.html">Types Of Trip</a></li>
                  <li><a href="travel.html">National Parks</a></li>
                  <li><a href="environment.html">Environment</a></li>
                  <li><a href="art-entertainment.html">Music & Opera </a></li>
                  <li><a href="art-entertainment.html">Celebrity & Film </a></li>
                  <li><a href="art-entertainment.html">Theatre & Dance </a></li>
                  <li><a href="art-entertainment.html">Art & Entertainment</a></li>
                </ul>
                <!-- End .list list-mark-1 --> 
              </div>
              <!--========== END .COL-MD-3 ==========--> 
              <!--========== BEGIN .COL-MD-9 ==========-->
              <div class="col-xs-12 col-sm-9 col-md-9"> 
                <!--========== BEGIN .NEWS ==========-->
                <div class="news">
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image09.jpg" alt=""></a><span><a class="label-1" href="news.html">Breaking News</a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="#"><strong>Explosion</strong></a></h3>
                      </div>
                      <p><a href="#" target="_blank" class="external-link"> At least nine firefighters were injured when a massive natural gas explosion...</a></p>
                      <div> <a href="watch-live.html" target="_blank"><span class="read-more">Watch Live</span></a> </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image10.jpg" alt=""></a><span><a class="label-4" href="travel.html">Sport</a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="#"><strong>Report's</strong> Guide</a></h3>
                      </div>
                      <p><a href="#" target="_blank" class="external-link">Welcome to this glorious time of unfiltered optimism. Welcome to the latest installment of spring football.</a></p>
                      <div> <a href="sport.html" target="_blank"><span class="read-more">Sport</span></a> </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image11.jpg" alt=""></a><span><a class="label-9" href="soccer.html">Lifestyle</a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="#"><strong>Lifestyle</strong> News</a></h3>
                      </div>
                      <p><a href="#" target="_blank" class="external-link">Divorce negotiations have intensified: ‘Will do whatever it takes to be with kids’, Sources Say</a></p>
                      <div> <a href="lifestyle.html" target="_blank"><span class="read-more">Lifestyle</span></a> </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image12.jpg" alt=""></a><span><a class="label-3" href="health.html">Travel</a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="#"><strong>Browns</strong> Canyon</a></h3>
                      </div>
                      <p><a href="#" target="_blank" class="external-link">To make a run at a national title, each element has to fall perfectly into place.</a></p>
                      <div> <a href="travel.html" target="_blank"><span class="read-more">Travel</span></a> </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image13.jpg" alt=""></a><span><a class="label-6" href="health.html">Web Shows</a></span> </div>
                    <div class="item-content">
                      <div class="title-left title-style04 underline04">
                        <h3><a href="#"><strong>The Web</strong> Shows</a></h3>
                      </div>
                      <p><a href="#" target="_blank" class="external-link">Critical acclaim, star power and an increasing number of web-to-TV success stories...</a></p>
                      <div> <a href="web-shows.html" target="_blank"><span class="read-more">Web Shows</span></a> </div>
                    </div>
                    <!-- End .content--> 
                  </div>
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
              <!-- Begin .scroll-item -->
              <div class="item">
                <div class="item-image-full"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image02.jpg" alt=""></a></div>
              </div>
              <div class="item">
                <div class="item-content-1">
                  <h3>Tens of thousands of people have demonstrated against the governing.</h3>
                </div>
              </div>
              
              <!-- End .scroll-item -->
              <div class="scroll-item">
                <div class="item">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image40.jpg" alt=""></a></div>
                  <div class="item-content">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 1 min ago</span> <br>
                      The River Seine is at its highest level for more than 30 years. </p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 2 min ago</span> <br>
                      First-time buyers are being warned they could pay more if they sign up for 40-year mortgages. </p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><a href="#"> <span class="day"> 3 min ago</span> <br>
                      The M. players ran through a banner which read: “You don’t need to wear red & blue...</a></p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image41.jpg" alt=""></a></div>
                  <div class="item-content">
                    <p><a href="#"> <span class="day"> 4 min ago</span> <br>
                      The flat brown spots show up on some parts of the skin...</a></p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image42.jpg" alt=""></a></div>
                  <div class="item-content">
                    <p><a href="#"> <span class="day">5 min ago</span> <br>
                      The natural cycles alone aren’t sufficient to explain...</a></p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 6 min ago</span> <br>
                      L.Counsel is demanding stop its policy of censoring student religious speech. </p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 7 min ago</span> <br>
                      Often broadcast social media sites with popular hashtags but the social media has nothing to do.</p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day">8 min ago</span> <br>
                      The River Seine is at its highest level for more than 30 years. </p>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="item-image-full"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image04.jpg" alt=""></a></div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 9 min ago</span> <br>
                      Magazines are publications, that are printed or electronically published online magazines. </p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 10 min ago</span> <br>
                      First-time buyers are being warned they could pay more if they sign up for 40-year mortgages </p>
                  </div>
                </div>
              </div>
              <div class="scroll-item">
                <div class="item">
                  <div class="item-content-1">
                    <p><i class="fa fa-clock-o"></i> <span class="day"> 11 min ago</span> <br>
                      New research provides new evidence that natural cycles alone aren’t sufficient to explain the global atmospheric warming. </p>
                  </div>
                </div>
              </div>
              <!-- End .scroll-item --> 
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
          <!-- Begin .full-block-three-columns -->
          <div class="full-block-three-columns"> 
            <!-- Begin .container-full-->
            <div class="container-full bottom-text full-photo">
              <div class="entry-media">
                <div class="image" style="display: block; background-image: url(img/index_875x656.jpg);"></div>
              </div>
              <div class="content">
                <h2><a href="#">Florida International Air Show</a></h2>
                <h4>One of the top 15 air show events and festivals in the USA, Florida International Air Show has been a popular event for nearly 40 years.</h4>
              </div>
            </div>
            <!-- End .container-full--> 
          </div>
          <!-- End .full-block-three-columns --> 
          <!-- Begin .full-block-three-columns -->
          <div class="full-block-three-columns"> 
            <!-- Begin .container-half -->
            <div class="container-half">
              <div class="entry-media">
                <div class="image" style="background-image: url(img/index_800x600.jpg);">
                <span><a class="label-1" href="#">World News</a></span> <a href="video.html"><span class="play-icon"></span></a></div>
                </div>
              <div class="content">
                <h4><a href="#">People have demonstrated against the policies...</a></h4>
              </div>
            </div>
            <!-- End .container- entry- --> 
            <!-- Begin .entry-post -->
            <div class="entry-post"> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_464x232-image01.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="entry-meta bg-1">News</div>
                  <p class="ellipsis"><a href="#">Police are hunting a group of men believed to be responsible for...</a></p>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_464x232-image02.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="entry-meta bg-2">Business</div>
                  <p class="ellipsis"><a href="#">The sale creates further uncertainty for British steelworkers...</a></p>
                </div>
              </div>
              <!-- End .item--> 
              <!-- Begin .item-->
              <div class="item">
                <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_464x232-image03.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="entry-meta bg-4">Politics</div>
                  <p class="ellipsis"><a href="#">Do referendums lead to better outcomes?</a></p>
                </div>
              </div>
              <!-- End .item--> 
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
            <h2 class="color-white weight-300 small-caption">We introduce you our <strong>24h News Team!</strong> Get more information about us here!</h2>
            <a href="about-us.html" target="_blank" class="btn btn-default">About Us</a> </div>
        </div>
      </div>
    </div>
    <!-- End #parallax-section1 --> 
    <!-- Begin .add-place -->
    <div class="add-place"> <?= ad_slot('midpage', 'img/banner_820x100.jpg') ?> </div>
    <!-- End .add-place --> 
    <!--========== BEGIN .MODULE ==========-->
    <section class="module">
      <h2 class="title-style05 style-02">more headlines in our <span><a href="#">item sections</a></span></h2>
      <!-- Begin .title-style05-bg -->
      <div class="center-title"> <span class="title-line-left"></span>
        <h4 class="title-style05 style-01">latest # news</h4>
        <span class="title-line-right"></span> </div>
      <!-- End .title-style05-bg --> 
      <!--========== BEGIN .CONTAINER ==========-->
      <div class="container"> 
        <!--========== BEGIN BIG-GALLERY==========--> 
        <!-- Begin .carousel-title -->
        <h3 class="carousel-title-gray">Home Gallery</h3>
        <!-- End .carousel-title --> 
        <!-- Begin .gallery-slider owl-carousel -->
        <div id="big-gallery-slider-3" class="owl-carousel">
          <div class="big-gallery"> <img src="img/index_slider-large-image01.jpg" alt=""> <a href="video.html"><span class="play-icon"></span></a> </div>
          <div class="big-gallery"><img src="img/index_slider-large-image02.jpg" alt=""> <a href="video.html"><span class="play-icon"></span></a> </div>
          <div class="big-gallery"> <img src="img/index_slider-large-image03.jpg" alt=""><a href="video.html"><span class="play-icon"></span></a> </div>
          <div class="big-gallery"><img src="img/index_slider-large-image04.jpg" alt=""><a href="video.html"><span class="play-icon"></span></a> </div>
          <div class="big-gallery"> <img src="img/index_slider-large-image05.jpg" alt=""><a href="video.html"><span class="play-icon"></span></a> </div>
          <div class="big-gallery"> <img src="img/index_slider-large-image06.jpg" alt=""><a href="video.html"><span class="play-icon"></span></a> </div>
        </div>
        <!-- End .gallery-slider owl-carousel --> 
        <!--========== END BIG-GALLERY==========--> 
      </div>
      <!--========== BEGIN .CONTAINER ==========--> 
    </section>
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
                <h3 class="title"><span class="bg-11">Worldwide 24h News</span></h3>
                <h3 class="subtitle">News in other languages</h3>
              </div>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image14.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3 lang="fr"><a href="#"><strong>Nouvelle sonde</strong> autour de Jupiter.</a></h3>
                  </div>
                  <p lang="fr"><a href="#"> <i class="fa fa-clock-o"></i> <span class="day"><strong> 5 october 2016</strong></span></a></p>
                  <p lang="fr"><a href="#">Des signaux transmis par l'engin spatial ont confirmé que la manœuvre de freinage s’était déroulée comme prévu.</a></p>
                  <p lang="fr"><a href="#">L’intensité des ceintures de radiation de la planète...</a> </p>
                  <div> <a href="#"><span lang="fr" class="read-more">Lire la suite</span></a> </div>
                </div>
              </div>
              <!-- End .item --> 
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image15.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3 lang="es"><a href="#"><strong>Mark</strong> acudió a la manifestación...</a></h3>
                  </div>
                  <p lang="es"><a href="#"> <i class="fa fa-clock-o"></i> <span class="day"><strong>1 hora</strong></span></a></p>
                  <p lang="es"><a href="#">En pleno caos por el ataque contra los policías en Dallas, las fuerzas de seguridad pidieron ayuda para encontrar a un primer sospechoso: un hombre armado y con camiseta militar de camuflaje.</a></p>
                  <div> <a href="#"><span lang="es" class="read-more">Lee mas</span></a> </div>
                </div>
              </div>
              <!-- End .item --> 
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-3"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image16.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3 lang="ru"><a href="#"><strong>Может</strong> ли спутник  на голову?</a></h3>
                  </div>
                  <p lang="ru"><a href="#"> <i class="fa fa-clock-o"></i> <span class="day"><strong>1 час назад</strong></span></a> </p>
                  <p lang="ru"><a href="#">Количество космического мусора на околоземной орбите неуклонно растет. Обозреватель  Future решил разобраться, что происходит, когда отработавшие своё спутники падают на Землю.</a></p>
                  <div> <a href="#"><span lang="ru" class="read-more">прочитайте больше</span></a> </div>
                </div>
              </div>
              <!-- End .item --> 
            </div>
            <!--========== END NEWS ==========--> 
          </div>
          <!--========== END COL-MD-9 ==========--> 
          <!--========== BEGIN COL-MD-4 ==========-->
          <div class="col-md-4"> 
            <!--========== BEGIN .TV SCHEDULE ==========-->
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
            <!--========== END .TV SCHEDULE ==========--> 
            <!--========== BEGIN SIDEBAR-VIDEO ==========--> 
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
            <!--========== END SIDEBAR-VIDEO ==========--> 
          </div>
          <!--========== END COL-MD-4 ==========--> 
        </div>
      </div>
    </section>
    <!--========== END .MODULE ==========--> 
    <!--========== BEGIN .MODULE ==========-->
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
                <h3 class="title"><span class="bg-1">Rethinking</span></h3>
                <h3 class="subtitle">the 24h News Experience</h3>
              </div>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image17.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Breaking</strong> News Stories</a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-author"> <i class="fa fa-user"></i><a href="#">By Hugh Jackson</a> </div>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i>December 25, 2016 </div>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">At the 24h News we see breaking news events as unexpected, momentous or informative events...</a></p>
                  <p><a href="#" target="_blank" class="external-link">Our editors also consider other aspects of...</a></p>
                  <p><a href="#" target="_blank" class="external-link">Is this event part of a subject the 24h News often reports on?</a></p>
                  <div> <a href="#"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item --> 
              <!-- Begin .news-block" -->
              <div class="news-block">
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image21.jpg" alt=""></a></div>
                  <div class="item-content"><span class="day">Thu, Dec 12, 2016</span>
                    <p><a href="#" target="_blank" class="external-link">The energy choices</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image22.jpg" alt=""></a></div>
                  <div class="item-content"><span class="day">Thu, Dec 12, 2016</span>
                    <p><a href="#" target="_blank" class="external-link">Food/Agriculture</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image23.jpg" alt=""></a></div>
                  <div class="item-content"><span class="day">Thu, Dec 12, 2016</span>
                    <p><a href="#" target="_blank" class="external-link">Healthcare</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image24.jpg" alt=""></a></div>
                  <div class="item-content"><span class="day">Thu, Dec 12, 2016</span>
                    <p><a href="#" target="_blank" class="external-link">Housing/Construction</a></p>
                  </div>
                </div>
              </div>
              <!-- End .news-block" -->
              <div class="module-title">
                <h3 class="title"><span class="bg-4">Sport News</span></h3>
                <h3 class="subtitle">Watch the latest sport news</h3>
              </div>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image18.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Features</strong> & Analysis</a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-author"> <i class="fa fa-user"></i><a href="#">By Hugh Jackson</a> </div>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i>December 25, 2016 </div>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">From within the sport, the obligatory defence mechanism.</a></p>
                  <p><a href="#" target="_blank" class="external-link">From outside the tennis, the inevitable questions. But the broader question is this: where’s the line?</a> </p>
                  <p><a href="#" target="_blank" class="external-link">The player will face scrutiny use but you also ...</a></p>
                  <div> <a href="#"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <div class="news-block">
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image25.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i><span class="day"> 1h ago</span><a href="#">Cricket</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image26.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i><span class="day"> 54min ago</span><a href="#"> Football</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image27.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i><span class="day"> 6h ago</span><a href="#"> Rugby</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image28.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i><span class="day"> 1h ago</span><a href="#">Cycling</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image29.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 1h ago</span><a href="#">Soccer</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image30.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day">54min ago</span><a href="#">Soccer</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image31.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 6h ago</span><a href="#">Soccer</a></div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image10.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 1h ago</span><a href="#">Soccer</a></div>
                </div>
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
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image07.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>01</h3>
                      <p class="ellipsis"><a href="#">Survivor is world's oldest man - Guinness World Records.</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image08.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>02</h3>
                      <p class="ellipsis"><a href="#">East Kilbride: Manager Billy O. to leave post on 20 Dec.</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image09.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>03</h3>
                      <p class="ellipsis"><a href="#">Nature's way of saying older women aren't attractive?</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image10.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>04</h3>
                      <p class="ellipsis"><a href="#">Last post for stamp collectors,  valued in catalogues</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image11.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>05</h3>
                      <p class="ellipsis"><a href="#">The great age of the Post Office is past, here and indeed all over the ...</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image12.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>06</h3>
                      <p class="ellipsis"><a href="#"> World's biggest cruise ship is 'a construction site'.</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image13.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>07</h3>
                      <p class="ellipsis"><a href="#"> Zika: Experts call for Rio Olympic Games move over virus</a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image14.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>08</h3>
                      <p class="ellipsis"><a href="#"> Why do I need to sign in with my TV service provider information? </a></p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="item">
                    <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_370x185-image15.jpg" alt=""></a></div>
                    <div class="item-content">
                      <h3>09</h3>
                      <p class="ellipsis"><a href="#"> Your TV subscription is your key to watching TV online. </a></p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <!--========== END .SIDEBAR-POST ==========-->
             <!--========== BEGIN #WEATHER ==========-->
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
            <!--========== END  #WEATHER ==========--> 
          </div>
          <!--========== END .COL-MD-4 ==========--> 
        </div>
        <!--========== BEGIN 24H NEWS ON-AIR ==========--> 
          <!-- Begin .outer -->
          <div class="outer">
            <div class="breaking-ribbon">
              <h5>24h News On-Air</h5>
            </div>
            <!-- Begin .newsticker -->
            <div class="news-on-air">
              <ul>
                <li>
                  <h4><i class="fa fa-video-camera" aria-hidden="true"></i> <a href="#">Watch 24h News Streaming Live Online</a></h4>
                </li>
              </ul>
              <div class="navi">
                <button class="up"><i class="fa fa-caret-left"></i></button>
                <button class="down"><i class="fa fa-caret-right"></i></button>
              </div>
            </div>
            <!-- End .outer --> 
          </div>
          <!--========== END 24H NEWS ON-AIR ==========--> 
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
                <h3 class="title"><span class="bg-2">Health News</span></h3>
                <h3 class="subtitle">Watch the latest health news</h3>
              </div>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image19.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Global</strong> Health Priorities</a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-author"> <i class="fa fa-user"></i><a href="#">By Hugh Jackson</a> </div>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i>December 25, 2016 </div>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">Health has gained importance on the global agenda.</a></p>
                  <p><a href="#" target="_blank" class="external-link">In this article three issues are considered: global health policy actors, global health priorities...</a></p>
                  <p><a href="#" target="_blank" class="external-link">Robot performs surgery on soft tissue better than human hands.</a></p>
                  <div> <a href="#"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <div class="news-block">
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image32.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 20 Dec</span>
                    <p><a href="#">Health Daily Politics</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image33.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 15 Dec</span>
                    <p><a href="#">Our's Mental Health</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image34.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 13 Dec</span>
                    <p><a href="#">Health Magazine</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image35.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day"> 8 Dec</span>
                    <p><a href="#">Health Social Care</a> 
                  </p></div>
                </div>
              </div>
              <div class="module-title">
                <h3 class="title"><span class="bg-9">School Report</span></h3>
                <h3 class="subtitle">Watch the latest report news</h3>
              </div>
              <!-- Begin .item -->
              <div class="item">
                <div class="item-image-1"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image20.jpg" alt=""></a></div>
                <div class="item-content">
                  <div class="title-left title-style04 underline04">
                    <h3><a href="#"><strong>Improve</strong> pupils' concentration?</a></h3>
                  </div>
                  <br>
                  <div class="post-meta-elements">
                    <div class="post-meta-author"> <i class="fa fa-user"></i><a href="#">By Hugh Jackson</a> </div>
                    <div class="post-meta-date"> <i class="fa fa-calendar"></i>December 25, 2016 </div>
                  </div>
                  <p><a href="#" target="_blank" class="external-link">Mindfulness is a psychological technique which is said to help combat stress.</a></p>
                  <p><a href="#" target="_blank" class="external-link">According to the Mindfulness in School Project...</a></p>
                  <p><a href="#" target="_blank" class="external-link"> Sometimes these types of travel experiences are funded by savings...</a></p>
                  <div> <a href="#"><span class="read-more">Continue reading</span></a> </div>
                </div>
              </div>
              <!-- End .item -->
              <div class="news-block">
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image36.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day">16 Dec</span>
                    <p><a href="#">Our school bans energy</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image37.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day">16 Dec</span>
                    <p><a href="#">School report news day</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image38.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day">20 Dec</span>
                    <p><a href="#">School report news day</a></p>
                  </div>
                </div>
                <div class="item-block">
                  <div class="item-image"><a class="img-link" href="#"><img class="img-responsive img-full" src="img/index_800x400-image39.jpg" alt=""></a></div>
                  <div class="item-content"><i class="fa fa-clock-o"></i> <span class="day">22 Dec</span>
                    <p><a href="#">School report radio</a></p>
                  </div>
                </div>
              </div>
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
            <!--========== BEGIN EXCHANGE RATES==========--> 
            <!-- Begin .block-title-3 -->
            <div class="block-title-3">
              <h3>exchange rates</h3>
            </div>
            <!-- End .block-title-3 -->
            <div class="currency">
              <h3>Date<span id="cdate"></span></h3>
              <h5>Base USD</h5>
              <hr>
              <h3>EXCHANGE RATES</h3>
              <hr>
              <div class="rates">
                <div class="row">
                  <div class="col-xs-6 col-sm-6 col-md-6">
                    <h4>EUR<span id="euro"></span></h4>
                    <h4>GBP<span id="pound"></span></h4>
                    <h4>JPY<span id="yen"></span></h4>
                    <h4>CHF<span id="franc"></span></h4>
                  </div>
                  <div class="col-xs-6 col-sm-6 col-md-6">
                    <h4>CAD<span id="cdollar"></span></h4>
                    <h4>AUD<span id="adollar"></span></h4>
                    <h4>RON<span id="ron"></span></h4>
                    <h4>RUB<span id="rub"></span></h4>
                  </div>
                </div>
              </div>
            </div>
            <!--========== END EXCHANGE RATES==========--> 
            <!--========== BEGIN CURRENCY CONVERTER==========--> 
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
            <!--========== END CURRENCY CONVERTER ==========--> 
            <!--========== BEGIN .CALENDAR ==========-->
            <div id='calendar'></div>
            <!--========== END .CALENDAR ==========--> 
            <!--========== BEGIN #SIDEBAR-NEWSLETTER ==========-->
            <div id="sidebar-newsletter"> 
              <!-- Begin .title-style01 -->
              <div class="title-style01">
                <h3><strong>Newsletter</strong></h3>
              </div>
              <!-- End .title-style01 --> 
              <!-- Begin .sidebar-newsletter-form -->
              <div class="sidebar-newsletter-form">
                <form class="form-horizontal" action="include/subscribe.php" id="subscribeForm" method="POST">
                  <div class="input-group">
                    <input title="Newsletter" class="form-control" name="email" type="email" id="address" placeholder="Enter Your Email Address" data-validate="validate(required, email)" required="">
                    <span class="input-group-btn">
                    <button type="submit" class="btn btn-success">Subscribe</button>
                    </span> </div>
                </form>
                <span id="result" class="alertMsg"></span> </div>
              <!-- End .sidebar-newsletter-form -->  
            </div>
            <!--========== END #SIDEBAR-NEWSLETTER ==========--> 
            <!--========== BEGIN #SIDEBAR-SOCIAL-BUTTONS ==========-->
            <div class="sidebar-social-icons"> 
              <!-- Begin .title-style01 -->
              <div class="title-style01">
                <h3><strong>Stay</strong> Connected</h3>
              </div>
              <!-- End .title-style01 -->
              <ul>
                <li> <a href="#" class="facebook"><i class="fa fa-facebook"></i></a> </li>
                <li> <a href="#" class="youtube"><i class="fa fa-youtube"></i></a> </li>
                <li> <a href="#" class="twitter"><i class="fa fa-twitter"></i></a> </li>
                <li> <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a> </li>
                <li> <a href="#" class="pinterest"><i class="fa fa-pinterest"></i></a> </li>
                <li> <a href="#" class="google-plus"><i class="fa fa-google-plus"></i></a> </li>
                <li> <a href="#" class="rss"><i class="fa fa-rss"></i></a> </li>
                <li> <a href="#" class="tumblr"><i class="fa fa-tumblr"></i></a> </li>
              </ul>
            </div>
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
        <h3 class="carousel-title">Home Carousel</h3>
        <!-- End .carousel-title --> 
        <!-- Begin .gallery-slider owl-carousel -->
        <div id="small-gallery-slider" class="owl-carousel">
          <div class="small-gallery"> <img class="img-responsive" src="img/index_108x108_slider-image01.jpg" alt="">
            <div class="post-content"> <a href="watch-live.html">Watch Live 24/7</a>
              <p><a href="watch-live.html">What this generation's watching.</a></p>
              <i class="fa fa-clock-o"></i><span class="day"> Fri, 23 Dec, 2016</span> </div>
          </div>
          <div class="small-gallery"> <img class="img-responsive" src="img/index_108x108_slider-image02.jpg" alt="">
            <div class="post-content"> <a href="24-tv-radio.html">24 TV & Radio </a>
              <p><a href="24-tv-radio.html">Sport station could follow 24h3 in going off air.</a></p>
              <i class="fa fa-clock-o"></i><span class="day"> Fri, 23 Dec, 2016</span> </div>
          </div>
          <div class="small-gallery"> <img class="img-responsive" src="img/index_108x108_slider-image03.jpg" alt="">
            <div class="post-content"> <a href="web-shows.html">Web Shows </a>
              <p><a href="web-shows.html">Promoted Links From around the Web.</a></p>
              <i class="fa fa-clock-o"></i> <span class="day"> Fri, 23 Dec, 2016</span> </div>
          </div>
          <div class="small-gallery"> <img class="img-responsive" src="img/index_108x108_slider-image04.jpg" alt="">
            <div class="post-content"> <a href="24-news-store.html">News Store </a>
              <p><a href="24-news-store.html">Everything | Stories | Interactives | iReport |</a></p>
              <i class="fa fa-clock-o"></i> <span class="day"> Fri, 23 Dec, 2016</span> </div>
          </div>
          <div class="small-gallery"> <img class="img-responsive" src="img/index_108x108_slider-image05.jpg" alt="">
            <div class="post-content"> <a href="business.html">Business </a>
              <p><a href="business.html">the Co.hired advisers at investment bank </a></p>
              <i class="fa fa-clock-o"></i> <span class="day"> Fri, 23 Dec, 2016</span> </div>
          </div>
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