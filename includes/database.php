<?php
// Load environment variables if not already loaded
if (!isset($_ENV['DB_HOST'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'warkop_qr';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

// Make $conn available globally
global $conn;

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=$charset",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    $env = $_ENV['APP_ENV'] ?? 'development';
    if ($env === 'production') {
        die('Database connection error. Please contact administrator.');
    } else {
        die('Database connection failed: ' . $e->getMessage());
    }
}
