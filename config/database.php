<?php

$projectRoot = dirname(__DIR__);
$autoload = $projectRoot . '/vendor/autoload.php';

if (!is_file($autoload)) {
    die('Composer autoload not found. Run: composer install');
}

require_once $autoload;

if (!class_exists(\Dotenv\Dotenv::class)) {
    die('phpdotenv package not installed.');
}

if (!is_file($projectRoot . '/.env')) {
    die('.env file not found.');
}

\Dotenv\Dotenv::createImmutable($projectRoot)->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            try {
                $pdo = new PDO(
                    sprintf(
                        'mysql:host=%s;dbname=%s;charset=%s',
                        DB_HOST,
                        DB_NAME,
                        DB_CHARSET
                    ),
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return $pdo;
    }
}