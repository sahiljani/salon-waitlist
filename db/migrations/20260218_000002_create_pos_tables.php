<?php

return [
    'up' => [
        "CREATE TABLE IF NOT EXISTS staff (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            icon VARCHAR(20) DEFAULT 'user',
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            icon VARCHAR(20) DEFAULT 'scissors',
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token_id INT NOT NULL,
            staff_id INT NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            discount DECIMAL(10,2) NOT NULL DEFAULT 0,
            tax DECIMAL(10,2) NOT NULL DEFAULT 0,
            total DECIMAL(10,2) NOT NULL,
            payment_method ENUM('CASH', 'UPI', 'CARD') NOT NULL DEFAULT 'CASH',
            sale_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sales_date (sale_date),
            INDEX idx_sales_staff (staff_id),
            INDEX idx_sales_token (token_id),
            CONSTRAINT fk_sales_token FOREIGN KEY (token_id) REFERENCES tokens(id) ON DELETE RESTRICT,
            CONSTRAINT fk_sales_staff FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS sale_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_id INT NOT NULL,
            service_id INT DEFAULT NULL,
            item_name VARCHAR(120) NOT NULL,
            qty INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            is_custom TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sale_items_sale (sale_id),
            INDEX idx_sale_items_service (service_id),
            CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            CONSTRAINT fk_sale_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT INTO staff (name, icon)
        SELECT * FROM (
            SELECT 'Staff 1', 'user' UNION ALL
            SELECT 'Staff 2', 'user' UNION ALL
            SELECT 'Staff 3', 'user'
        ) AS tmp
        WHERE NOT EXISTS (SELECT 1 FROM staff LIMIT 1)",

        "INSERT INTO services (name, price, icon, sort_order)
        SELECT * FROM (
            SELECT 'Hair Cut', 150.00, 'scissors', 1 UNION ALL
            SELECT 'Shave', 80.00, 'scissors', 2 UNION ALL
            SELECT 'Hair Wash', 100.00, 'droplets', 3 UNION ALL
            SELECT 'Facial', 300.00, 'sparkles', 4
        ) AS tmp
        WHERE NOT EXISTS (SELECT 1 FROM services LIMIT 1)"
    ],
    'down' => [
        "DROP TABLE IF EXISTS sale_items",
        "DROP TABLE IF EXISTS sales",
        "DROP TABLE IF EXISTS services",
        "DROP TABLE IF EXISTS staff"
    ]
];
