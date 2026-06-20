<?php
require_once '../includes/db.php';

echo "<h2>Stockara Database Synchronization</h2>";

$tables = [
    "businesses" => "CREATE TABLE IF NOT EXISTS businesses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        logo VARCHAR(255),
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(100),
        currency VARCHAR(10) DEFAULT '₦',
        tax_rate DECIMAL(5,2) DEFAULT 0.00,
        receipt_footer TEXT,
        subscription_plan_id INT DEFAULT 1,
        subscription_status ENUM('Active', 'Expired', 'Trial', 'Cancelled') DEFAULT 'Trial',
        subscription_expiry DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "users" => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        role ENUM('Admin', 'Manager', 'Cashier', 'Technician') DEFAULT 'Cashier',
        email VARCHAR(100),
        status TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "subscription_plans" => "CREATE TABLE IF NOT EXISTS subscription_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        duration_days INT NOT NULL,
        max_products INT DEFAULT -1,
        max_users INT DEFAULT -1,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "subscriptions" => "CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT,
        plan_id INT,
        amount_paid DECIMAL(10,2) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        payment_reference VARCHAR(100),
        payment_status ENUM('Paid', 'Pending', 'Failed') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "super_admins" => "CREATE TABLE IF NOT EXISTS super_admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "contact_messages" => "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('Unread', 'Read', 'Replied') DEFAULT 'Unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "activity_logs" => "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT,
        user_id INT,
        action VARCHAR(255),
        module VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✔ Table <b>$name</b> is ready.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✘ Error creating $name: " . $e->getMessage() . "</p>";
    }
}

echo "<hr><p>Done! You can now access your Super Admin portal features.</p>";
echo "<a href='dashboard.php'>Go to Dashboard</a>";
?>
