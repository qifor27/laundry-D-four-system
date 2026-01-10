<?php
/**
 * Migration: Payment Methods & Payments Tables
 * 
 * Jalankan file ini untuk membuat tabel yang dibutuhkan.
 * php database/migrate_payments.php
 */

require_once __DIR__ . '/../config/database_mysql.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting Payment Methods migration...\n";
    echo "=====================================\n\n";
    
    // 1. Create payment_methods table
    $db->exec("
        CREATE TABLE IF NOT EXISTS payment_methods (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            type ENUM('cash', 'bank_transfer') NOT NULL,
            bank_name VARCHAR(50) NULL,
            account_number VARCHAR(30) NULL,
            account_holder VARCHAR(100) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Table 'payment_methods' created\n";
    
    // 2. Insert default payment methods
    $stmt = $db->query("SELECT COUNT(*) FROM payment_methods");
    if ($stmt->fetchColumn() == 0) {
        $db->exec("
            INSERT INTO payment_methods (name, type, bank_name, account_number, account_holder) VALUES
            ('Cash', 'cash', NULL, NULL, NULL),
            ('Transfer BCA', 'bank_transfer', 'BCA', '1234567890', 'D''four Laundry')
        ");
        echo "✓ Default payment methods inserted\n";
    } else {
        echo "- Payment methods already exist, skipping insert\n";
    }
    
    // 3. Create payments table
    $db->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            transaction_id INT NOT NULL,
            payment_method_id INT NOT NULL,
            amount DECIMAL(12, 2) NOT NULL,
            status ENUM('unpaid', 'pending', 'paid', 'cancelled') DEFAULT 'unpaid',
            paid_at TIMESTAMP NULL,
            confirmed_by INT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Table 'payments' created\n";
    
    // 4. Add payment columns to transactions (if not exists)
    $columns = $db->query("SHOW COLUMNS FROM transactions LIKE 'payment_status'")->fetchAll();
    if (count($columns) == 0) {
        $db->exec("
            ALTER TABLE transactions 
            ADD COLUMN payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid' AFTER status,
            ADD COLUMN payment_method_id INT NULL AFTER payment_status
        ");
        echo "✓ Payment columns added to 'transactions'\n";
    } else {
        echo "- Payment columns already exist in transactions, skipping\n";
    }
    
    echo "\n=====================================\n";
    echo "✅ Migration completed successfully!\n";
    
    // Show current payment methods
    echo "\n📋 Current Payment Methods:\n";
    $stmt = $db->query("SELECT * FROM payment_methods");
    $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($methods as $m) {
        echo "   - [{$m['id']}] {$m['name']}";
        if ($m['bank_name']) {
            echo " ({$m['bank_name']} - {$m['account_number']})";
        }
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
