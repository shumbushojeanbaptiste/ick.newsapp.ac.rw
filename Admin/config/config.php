<?php
/**
 * ICK News - Application configuration & PDO connection.
 *
 * Every admin page should start with:  require_once __DIR__ . '/config/config.php';
 * (adjust the relative path with how deep the page is).
 */

// ---- Error reporting (development). Turn display_errors off in production. ----
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---- Database connection (shared, single source of truth) ----
// Defines DB_* constants and the db() PDO singleton used across the admin.
require_once __DIR__ . '/../../config/database.php';

// ---- App constants ----
define('APP_NAME', 'ICK News');
// Absolute filesystem path to the Admin folder.
define('BASE_PATH', dirname(__DIR__));
// Folder (relative to Admin) where uploaded images go.
define('UPLOAD_DIR', BASE_PATH . '/uploads');

/**
 * Web base URL path of the Admin root, always with a trailing slash,
 * e.g. "/KAMINUZA/Admin/". Derived from the running script so it works
 * no matter which subfolder (articles/, categories/, ...) the request is in.
 */
function app_base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    // Filesystem path of the document root and of the app root.
    $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));
    $appRoot = str_replace('\\', '/', BASE_PATH);

    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        // App lives under the document root: take the remainder as the URL path.
        $base = substr($appRoot, strlen($docRoot));
    } else {
        // Fallback: derive from the current script's URL by stripping known subdirs.
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
        $dir = rtrim(dirname($script), '/');
        // If we're in a known subfolder, climb up one level to the app root.
        foreach (['/articles', '/categories', '/users', '/radio', '/config'] as $sub) {
            if (substr($dir, -strlen($sub)) === $sub) {
                $dir = substr($dir, 0, -strlen($sub));
                break;
            }
        }
        $base = $dir;
    }
    $base = '/' . trim($base, '/');
    return $base = ($base === '/' ? '/' : $base . '/');
}

// Supported content languages.
$GLOBALS['LANGUAGES'] = ['rw' => 'Kinyarwanda', 'fr' => 'French', 'en' => 'English'];

// db() (the shared PDO singleton) is provided by config/database.php above.

// ---- Small helpers used across the admin ----

/** Escape output for safe HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Site settings (key/value). Loaded once and cached for the request.
 * setting('contact_email') returns the value or the given default.
 */
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
            $cache = []; // settings table may not exist yet
        }
    }
    return $cache;
}

function setting(string $key, ?string $default = ''): ?string
{
    $all = all_settings();
    return $all[$key] ?? $default;
}

/**
 * Logo URL from Settings, falling back to the bundled template logo.
 * $variant: 'dark' (default) or 'light'. The uploaded logo is used for both;
 * only the fallback differs by variant.
 */
function logo_url(string $variant = 'dark'): string
{
    $logo = setting('logo', '');
    if ($logo) {
        return $logo;
    }
    return $variant === 'light' ? 'assets/images/logo-light.png' : 'assets/images/logo-dark.png';
}

/** Company name from Settings, falling back to APP_NAME. */
function site_name(): string
{
    $name = setting('company_name', '');
    return $name !== '' ? $name : APP_NAME;
}

/** Redirect helper. */
function redirect(string $path): void
{
    // Resolve relative app paths (e.g. "articles/index.php") to an absolute URL
    // path so the browser navigates cleanly regardless of the current folder or
    // any <base href> on the page.
    if (!preg_match('~^(https?:)?//~i', $path) && $path[0] !== '/') {
        $path = app_base_url() . ltrim($path, '/');
    }
    header('Location: ' . $path);
    exit;
}

/** Build a URL-friendly slug from any string. */
function slugify(string $text): string
{
    $text = trim($text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = strtolower(trim($text, '-'));
    return $text !== '' ? $text : substr(md5((string) microtime(true)), 0, 10);
}

/** One-time flash messages stored in session. */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

// ---- CSRF protection ----
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        // 403 (standard) rather than 419 (non-standard, which some SAPIs turn into 500).
        http_response_code(403);
        die('Invalid or expired CSRF token. Please go back and try again.');
    }
}
