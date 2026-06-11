<?php
/**
 * UR KAMINUZA - Application configuration & PDO connection.
 *
 * Every admin page should start with:  require_once __DIR__ . '/config/config.php';
 * (adjust the relative path with how deep the page is).
 */

// ---- Error reporting (development). Turn display_errors off in production. ----
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---- Database credentials (XAMPP defaults) ----
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'KAMINUZA');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---- App constants ----
define('APP_NAME', 'UR KAMINUZA');
// Absolute filesystem path to the Admin_Templete folder.
define('BASE_PATH', dirname(__DIR__));
// Folder (relative to Admin_Templete) where uploaded images go.
define('UPLOAD_DIR', BASE_PATH . '/uploads');

/**
 * Web base URL path of the Admin_Templete root, always with a trailing slash,
 * e.g. "/KAMINUZA/Admin_Templete/". Derived from the running script so it works
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

/**
 * Shared PDO instance (singleton).
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

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
