<?php
/**
 * Script untuk membuat database MySQL
 * Jalankan script ini terlebih dahulu sebelum init_mysql.php
 */

echo "🔧 Creating MySQL database...\n\n";

// Konfigurasi
$host = "localhost";
$username = "root";
$password = "";
$db_name = "laundry_dfour";
$charset = "utf8mb4";

try {
    // Koneksi ke MySQL tanpa memilih database
    $dsn = "mysql:host={$host};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Buat database
    $sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}` 
            CHARACTER SET {$charset} 
            COLLATE utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "✅ Database '{$db_name}' berhasil dibuat!\n";
    echo "📊 Character Set: {$charset}\n";
    echo "🔤 Collation: utf8mb4_unicode_ci\n\n";
    
    // Verifikasi database
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$db_name}'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Verifikasi: Database ditemukan\n";
        echo "🎉 Siap untuk menjalankan init_mysql.php\n\n";
    } else {
        echo "⚠️  Warning: Database tidak ditemukan setelah dibuat\n";
    }
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "💡 Pastikan MySQL sudah running di XAMPP Control Panel\n";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "💡 Periksa username dan password MySQL Anda\n";
    }
    
    exit(1);
}
?>
