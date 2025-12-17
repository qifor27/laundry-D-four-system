<?php
/**
 * Script untuk migrasi data dari SQLite ke MySQL
 * Jalankan script ini setelah init_mysql.php berhasil
 */

echo "🔄 Starting data migration from SQLite to MySQL...\n\n";

// Load SQLite database
require_once __DIR__ . '/../config/database.php';
$sqliteDb = Database::getInstance()->getConnection();

// Load MySQL database
require_once __DIR__ . '/../config/database_mysql.php';
$mysqlDb = Database::getInstance()->getConnection();

try {
    // Start transaction
    $mysqlDb->beginTransaction();
    
    // ==========================================
    // 1. Migrate Customers
    // ==========================================
    echo "📦 Migrating customers...\n";
    
    $customers = $sqliteDb->query("SELECT * FROM customers ORDER BY id")->fetchAll();
    
    if (count($customers) > 0) {
        $stmt = $mysqlDb->prepare("
            INSERT INTO customers (id, name, phone, address, created_at) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($customers as $customer) {
            $stmt->execute([
                $customer['id'],
                $customer['name'],
                $customer['phone'],
                $customer['address'],
                $customer['created_at']
            ]);
            echo "  ✓ Customer: {$customer['name']}\n";
        }
        
        echo "✅ Migrated " . count($customers) . " customers\n\n";
    } else {
        echo "⏭️  No customers to migrate\n\n";
    }
    
    // ==========================================
    // 2. Migrate Service Types
    // ==========================================
    echo "📦 Migrating service types...\n";
    
    $services = $sqliteDb->query("SELECT * FROM service_types ORDER BY id")->fetchAll();
    
    if (count($services) > 0) {
        // Clear default services first
        $mysqlDb->exec("DELETE FROM service_types");
        
        $stmt = $mysqlDb->prepare("
            INSERT INTO service_types (id, name, unit, price_per_unit, description, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($services as $service) {
            $stmt->execute([
                $service['id'],
                $service['name'],
                $service['unit'],
                $service['price_per_unit'],
                $service['description'],
                $service['is_active'],
                $service['created_at']
            ]);
            echo "  ✓ Service: {$service['name']}\n";
        }
        
        echo "✅ Migrated " . count($services) . " service types\n\n";
    } else {
        echo "⏭️  No service types to migrate (using defaults)\n\n";
    }
    
    // ==========================================
    // 3. Migrate Transactions
    // ==========================================
    echo "📦 Migrating transactions...\n";
    
    $transactions = $sqliteDb->query("SELECT * FROM transactions ORDER BY id")->fetchAll();
    
    if (count($transactions) > 0) {
        $stmt = $mysqlDb->prepare("
            INSERT INTO transactions (id, customer_id, service_type, weight, quantity, price, status, notes, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($transactions as $transaction) {
            $stmt->execute([
                $transaction['id'],
                $transaction['customer_id'],
                $transaction['service_type'],
                $transaction['weight'],
                $transaction['quantity'],
                $transaction['price'],
                $transaction['status'],
                $transaction['notes'],
                $transaction['created_at'],
                $transaction['updated_at']
            ]);
            echo "  ✓ Transaction ID: {$transaction['id']}\n";
        }
        
        echo "✅ Migrated " . count($transactions) . " transactions\n\n";
    } else {
        echo "⏭️  No transactions to migrate\n\n";
    }
    
    // Commit transaction
    $mysqlDb->commit();
    
    // ==========================================
    // Summary
    // ==========================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 Migration completed successfully!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Summary:\n";
    echo "   • Customers: " . count($customers) . "\n";
    echo "   • Service Types: " . count($services) . "\n";
    echo "   • Transactions: " . count($transactions) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n✅ You can now use MySQL database!\n";
    echo "💡 Next step: Update all PHP files to use database_mysql.php\n\n";
    
} catch(PDOException $e) {
    // Rollback on error
    $mysqlDb->rollBack();
    die("\n❌ Migration failed: " . $e->getMessage() . "\n");
}
?>
