<?php
/**
 * Shared admin header. Pages should set $pageTitle (optional) and then:
 *     require_once __DIR__ . '/header.php';
 * It bootstraps auth, enforces login, and opens the layout wrapper.
 */
require_once __DIR__ . '/config/auth.php';
require_login();

$me = current_user();
$pageTitle = $pageTitle ?? 'Dashboard';

/*
 * $basePath lets pages in subfolders (articles/, categories/, ...) reuse this
 * layout. A page sets it to '../' before including header.php; root pages leave
 * it as ''. All asset/link references below are relative to it via <base>.
 */
$basePath = $basePath ?? '';
$GLOBALS['BASE_URL'] = $basePath;
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8">
    <base href="<?= e($basePath === '' ? './' : $basePath) ?>">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="UR KAMINUZA News Platform" name="description">
    <link rel="shortcut icon" href="<?= e(logo_url('dark')) ?>">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css">
    <?php if (!empty($pageStyles)) { echo $pageStyles; } ?>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="index" class="logo logo-dark">
                                <span class="logo-sm"><img src="<?= e(logo_url('dark')) ?>" alt="" height="28"></span>
                                <span class="logo-lg"><img src="<?= e(logo_url('dark')) ?>" alt="" height="30"></span>
                            </a>
                            <a href="index" class="logo logo-light">
                                <span class="logo-sm"><img src="<?= e(logo_url('light')) ?>" alt="" height="28"></span>
                                <span class="logo-lg"><img src="<?= e(logo_url('light')) ?>" alt="" height="30"></span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                            <span class="hamburger-icon"><span></span><span></span><span></span></span>
                        </button>

                        <h4 class="mb-0 fw-semibold ms-2 d-none d-md-block align-self-center"><?= e(site_name()) ?></h4>
                    </div>

                    <div class="d-flex align-items-center">

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user object-fit-cover" src="<?= e(avatar_url($me)) ?>" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?= e($me['name']) ?></span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"><?= e($me['role_label']) ?></span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Welcome <?= e($me['name']) ?>!</h6>
                                <a class="dropdown-item" href="profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Logout</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

<?php include __DIR__ . '/sidebar.php'; ?>
