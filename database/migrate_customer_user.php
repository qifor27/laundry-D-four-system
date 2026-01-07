<?php
/**
 * Migration: Customer-User Integration
 * Jalankan: php database/migrate_customer_user.php
 */

require_once __DIR__ . '/../config/database_mysql.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== Customer-User Integration Migration ===\n\n";

    // 1. Menambahkan kolom phone ke users
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) UNIQUE NULL AFTER name");
        echo "✅ Menambahkan kolom 'phone' ke tabel users\n";
    } else {
        echo "⏭️  Kolom 'phone' sudah ada di tabel users\n";
    }

    // 2. Menambahkan kolom user_id ke customers
    $stmt = $db->query("SHOW COLUMNS FROM customers LIKE 'user_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE customers ADD COLUMN user_id INT NULL AFTER id");
        echo "✅ Menambahkan kolom 'user_id' ke tabel customers\n";
    } else {
        echo "⏭️  Kolom 'user_id' sudah ada di tabel customers\n";
    }

    // 3. Menambahkan kolom registered_at ke customers
    $stmt = $db->query("SHOW COLUMNS FROM customers LIKE 'registered_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE customers ADD COLUMN registered_at TIMESTAMP NULL AFTER address");
        echo "✅ Menambahkan kolom 'registered_at' ke tabel customers\n";
    } else {
        echo "⏭️  Kolom 'registered_at' sudah ada di tabel customers\n";
    }

    // 4. Tambah foreign key (opsional, skip jika error)
    try {
        $db->exec("ALTER TABLE customers ADD CONSTRAINT fk_customer_user 
                   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "✅ Menambahkan foreign key constraint\n";
    } catch (Exception $e) {
        echo "⏭️  Foreign key sudah ada atau dilewati\n";
    }

    echo "\n🎉 Migration selesai!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>