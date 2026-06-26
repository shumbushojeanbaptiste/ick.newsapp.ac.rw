<!--========== BEGIN #FOOTER==========-->
  <footer id="footer"> 
    <!-- Begin .parallax -->
    <div id="parallax-section2">
      <div class="bg parallax2 overlay img-overlay2">
        <div class="container">
          <div class="row no-gutter">
            <div class="col-sm-6 col-md-3">
              <h3 class="title-left title-style03 underline03"><?= e(t("about_us")) ?></h3>
              <p class="about-us"><?= e(setting('about') ?: (site_name() . ' — ' . (site_tagline() ?: 'University of Rwanda news, stories and student voices.'))) ?></p>
              <div class="site-logo"><a href="."><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
            </div>
            <div class="col-sm-6 col-md-3">
              <h3 class="title-left title-style03 underline03"><?= e(t("latest_news")) ?></h3>
              <div class="footer-post">
                <ul>
                  <?php foreach (published_articles(4) as $fa): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($fa['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($fa) ?: 'img/index_370x185-image01.jpg') ?>" alt="<?= e(a_title($fa)) ?>"></a></div>
                      <div class="item-content">
                        <p class="ellipsis"><a href="article?slug=<?= e($fa['slug']) ?>"><?= e(a_title($fa)) ?></a></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <h3 class="title-left title-style03 underline03"><?= e(t("watch_listen")) ?></h3>
              <div class="footer-post">
                <ul>
                  <?php
                  // Multimedia stories (podcast/video) for the footer; fall back to
                  // editor's picks if the Multimedia section is still empty.
                  $mmCat = category_by_slug('multimedia');
                  [$mmList] = $mmCat ? published_in_category((int) $mmCat['id'], 4, 0) : [[]];
                  if (!$mmList) { $mmList = published_articles(4, 'editor_pick') ?: published_articles(4); }
                  foreach ($mmList as $mm): ?>
                  <li>
                    <div class="item">
                      <div class="item-image"><a class="img-link" href="article?slug=<?= e($mm['slug']) ?>"><img class="img-responsive img-full" src="<?= e(a_image($mm) ?: 'img/index_370x185-image15.jpg') ?>" alt="<?= e(a_title($mm)) ?>"></a></div>
                      <div class="item-content">
                        <p class="ellipsis"><a href="article?slug=<?= e($mm['slug']) ?>"><?= e(a_title($mm)) ?></a></p>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <h3 class="title-left title-style03 underline03"><?= e(t("sections")) ?></h3>
              <div class="tagcloud">
                <?php foreach (nav_top_sections() as $sec): ?>
                  <a href="section?cat=<?= e($sec['slug']) ?>"><?= e(cat_name($sec)) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End .parallax --> 
  </footer>
  <!--========== END #FOOTER==========--> 
  <!--========== BEGIN #COPYRIGHTS==========-->
  <div id="copyrights"> 
    <!-- Begin .container -->
    <div class="container"> 
      <!-- Begin .copyright -->
      <div class="copyright">
        &copy; <?= date('Y') ?>, <?= e(site_name()) ?>. All Rights Reserved.
        &nbsp;|&nbsp; <a href="<?= e(ADMIN_BASE) ?>login"><?= e(t("staff_login")) ?></a>
      </div>
      <!-- End .copyright -->
      <!--  Begin .footer-social-icons -->
      <div class="footer-social-icons">
        <ul>
          <li> <a href="<?= e(setting('facebook') ?: '#') ?>" class="facebook"<?= setting('facebook') ? ' target="_blank"' : '' ?>><i class="fa fa-facebook"></i></a> </li>
          <li> <a href="<?= e(setting('twitter') ?: '#') ?>" class="twitter"<?= setting('twitter') ? ' target="_blank"' : '' ?>><i class="fa fa-twitter"></i></a> </li>
          <li> <a href="<?= e(setting('youtube') ?: '#') ?>" class="youtube"<?= setting('youtube') ? ' target="_blank"' : '' ?>><i class="fa fa-youtube"></i></a> </li>
          <li> <a href="<?= e(setting('instagram') ?: '#') ?>" class="instagram"<?= setting('instagram') ? ' target="_blank"' : '' ?>><i class="fa fa-instagram"></i></a> </li>
          <li> <a href="<?= e(setting('linkedin') ?: '#') ?>" class="linkedin"<?= setting('linkedin') ? ' target="_blank"' : '' ?>><i class="fa fa-linkedin"></i></a> </li>
        </ul>
      </div>
      <!--  End .footer-social-icons --> 
    </div>
    <!-- End .container --> 
  </div>
  <!--========== END #COPYRIGHTS==========-->
</div>

<!--========== END #WRAPPER ==========-->

<?php
// On-site radio player (hidden until the header "Salus Radio" link is clicked).
// Stream URL + name come from Admin > Radio (the first active stream).
$kzStreams = active_streams();
$kzRadioUrl  = setting('quick_radio') ?: ($kzStreams[0]['stream_url'] ?? '');
$kzRadioName = $kzStreams[0]['name'] ?? 'Salus Radio';
if ($kzRadioUrl):
?>
<div id="kz-radio-player" class="kz-radio-player" data-url="<?= e($kzRadioUrl) ?>" hidden>
  <div class="kz-radio-player__bar">
    <span class="kz-radio-player__live"><i class="fa fa-microphone"></i> <?= e(t('on_air_now')) ?></span>
    <span class="kz-radio-player__name"><?= e($kzRadioName) ?></span>
    <audio id="kz-radio-audio" class="kz-radio-player__audio" controls preload="none">
      <source src="<?= e($kzRadioUrl) ?>">
    </audio>
    <a class="kz-radio-player__open" href="<?= e($kzRadioUrl) ?>" target="_blank" rel="noopener" title="Open in new tab"><i class="fa fa-external-link"></i></a>
    <button type="button" class="kz-radio-player__close" aria-label="Close">&times;</button>
  </div>
</div>
<?php endif; ?>


<!-- External JavaScripts --> 
<script src="js/jquery-3.1.1.min.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/jquery-ui.min.js"></script> 
<script src="js/plugins.js"></script> 
<!-- JavaScripts -->
<script src="js/functions.js"></script>
<script src="js/kaminuza.js?v=<?= is_file(__DIR__ . '/js/kaminuza.js') ? filemtime(__DIR__ . '/js/kaminuza.js') : '5' ?>"></script>
</body>
</html>