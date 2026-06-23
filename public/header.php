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

<!-- Favicon -->
<link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">

<!-- Web Fonts  -->
<link href="css?family=Roboto+Condensed%7CRoboto+Slab:300,400,700%7CRoboto:300,400,500,700" rel="stylesheet"> 

<!-- Stylesheets -->
<link rel="stylesheet" href="css%20%281%29/bootstrap.min.css">
<link rel="stylesheet" href="css%20%281%29/main.css">
<link rel="stylesheet" href="css%20%281%29/style.css">
<link rel="stylesheet" href="css%20%281%29/colors.css">
<link rel="stylesheet" href="css%20%281%29/responsive.css">
<link rel="stylesheet" href="css%20%281%29/jquery-ui.min.css">
<link rel="stylesheet" href="css%20%281%29/weather-icons.min.css">
<link rel="stylesheet" href="css%20%281%29/kaminuza.css">

<!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
</head>

<body>
<!-- <div id="pageloader">
  <div class="loader-item"> <img src="img/load.gif" alt='loader'> </div>
</div> -->
<!--========== BEGIN #WRAPPER ==========-->
<div id="wrapper" data-color="blue"> 
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
          // Language switcher with flag + full name. Picks the site language,
          // which persists in the session and filters content to that language.
          $curLang = current_lang();
          $langLabels = [
              'rw' => ['flag' => '🇷🇼', 'name' => 'KINYARWANDA'],
              'en' => ['flag' => '🇬🇧', 'name' => 'ENGLISH'],
              'fr' => ['flag' => '🇫🇷', 'name' => 'FRANCAIS'],
          ];
          foreach ($langLabels as $code => $l): ?>
          <li class="lang-switch"><a href="?lang=<?= $code ?>"<?= $curLang === $code ? ' class="active-lang"' : '' ?>><span class="lang-flag"><?= $l['flag'] ?></span> <?= $l['name'] ?></a></li>
          <?php endforeach; ?>
          <li class="contact"><a href="contact" title="Contact us"><i class="fa fa-map-marker fa-i"></i></a></li>
          <li class="about"><a href="<?= e(ADMIN_BASE) ?>login.php" title="Staff Login"><i class="fa fa-user fa-i"></i></a> </li>
          <li>
            <form class="search-container" action="search" method="get" role="search">
              <button type="submit" class="search-icon-btn" style="background:none;border:0;"> <span style="cursor:pointer"><i class="fa fa-search"></i></span> </button>
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
      <div class="header-logo"><a href="index.php"><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
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
          <div class="sidenav-header-logo"><a href="index.php"><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
        </div>
        <div class="sidenav" data-sidenav="" data-sidenav-toggle="#sidenav-toggle">
          <button type="button" class="navbar-toggle active" data-toggle="collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          <div class="sidenav-brand">
            <div class="sidenav-header-logo"><a href="index.php"><img src="<?= e(logo_url()) ?>" alt="<?= e(site_name()) ?>"></a></div>
          </div>
          <ul class="sidenav-menu">
            <li><a href="index.php" class="<?= ($GLOBALS['active_section'] ?? '') === '' ? 'active' : '' ?>"><?= e(t("home")) ?></a></li>
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
            <li<?= ($GLOBALS['active_section'] ?? '') === '' ? ' class="active"' : '' ?>><a href="index.php"><?= e(t("home")) ?></a></li>
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
              // Quick links: label stays, URL comes from Settings (falls back to
              // a sensible internal page). Edit the URLs in Admin > Settings.
              $quickLinks = [
                  'Salus Radio'        => setting('quick_radio')      ?: '#',
                  t('latest_news')     => setting('quick_latest')     ?: 'section?cat=news',
                  cat_name(category_by_slug('multimedia') ?: ['name' => 'Multimedia']) => setting('quick_multimedia') ?: 'section?cat=multimedia',
                  t('about_us')        => setting('quick_about')       ?: 'page?slug=about-us',
              ];
              foreach ($quickLinks as $label => $url): ?>
              <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
              <?php endforeach; ?>
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