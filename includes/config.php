<?php
// Load environment variables if not already loaded
if (!isset($_ENV['APP_URL'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    use Dotenv\Dotenv;
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

define('BASE_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'));
define('BASE_PATH', dirname(__DIR__) . '/');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Set error reporting based on environment
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}