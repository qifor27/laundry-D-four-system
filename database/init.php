<?php
/**
 * Database Schema Initialization
 * Run this file once to create the database tables
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Create customers table
    $db->exec("
        CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT UNIQUE NOT NULL,
            address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Create transactions table
    $db->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_id INTEGER NOT NULL,
            service_type TEXT NOT NULL,
            weight REAL DEFAULT 0,
            quantity INTEGER DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        );
    ");
    
    // Create service_types table (untuk master data layanan)
    $db->exec("
        CREATE TABLE IF NOT EXISTS service_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            unit TEXT NOT NULL, -- 'kg' atau 'pcs'
            price_per_unit DECIMAL(10,2) NOT NULL,
            description TEXT,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Create indexes untuk performa
    $db->exec("
        CREATE INDEX IF NOT EXISTS idx_transactions_customer_id ON transactions(customer_id);
        CREATE INDEX IF NOT EXISTS idx_transactions_status ON transactions(status);
        CREATE INDEX IF NOT EXISTS idx_transactions_created_at ON transactions(created_at);
        CREATE INDEX IF NOT EXISTS idx_customers_phone ON customers(phone);
    ");
    
    // Insert default service types jika belum ada
    $stmt = $db->query("SELECT COUNT(*) as count FROM service_types");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
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
        }
        
        echo "✅ Default service types created\n";
    }
    
    echo "✅ Database initialized successfully!\n";
    echo "📊 Tables created: customers, transactions, service_types\n";
    echo "🔗 Indexes created for better performance\n";
    
} catch(PDOException $e) {
    die("❌ Error initializing database: " . $e->getMessage());
}
?>
