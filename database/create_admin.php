<?php

/**
 * Create/Promote Admin Account
 * 
 * Usage: php database/create_admin.php
 */

require_once __DIR__ . '/../config/database_mysql.php';

$email = 'admin@dfour.com';
$name = 'Admin D\'four';
$phone = '081111111111';
$role = 'admin';

try {
    $db = Database::getInstance()->getConnection();

    echo "🔄 Creating admin account...\n\n";

    // Check if user exists
    $stmt = $db->prepare("SELECT id, name, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // User exists, update role
        $stmt = $db->prepare("UPDATE users SET role = ?, email_verified_at = IFNULL(email_verified_at, NOW()) WHERE email = ?");
        $stmt->execute([$role, $email]);

        echo "✅ User sudah ada, role diupdate!\n";
        echo "   Email: $email\n";
        echo "   Nama: " . $user['name'] . "\n";
        echo "   Role sebelumnya: " . $user['role'] . "\n";
        echo "   Role baru: $role\n";
    } else {
        // User doesn't exist, create new with password
        $password = 'admin123'; // Default password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (email, name, phone, password_hash, role, login_method, email_verified_at, is_active)
            VALUES (?, ?, ?, ?, ?, 'email', NOW(), 1)
        ");
        $stmt->execute([$email, $name, $phone, $passwordHash, $role]);

        echo "✅ Admin baru berhasil dibuat!\n";
        echo "   Email: $email\n";
        echo "   Nama: $name\n";
        echo "   Role: $role\n";
        echo "   Password: $password\n";
        echo "\n⚠️  PENTING: Segera ganti password setelah login!\n";
    }

    echo "\n🎉 Done!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
