<?php
/**
 * UR KAMINUZA - Public website (front-end) configuration.
 *
 * Connects to the SAME `KAMINUZA` database the Admin writes to, and exposes
 * small read-only helpers the public pages use (settings, logo, category tree,
 * published articles). Keep this in sync with Admin_Templete/config/config.php.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');   // turn off in production

// ---- Database connection (shared, single source of truth) ----
// Provides the DB_* constants and the db() PDO singleton.
require_once __DIR__ . '/../../config/database.php';

// Supported content languages.
$GLOBALS['LANGUAGES'] = ['rw' => 'Kinyarwanda', 'fr' => 'French', 'en' => 'English'];

/**
 * Path (URL) from the public site to the Admin_Templete folder, used to reach
 * uploaded files (logos, article images) which are stored relative to the Admin.
 * Both folders live side by side under the project root.
 */
define('ADMIN_BASE', '../Admin_Templete/');

/** Escape output for safe HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* ---------------------------------------------------------------------------
 * Site settings (key/value), loaded once per request.
 * ------------------------------------------------------------------------- */
function all_settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            $cache = [];
        }
    }
    return $cache;
}

function setting(string $key, ?string $default = ''): ?string
{
    return all_settings()[$key] ?? $default;
}

/** Company / site name from Settings. */
function site_name(): string
{
    $n = setting('company_name', '');
    return $n !== '' ? $n : 'UR KAMINUZA';
}

/** Site tagline from Settings. */
function site_tagline(): string
{
    return setting('tagline', '') ?: '';
}

/**
 * Public URL of the site logo from Settings. The stored path is relative to the
 * Admin folder, so we prefix it with ADMIN_BASE. Falls back to the theme logo.
 */
function logo_url(): string
{
    $logo = setting('logo', '');
    if ($logo) {
        // Absolute URL/path stays as-is; otherwise resolve against the Admin folder.
        if (preg_match('~^(https?:)?//~i', $logo) || $logo[0] === '/') {
            return $logo;
        }
        return ADMIN_BASE . ltrim($logo, '/');
    }
    return 'img/logo.png';   // bundled theme fallback
}

/**
 * Public URL for an uploaded file path stored by the Admin (article images,
 * etc.). Returns '' for empty input.
 */
function upload_url(?string $path): string
{
    if (!$path) {
        return '';
    }
    // Absolute URL or root-relative path: use as-is.
    if (preg_match('~^(https?:)?//~i', $path) || $path[0] === '/') {
        return $path;
    }
    // Theme/public assets bundled with the public site (e.g. "img/...") are
    // already relative to the public root, so use them directly.
    if (strncmp($path, 'img/', 4) === 0) {
        return $path;
    }
    // Otherwise it's an Admin-stored upload, relative to the Admin folder.
    return ADMIN_BASE . ltrim($path, '/');
}

/**
 * Render an advert slot from settings, e.g. ad_slot('header', 'img/banner_728x90.jpg').
 * Reads settings "<slot>_ad_image" and "<slot>_ad_link". If no image is set it
 * falls back to the theme's bundled placeholder so the layout is preserved.
 *
 * The image is wrapped in a fixed-size box (class "kz-ad kz-ad--<slot>") so that
 * ANY uploaded image is cropped to fit that exact slot via CSS (object-fit:cover),
 * keeping the design intact regardless of the uploaded image's dimensions.
 */
function ad_slot(string $slot, string $fallbackImg = ''): string
{
    $img  = setting($slot . '_ad_image');
    $link = setting($slot . '_ad_link');
    $src  = $img ? upload_url($img) : $fallbackImg;
    if (!$src) {
        return '';
    }
    $target = $link ? ' target="_blank"' : '';
    return '<a class="kz-ad kz-ad--' . e($slot) . '" href="' . e($link ?: '#') . '"' . $target . '>'
        . '<img src="' . e($src) . '" alt="Advertisement"></a>';
}
