<?php
/**
 * UR KAMINUZA - Shared database connection (single source of truth).
 *
 * Both the Admin (Admin/) and the public site (Web/) include
 * this file so the database credentials live in ONE place. If the password or
 * host ever changes, edit it here only.
 *
 * It defines the DB_* constants and a shared db() PDO singleton. Everything
 * else (app helpers, settings, CSRF, ...) stays in each side's own config.
 *
 * Credentials are read from the project-root .env file (via phpdotenv) so they
 * are never hardcoded or committed. If .env / vendor is missing, sensible XAMPP
 * defaults are used so the app still runs.
 */

// ---- Load credentials from .env (if available) ----
$projectRoot = dirname(__DIR__);
$autoload    = $projectRoot . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
    if (class_exists(\Dotenv\Dotenv::class) && is_file($projectRoot . '/.env')) {
        // Safe loader: doesn't overwrite existing env vars, won't throw if absent.
        \Dotenv\Dotenv::createImmutable($projectRoot)->safeLoad();
    }
}

/** Read an env var ($_ENV / getenv) with a fallback default. */
function env_value(string $key, string $default = ''): string
{
    $v = $_ENV[$key] ?? getenv($key);
    return ($v === false || $v === null || $v === '') ? $default : (string) $v;
}

// ---- Database credentials (from .env, falling back to XAMPP defaults) ----
if (!defined('DB_HOST'))    { define('DB_HOST', env_value('DB_HOST', '127.0.0.1')); }
if (!defined('DB_NAME'))    { define('DB_NAME', env_value('DB_NAME', 'KAMINUZA')); }
if (!defined('DB_USER'))    { define('DB_USER', env_value('DB_USER', 'root')); }
if (!defined('DB_PASS'))    { define('DB_PASS', env_value('DB_PASS', '')); }
if (!defined('DB_CHARSET')) { define('DB_CHARSET', env_value('DB_CHARSET', 'utf8mb4')); }

if (!function_exists('db')) {
    /** Shared PDO instance (singleton). */
    function db(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
        }
        return $pdo;
    }
}
