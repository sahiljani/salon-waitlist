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

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS staff (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            name VARCHAR(120) NOT NULL,\n            icon VARCHAR(20) DEFAULT 'user',\n            is_active TINYINT(1) NOT NULL DEFAULT 1,\n            created_at DATETIME DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4\n    ");
    echo "Table 'staff' created successfully!\n";

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS services (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            name VARCHAR(120) NOT NULL,\n            price DECIMAL(10,2) NOT NULL,\n            icon VARCHAR(20) DEFAULT 'scissors',\n            sort_order INT NOT NULL DEFAULT 0,\n            is_active TINYINT(1) NOT NULL DEFAULT 1,\n            created_at DATETIME DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4\n    ");
    echo "Table 'services' created successfully!\n";

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS sales (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            token_id INT NOT NULL,\n            staff_id INT NOT NULL,\n            subtotal DECIMAL(10,2) NOT NULL,\n            discount DECIMAL(10,2) NOT NULL DEFAULT 0,\n            tax DECIMAL(10,2) NOT NULL DEFAULT 0,\n            total DECIMAL(10,2) NOT NULL,\n            payment_method ENUM('CASH', 'UPI', 'CARD') NOT NULL DEFAULT 'CASH',\n            sale_date DATE NOT NULL,\n            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,\n            INDEX idx_sales_date (sale_date),\n            INDEX idx_sales_staff (staff_id),\n            INDEX idx_sales_token (token_id),\n            CONSTRAINT fk_sales_token FOREIGN KEY (token_id) REFERENCES tokens(id) ON DELETE RESTRICT,\n            CONSTRAINT fk_sales_staff FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4\n    ");
    echo "Table 'sales' created successfully!\n";

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS sale_items (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            sale_id INT NOT NULL,\n            service_id INT DEFAULT NULL,\n            item_name VARCHAR(120) NOT NULL,\n            qty INT NOT NULL DEFAULT 1,\n            unit_price DECIMAL(10,2) NOT NULL,\n            amount DECIMAL(10,2) NOT NULL,\n            is_custom TINYINT(1) NOT NULL DEFAULT 0,\n            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,\n            INDEX idx_sale_items_sale (sale_id),\n            INDEX idx_sale_items_service (service_id),\n            CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,\n            CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4\n    ");
    echo "Table 'sale_items' created successfully!\n";

    $pdo->exec("\n        INSERT INTO staff (name, icon)\n        SELECT * FROM (\n            SELECT 'Staff 1', 'user' UNION ALL\n            SELECT 'Staff 2', 'user' UNION ALL\n            SELECT 'Staff 3', 'user'\n        ) AS tmp\n        WHERE NOT EXISTS (SELECT 1 FROM staff LIMIT 1)\n    ");

    $pdo->exec("\n        INSERT INTO services (name, price, icon, sort_order)\n        SELECT * FROM (\n            SELECT 'Hair Cut', 150.00, 'scissors', 1 UNION ALL\n            SELECT 'Shave', 80.00, 'scissors', 2 UNION ALL\n            SELECT 'Hair Wash', 100.00, 'droplets', 3 UNION ALL\n            SELECT 'Facial', 300.00, 'sparkles', 4\n        ) AS tmp\n        WHERE NOT EXISTS (SELECT 1 FROM services LIMIT 1)\n    ");

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
}
