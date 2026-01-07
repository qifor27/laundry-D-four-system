<?php
/**
 * Database Migration: Add Enterprise Auth Columns
 * 
 * Run this script to add new columns to existing users table
 * for email/password authentication and email verification.
 * 
 * Usage: php database/migrate_auth.php
 */

require_once __DIR__ . '/../config/database_mysql.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "🔄 Starting authentication migration...\n\n";
    
    // Check if password_hash column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'password_hash'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NULL AFTER name");
        echo "✅ Added column 'password_hash'\n";
    } else {
        echo "⏭️  Column 'password_hash' already exists\n";
    }
    
    // Check if login_method column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'login_method'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN login_method ENUM('email', 'google') DEFAULT 'email' AFTER role");
        echo "✅ Added column 'login_method'\n";
    } else {
        echo "⏭️  Column 'login_method' already exists\n";
    }
    
    // Check if email_verified_at column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'email_verified_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL AFTER is_active");
        echo "✅ Added column 'email_verified_at'\n";
    } else {
        echo "⏭️  Column 'email_verified_at' already exists\n";
    }
    
    // Check if verification_token column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'verification_token'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN verification_token VARCHAR(64) NULL AFTER email_verified_at");
        echo "✅ Added column 'verification_token'\n";
    } else {
        echo "⏭️  Column 'verification_token' already exists\n";
    }
    
    // Check if verification_expires column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'verification_expires'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN verification_expires TIMESTAMP NULL AFTER verification_token");
        echo "✅ Added column 'verification_expires'\n";
    } else {
        echo "⏭️  Column 'verification_expires' already exists\n";
    }
    
    // Add index for verification_token if not exists
    $stmt = $db->query("SHOW INDEX FROM users WHERE Key_name = 'idx_verification_token'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD INDEX idx_verification_token (verification_token)");
        echo "✅ Added index 'idx_verification_token'\n";
    } else {
        echo "⏭️  Index 'idx_verification_token' already exists\n";
    }
    
    echo "\n";
    
    // Create password_resets table if not exists
    $stmt = $db->query("SHOW TABLES LIKE 'password_resets'");
    if ($stmt->rowCount() == 0) {
        $db->exec("
            CREATE TABLE password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(64) NOT NULL UNIQUE,
                expires_at TIMESTAMP NOT NULL,
                used_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_token (token),
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Created table 'password_resets'\n";
    } else {
        echo "⏭️  Table 'password_resets' already exists\n";
    }
    
    // Update existing Google users to have login_method = 'google' and verified
    $db->exec("
        UPDATE users 
        SET login_method = 'google', email_verified_at = created_at 
        WHERE google_id IS NOT NULL AND login_method = 'email'
    ");
    echo "✅ Updated existing Google users\n";
    
    echo "\n🎉 Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
