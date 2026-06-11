<?php
/**
 * Shared admin sidebar. Menu items are filtered by the current user's permissions.
 * Set $activeMenu in a page (e.g. 'articles') to highlight the current section.
 */
if (!function_exists('current_role')) {
    require_once __DIR__ . '/config/auth.php';
}
$activeMenu = $activeMenu ?? '';
$isActive = static fn(string $key): string => $activeMenu === $key ? ' active' : '';
?>
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index.php" class="logo logo-dark">
            <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="" height="22"></span>
            <span class="logo-lg"><img src="<?= e(logo_url('dark')) ?>" alt="" height="40"></span>
        </a>
        <a href="index.php" class="logo logo-light">
            <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="" height="22"></span>
            <span class="logo-lg"><img src="<?= e(logo_url('light')) ?>" alt="" height="40"></span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="menu-title"><span>Menu</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('dashboard') ?>" href="index.php">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span>News</span></li>

                <?php if (can('view_articles')): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('articles') ?>" href="articles/index.php">
                        <i class="ri-article-line"></i> <span>Articles</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('create_article')): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('article-new') ?>" href="articles/edit.php">
                        <i class="ri-add-circle-line"></i> <span>New Article</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('manage_categories')): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('categories') ?>" href="categories/index.php">
                        <i class="ri-price-tag-3-line"></i> <span>Categories</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('manage_radio')): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('radio') ?>" href="radio/index.php">
                        <i class="ri-radio-line"></i> <span>Radio Streams</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('manage_users')): ?>
                <li class="menu-title"><i class="ri-more-fill"></i> <span>Administration</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('users') ?>" href="users/index.php">
                        <i class="ri-team-line"></i> <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link<?= $isActive('settings') ?>" href="settings/index.php">
                        <i class="ri-settings-3-line"></i> <span>Settings</span>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<div class="vertical-overlay"></div>
