<?php
/**
 * MySQL Database Schema Initialization
 * Run this file once to create the database tables for MySQL
 */

require_once __DIR__ . '/../config/database_mysql.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "🚀 Starting MySQL database initialization...\n\n";
    
    // Create customers table
    $db->exec("
        CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) UNIQUE NOT NULL,
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_phone (phone)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✅ Table 'customers' created\n";
    
    // Create service_types table
    $db->exec("
        CREATE TABLE IF NOT EXISTS service_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            unit VARCHAR(10) NOT NULL COMMENT 'kg atau pcs',
            price_per_unit DECIMAL(10,2) NOT NULL,
            description TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✅ Table 'service_types' created\n";
    
    // Create transactions table
    $db->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            service_type VARCHAR(255) NOT NULL,
            weight DECIMAL(10,2) DEFAULT 0,
            quantity INT DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            INDEX idx_customer_id (customer_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✅ Table 'transactions' created\n";
    
    // Insert default service types jika belum ada
    $stmt = $db->query("SELECT COUNT(*) as count FROM service_types");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "\n📦 Inserting default service types...\n";
        
        $defaultServices = [
            ['Cuci Kering', 'kg', 5000, 'Cuci + setrika (per kilogram)'],
            ['Cuci Setrika', 'kg', 7000, 'Cuci + setrika + lipat (per kilogram)'],
            ['Setrika Saja', 'kg', 4000, 'Hanya setrika (per kilogram)'],
            ['Bed Cover Single', 'pcs', 15000, 'Cuci bed cover ukuran single'],
            ['Bed Cover Double', 'pcs', 25000, 'Cuci bed cover ukuran double'],
            ['Selimut', 'pcs', 20000, 'Cuci selimut'],
            ['Boneka Kecil', 'pcs', 10000, 'Cuci boneka ukuran kecil'],
            ['Boneka Besar', 'pcs', 25000, 'Cuci boneka ukuran besar'],
        ];
        
        $stmt = $db->prepare("
            INSERT INTO service_types (name, unit, price_per_unit, description) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($defaultServices as $service) {
            $stmt->execute($service);
            echo "  ✓ {$service[0]}\n";
        }
        
        echo "✅ Default service types inserted\n";
    } else {
        echo "\n⏭️  Service types already exist, skipping insertion\n";
    }
    
    echo "\n🎉 Database initialized successfully!\n";
    echo "📊 Tables created: customers, transactions, service_types\n";
    echo "🔗 Indexes created for better performance\n";
    echo "🗄️  Database: " . Database::getInstance()->getConfig()['database'] . "\n";
    
} catch(PDOException $e) {
    die("\n❌ Error initializing database: " . $e->getMessage() . "\n");
}
?>
