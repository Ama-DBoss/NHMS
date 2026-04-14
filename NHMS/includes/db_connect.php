<?php
// Security: Set environment variables or use defaults for production
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'nigerian_hospital_registry';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '3306';
$charset = 'utf8mb4';

// Build DSN
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 10,
];

// Create connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error securely - don't expose in production
    error_log($e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    die('Database connection failed. Please try again later.');
}
