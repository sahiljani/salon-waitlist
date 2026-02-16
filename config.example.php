<?php
// Database configuration
// Copy this file to config.php and fill in your hPanel MySQL details
define('DB_HOST', 'localhost');          // hPanel uses 'localhost'
define('DB_NAME', 'your_database_name'); // From hPanel > Databases > MySQL
define('DB_USER', 'your_database_user'); // From hPanel > Databases > MySQL
define('DB_PASS', 'your_database_pass'); // The password you set

// Create connection
function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

// Get today's date
function getToday() {
    return date('Y-m-d');
}

// Format token number
function formatToken($num) {
    return 'T-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}
