<?php
require_once 'config.php';

try {
    $pdo = getDB();

    // Create tokens table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token_no INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            status ENUM('WAITING', 'SERVING', 'DONE', 'NO_SHOW') DEFAULT 'WAITING',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            date DATE NOT NULL,
            INDEX idx_date_status (date, status),
            INDEX idx_date_token (date, token_no)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    echo "Table 'tokens' created successfully!\n";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
}
