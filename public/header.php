<?php require_once __DIR__ . '/include/content.php'; ?>
<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="description" content="24h News - Broadcast News TV Channel & News Magazine Template">
<meta name="author" content="Via-Theme">

<!-- Mobile Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Site Title  -->
<!-- <title><?= e(site_name()) ?> | <?= e(site_tagline() ?: 'University of Rwanda News') ?></title> -->

<!-- Favicon (matches the site logo set in Admin > Settings) -->
<link rel="shortcut icon" href="<?= e(logo_url()) ?>">
<link rel="icon" href="<?= e(logo_url()) ?>">

<!-- Web Fonts  -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Stylesheets -->
<?php
// Cache-buster: use each stylesheet's last-modified time so a new deploy
// automatically serves a fresh copy (defeats Cloudflare's long cache).
$cssVer = static function (string $file): string {
    $path = __DIR__ . '/css (1)/' . $file;
    return '?v=' . (is_file($path) ? filemtime($path) : '1');
};
?>
<link rel="stylesheet" href="css%20%281%29/bootstrap.min.css<?= $cssVer('bootstrap.min.css') ?>">
<link rel="stylesheet" href="css%20%281%29/main.css<?= $cssVer('main.css') ?>">
<link rel="stylesheet" href="css%20%281%29/style.css<?= $cssVer('style.css') ?>">
<link rel="stylesheet" href="css%20%281%29/colors.css<?= $cssVer('colors.css') ?>">
<link rel="stylesheet" href="css%20%281%29/responsive.css<?= $cssVer('responsive.css') ?>">
<link rel="stylesheet" href="css%20%281%29/jquery-ui.min.css<?= $cssVer('jquery-ui.min.css') ?>">
<link rel="stylesheet" href="css%20%281%29/weather-icons.min.css<?= $cssVer('weather-icons.min.css') ?>">
<link rel="stylesheet" href="css%20%281%29/kaminuza.css<?= $cssVer('kaminuza.css') ?>">
<?php
// Per-page stylesheets. A page sets $pageStyles = ['foo.css'] BEFORE requiring
// this header; every other page is untouched.
foreach (($pageStyles ?? []) as $sheet) {
    echo '<link rel="stylesheet" href="css%20%281%29/' . rawurlencode($sheet) . $cssVer($sheet) . '">' . "\n";
}
?>

<!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
</head>

<body>
<!-- <div id="pageloader">
  <div class="loader-item"> <img src="img/load.gif" alt='loader'> </div>
</div> -->
<!--========== BEGIN #WRAPPER ==========-->
<div id="wrapper" data-color="<?= e(setting('theme_color') ?: 'ick') ?>">
  <!--========== BEGIN #HEADER ==========-->
  <header id="header"> 
    <!-- Begin .top-menu -->
    <div class="top-menu"> 
      <!-- Begin .container -->
      <div class="container"> 
        <!-- Begin .left-top-menu -->
        <ul class="left-top-menu">
          <li> <a href="<?= e(setting('facebook') ?: '#') ?>" class="facebook"<?= setting('facebook') ? ' target="_blank"' : '' ?>><i class="fa fa-facebook"></i></a></li>
          <li> <a href="<?= e(setting('twitter') ?: '#') ?>" class="twitter"<?= setting('twitter') ? ' target="_blank"' : '' ?>><i class="fa fa-twitter"></i></a> </li>
          <li> <a href="<?= e(setting('youtube') ?: '#') ?>" class="youtube"<?= setting('youtube') ? ' target="_blank"' : '' ?>> <i class="fa fa-youtube"></i></a> </li>
          <li> <a href="<?= e(setting('linkedin') ?: '#') ?>" class="linkedin"<?= setting('linkedin') ? ' target="_blank"' : '' ?>><i class="fa fa-linkedin"></i></a> </li>
          <li> <a href="<?= e(setting('instagram') ?: '#') ?>" class="instagram"<?= setting('instagram') ? ' target="_blank"' : '' ?>> <i class="fa fa-instagram"></i></a> </li>
          <?php if (setting('contact_phone')): ?><li class="address"><a href="tel:<?= e(setting('contact_phone')) ?>"><i class="fa fa-phone"></i> <?= e(setting('contact_phone')) ?></a></li><?php endif; ?>
          <?php if (setting('contact_email')): ?><li class="address"><a href="mailto:<?= e(setting('contact_email')) ?>"><i class="fa fa-envelope-o"></i> <?= e(setting('contact_email')) ?></a></li><?php endif; ?>
        </ul>
        <!-- End .left-top-menu --> 
        <!-- Begin .right-top-menu -->
        <ul class="right-top-menu pull-right">
          <?php
          // Language switcher with flag image + full name (order: EN, RW, FR).
          // Flags live in public/img (tracked theme assets, so they deploy).
          $curLang = current_lang();
          $langLabels = [
              'en' => ['flag' => 'img/flag-en.png', 'name' => ''],
              'rw' => ['flag' => 'img/flag-rw.png', 'name' => ''],
              'fr' => ['flag' => 'img/flag-fr.png', 'name' => ''],
          ];
          foreach ($langLabels as $code => $l): ?>
          <li class="lang-switch"><a href="?lang=<?= $code ?>"<?= $curLang === $code ? ' class="active-lang"' : '' ?>><img class="lang-flag" src="<?= e($l['flag']) ?>" alt="<?= e($l['name']) ?>"> <?= $l['name'] ?></a></li>
          <?php endforeach; ?>
          <?php
          // Radio icon (plays ICK Radio on-site). Stream from Admin > Radio.
          // ICK Radio is not broadcasting yet, so with no stream URL the icon
          // still shows and opens a short "coming soon" notice instead.
          $hdrRadioUrl  = radio_url();
          $hdrRadioName = radio_name();
          $hdrRadioTitle = $hdrRadioUrl
              ? $hdrRadioName . ' — ' . t('listen_live')
              : $hdrRadioName . ' — ' . t('radio_coming_soon');
          ?>
          <li class="radio"><a href="<?= e($hdrRadioUrl ?: '#') ?>" class="kz-radio-link" data-radio="<?= e($hdrRadioUrl) ?>" data-radio-name="<?= e($hdrRadioName) ?>" title="<?= e($hdrRadioTitle) ?>"><i class="fa fa-microphone fa-i"></i></a></li>
          <li class="contact"><a href="contact" title="Contact us"><i class="fa fa-map-marker fa-i"></i></a></li>
          <li class="about"><a href="<?= e(ADMIN_BASE) ?>login" title="Staff Login"><i class="fa fa-user fa-i"></i></a> </li>
          <li>
            <?php // The search field stays closed until the magnifier is clicked (kz-search). ?>
            <form class="search-container kz-search" action="search" method="get" role="search">
              <button type="submit" class="search-icon-btn" style="background:none;border:0;" aria-label="Search" aria-expanded="false"> <span style="cursor:pointer"><i class="fa fa-search"></i></span> </button>
              <div class="search-input">
                <input type="search" name="q" class="search-bar" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= e(t('search_placeholder')) ?>" title="Search">
              </div>
            </form>
          </li>
        </ul>
        <!-- End .right-top-menu --> 
      </div>
      <!-- End .container --> 
    </div>
    <!-- End .top-menu --> 
    <!-- Begin .container -->
    <div class="container"> 
      <!-- Begin .header-logo -->
      <div class="header-logo"><a href="."><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
      <!-- End .header-logo --> 
      <!-- Begin .header-add-place (advert managed from Admin settings) -->
      <div class="header-add-place">
        <div class="desktop-add"><?= ad_slot('header', 'img/banner_728x90.jpg') ?></div>
      </div>
      <!-- End .header-add-place -->
      <!--========== BEGIN .NAVBAR #MOBILE-NAV ==========-->
      <nav class="navbar navbar-default" id="mobile-nav">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" id="sidenav-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          <div class="sidenav-header-logo"><a href="."><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
        </div>
        <div class="sidenav" data-sidenav="" data-sidenav-toggle="#sidenav-toggle">
          <button type="button" class="navbar-toggle active" data-toggle="collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          <div class="sidenav-brand">
            <div class="sidenav-header-logo"><a href="."><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
          </div>
          <ul class="sidenav-menu">
            <li><a href="." class="<?= ($GLOBALS['active_section'] ?? '') === '' ? 'active' : '' ?>"><?= e(t("home")) ?></a></li>
            <?php foreach (nav_top_sections() as $sec):
                $kids = nav_children((int) $sec['id']); ?>
              <li><a href="section?cat=<?= e($sec['slug']) ?>"><?= e(cat_name($sec)) ?></a>
                <?php if ($kids): ?>
                <div class="icon-sub-menu" data-sidenav-dropdown-toggle=""><span class="sidenav-dropdown-icon show" data-sidenav-dropdown-icon=""></span><span class="sidenav-dropdown-icon up-icon" data-sidenav-dropdown-icon=""></span></div>
                <ul class="sidenav-dropdown" data-sidenav-dropdown="">
                  <?php foreach ($kids as $kid): ?>
                  <li><a href="section?cat=<?= e($kid['slug']) ?>"><?= e(cat_name($kid)) ?></a></li>
                  <?php endforeach; ?>
                </ul>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
            <?php // External links: ICK News YouTube channel + the ICK website. ?>
            <?php foreach (nav_external_links() as $ext): ?>
            <li><a href="<?= e($ext['url']) ?>" target="_blank" rel="noopener"><i class="fa <?= e($ext['icon']) ?>"></i> <?= e($ext['label']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </nav>
      <!--========== END .NAVBAR #MOBILE-NAV ==========-->
    </div>
    <!-- End .container --> 
    <!--========== BEGIN .NAVBAR #FIXED-NAVBAR ==========-->
    <div class="navbar" id="fixed-navbar"> 
      <!--========== BEGIN MAIN-MENU .NAVBAR-COLLAPSE COLLAPSE #FIXED-NAVBAR-TOOGLE ==========-->
      <div class="main-menu nav navbar-collapse collapse" id="fixed-navbar-toggle"> 
        <!--========== BEGIN .CONTAINER ==========-->
        <div class="container"> 
          <!-- Begin .nav navbar-nav (built from the category tree) -->
          <ul class="nav navbar-nav">
            <li<?= ($GLOBALS['active_section'] ?? '') === '' ? ' class="active"' : '' ?>><a href="."><?= e(t("home")) ?></a></li>
            <?php foreach (nav_top_sections() as $sec):
                $kids = nav_children((int) $sec['id']);
                $isActive = ($GLOBALS['active_section'] ?? '') === $sec['slug'];
            ?>
              <?php if ($kids): ?>
              <li class="dropdown<?= $isActive ? ' active' : '' ?>">
                <a href="section?cat=<?= e($sec['slug']) ?>" class="dropdown-toggle" data-toggle="dropdown"><?= e(cat_name($sec)) ?></a>
                <ul class="dropdown-menu">
                  <?php foreach ($kids as $kid): ?>
                  <li><a href="section?cat=<?= e($kid['slug']) ?>"><?= e(cat_name($kid)) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
              <?php else: ?>
              <li<?= $isActive ? ' class="active"' : '' ?>><a href="section?cat=<?= e($sec['slug']) ?>"><?= e(cat_name($sec)) ?></a></li>
              <?php endif; ?>
            <?php endforeach; ?>
            <?php // External links: ICK News YouTube channel ("Videos"/"Amashusho") + the ICK website. ?>
            <?php foreach (nav_external_links() as $ext): ?>
            <li class="kz-nav-external"><a href="<?= e($ext['url']) ?>" target="_blank" rel="noopener"><i class="fa <?= e($ext['icon']) ?>"></i> <?= e($ext['label']) ?></a></li>
            <?php endforeach; ?>
          </ul>
          <!--========== END .NAV NAVBAR-NAV ==========-->
        </div>
        <!--========== END .CONTAINER ==========-->
      </div>
      <!--========== END MAIN-MENU .NAVBAR-COLLAPSE COLLAPSE #FIXED-NAVBAR-TOOGLE ==========-->
      <!--========== BEGIN .SECOND-MENU NAVBAR #NAV-BELOW-MAIN ==========-->
      <div class="second-menu navbar" id="nav-below-main"> 
        <!-- Begin .container -->
        <div class="container"> 
          <!-- Begin .collapse navbar-collapse -->
          <div class="collapse navbar-collapse nav-below-main"> 
            <!-- Begin .nav navbar-nav -->
            <ul class="nav navbar-nav">
              <?php
              // Radio link: use the Settings "quick_radio" URL if set, else fall
              // back to the first active radio stream (managed in Admin > Radio).
              // Defaults to "ICK Radio" — the station ICK plans to launch.
              $radioUrl   = radio_url();
              $radioLabel = radio_name();

              // Quick links: label stays, URL comes from Settings (falls back to
              // a sensible internal page). Edit the URLs in Admin > Settings.
              $quickLinks = [
                  $radioLabel          => $radioUrl ?: '#',
                  t('latest_news')     => setting('quick_latest')     ?: 'section?cat=news',
                  cat_name(category_by_slug('multimedia') ?: ['name' => 'Multimedia']) => setting('quick_multimedia') ?: 'section?cat=multimedia',
                  t('about_us')        => setting('quick_about')       ?: 'about',
              ];
              $first = true;
              foreach ($quickLinks as $label => $url):
                  // The first item is the radio link: it toggles the on-site
                  // player (and still carries the stream URL as a fallback).
                  // Shown even before the station goes live — the player then
                  // displays a "coming soon" notice instead of an audio stream.
                  if ($first):
                      $first = false; ?>
              <li><a href="<?= e($radioUrl ?: '#') ?>" class="kz-radio-link" data-radio="<?= e($radioUrl) ?>" data-radio-name="<?= e($radioLabel) ?>"><i class="fa fa-microphone"></i> <?= e($label) ?><?= $radioUrl ? '' : ' <span class="kz-radio-soon">(' . e(t('radio_coming_soon')) . ')</span>' ?></a></li>
              <?php else:
                  $first = false;
                  // External links (http) open in a new tab; internal pages don't.
                  $isExternal = preg_match('~^https?://~i', $url);
              ?>
              <li><a href="<?= e($url) ?>"<?= $isExternal ? ' target="_blank" rel="noopener"' : '' ?>><?= e($label) ?></a></li>
              <?php endif; endforeach; ?>
            </ul>
            <!-- End .nav navbar-nav --> 
          </div>
          <!-- End .collapse navbar-collapse --> 
          <!-- Begin .clock -->
          <div class="clock">
            <div id="time"></div>
            <div id="date"></div>
          </div>
          <!-- End .clock --> 
        </div>
        <!-- End .container --> 
      </div>
      <!--========== END .SECOND-MENU NAVBAR #NAV-BELOW-MAIN ==========--> 
    </div>
  </header>
  <!--========== END #HEADER ==========--> 
