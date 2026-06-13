<?php
/**
 * UR KAMINUZA - Shared database connection (single source of truth).
 *
 * Both the Admin (Admin_Templete) and the public site (Web_Templete) include
 * this file so the database credentials live in ONE place. If the password or
 * host ever changes, edit it here only.
 *
 * It defines the DB_* constants and a shared db() PDO singleton. Everything
 * else (app helpers, settings, CSRF, ...) stays in each side's own config.
 */

// ---- Database credentials (XAMPP defaults) ----
if (!defined('DB_HOST'))    { define('DB_HOST', '127.0.0.1'); }
if (!defined('DB_NAME'))    { define('DB_NAME', 'KAMINUZA'); }
if (!defined('DB_USER'))    { define('DB_USER', 'root'); }
if (!defined('DB_PASS'))    { define('DB_PASS', ''); }
if (!defined('DB_CHARSET')) { define('DB_CHARSET', 'utf8mb4'); }

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
