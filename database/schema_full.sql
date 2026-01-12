-- ============================================================
-- D'four Smart Laundry System - Full Database Schema
-- Database: MySQL 5.7+ / MariaDB 10.3+
-- Updated: 2026-01-13
-- ============================================================

-- Buat database (jalankan manual jika perlu)
-- CREATE DATABASE IF NOT EXISTS laundry_dfour CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE laundry_dfour;

-- ============================================================
-- DROP TABLES (Urutan: child tables dulu, parent tables terakhir)
-- Uncomment jika ingin reset database
-- ============================================================
-- DROP TABLE IF EXISTS payments;
-- DROP TABLE IF EXISTS password_resets;
-- DROP TABLE IF EXISTS transactions;
-- DROP TABLE IF EXISTS payment_methods;
-- DROP TABLE IF EXISTS customers;
-- DROP TABLE IF EXISTS service_types;
-- DROP TABLE IF EXISTS users;


-- ============================================================
-- TABLE: users
-- Menyimpan data user (Admin/Karyawan dan Pelanggan)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE NULL COMMENT 'ID dari Google OAuth',
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NULL COMMENT 'Nomor HP untuk link ke customers',
    password_hash VARCHAR(255) NULL COMMENT 'NULL jika login via Google',
    profile_picture TEXT NULL,
    role ENUM('superadmin', 'admin', 'cashier', 'user') DEFAULT 'user' COMMENT 'superadmin=Owner, admin=Manager, cashier=Karyawan, user=Pelanggan',
    login_method ENUM('email', 'google') DEFAULT 'email',
    is_active TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    verification_token VARCHAR(64) NULL,
    verification_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_google_id (google_id),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_role (role),
    INDEX idx_verification_token (verification_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: customers
-- Menyimpan data pelanggan laundry
-- ============================================================
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'Link ke tabel users jika pelanggan sudah daftar',
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    address TEXT NULL,
    registered_at TIMESTAMP NULL COMMENT 'Waktu pelanggan mendaftar akun',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_phone (phone),
    INDEX idx_user_id (user_id),
    
    -- Foreign Key
    CONSTRAINT fk_customer_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: service_types
-- Daftar jenis layanan laundry dan harganya
-- ============================================================
CREATE TABLE IF NOT EXISTS service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    unit VARCHAR(10) NOT NULL COMMENT 'Satuan: kg atau pcs',
    price_per_unit DECIMAL(10,2) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: payment_methods
-- Daftar metode pembayaran yang tersedia
-- ============================================================
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type ENUM('cash', 'bank_transfer', 'ewallet', 'qris') NOT NULL,
    bank_name VARCHAR(50) NULL COMMENT 'Nama bank (untuk transfer)',
    account_number VARCHAR(30) NULL COMMENT 'Nomor rekening',
    account_holder VARCHAR(100) NULL COMMENT 'Nama pemilik rekening',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: transactions
-- Menyimpan data transaksi laundry
-- ============================================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_type VARCHAR(255) NOT NULL,
    weight DECIMAL(10,2) DEFAULT 0 COMMENT 'Berat dalam kg',
    quantity INT DEFAULT 1 COMMENT 'Jumlah item (untuk satuan pcs)',
    price DECIMAL(10,2) NOT NULL COMMENT 'Total harga',
    status ENUM('pending', 'washing', 'drying', 'ironing', 'done', 'picked_up', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid',
    payment_method VARCHAR(50) NULL COMMENT 'cash, transfer, ewallet',
    payment_method_id INT NULL COMMENT 'Link ke payment_methods',
    notes TEXT NULL,
    estimated_done TIMESTAMP NULL COMMENT 'Estimasi selesai',
    picked_up_at TIMESTAMP NULL COMMENT 'Waktu diambil pelanggan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    INDEX idx_payment_method_id (payment_method_id),
    
    -- Foreign Keys
    CONSTRAINT fk_transaction_customer FOREIGN KEY (customer_id) 
        REFERENCES customers(id) ON DELETE CASCADE,
    CONSTRAINT fk_transaction_payment_method FOREIGN KEY (payment_method_id)
        REFERENCES payment_methods(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: payments
-- Menyimpan riwayat pembayaran
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('unpaid', 'pending', 'paid', 'cancelled') DEFAULT 'unpaid',
    paid_at TIMESTAMP NULL COMMENT 'Waktu pembayaran dikonfirmasi',
    confirmed_by INT NULL COMMENT 'ID user yang mengkonfirmasi',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_payment_method_id (payment_method_id),
    INDEX idx_status (status),
    
    -- Foreign Keys
    CONSTRAINT fk_payment_transaction FOREIGN KEY (transaction_id)
        REFERENCES transactions(id) ON DELETE CASCADE,
    CONSTRAINT fk_payment_method FOREIGN KEY (payment_method_id)
        REFERENCES payment_methods(id) ON DELETE RESTRICT,
    CONSTRAINT fk_payment_confirmed_by FOREIGN KEY (confirmed_by)
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLE: password_resets
-- Untuk fitur lupa password
-- ============================================================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_token (token),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DEFAULT DATA: Service Types (Jenis Layanan)
-- ============================================================
INSERT INTO service_types (name, unit, price_per_unit, description) VALUES
    ('Cuci Kering', 'kg', 5000.00, 'Cuci + keringkan (per kilogram)'),
    ('Cuci Setrika', 'kg', 7000.00, 'Cuci + setrika + lipat (per kilogram)'),
    ('Setrika Saja', 'kg', 4000.00, 'Hanya setrika (per kilogram)'),
    ('Cuci Express', 'kg', 12000.00, 'Cuci kilat selesai dalam 6 jam'),
    ('Bed Cover Single', 'pcs', 15000.00, 'Cuci bed cover ukuran single'),
    ('Bed Cover Double', 'pcs', 25000.00, 'Cuci bed cover ukuran double'),
    ('Selimut', 'pcs', 20000.00, 'Cuci selimut'),
    ('Karpet Kecil', 'pcs', 25000.00, 'Cuci karpet ukuran kecil'),
    ('Karpet Besar', 'pcs', 50000.00, 'Cuci karpet ukuran besar'),
    ('Boneka Kecil', 'pcs', 10000.00, 'Cuci boneka ukuran kecil'),
    ('Boneka Besar', 'pcs', 25000.00, 'Cuci boneka ukuran besar'),
    ('Gorden', 'pcs', 30000.00, 'Cuci gorden per lembar'),
    ('Jas/Blazer', 'pcs', 20000.00, 'Dry clean jas atau blazer'),
    ('Gaun/Dress', 'pcs', 25000.00, 'Dry clean gaun atau dress')
ON DUPLICATE KEY UPDATE name = VALUES(name);


-- ============================================================
-- DEFAULT DATA: Payment Methods
-- ============================================================
INSERT INTO payment_methods (name, type, bank_name, account_number, account_holder) VALUES
    ('Cash', 'cash', NULL, NULL, NULL),
    ('Transfer BCA', 'bank_transfer', 'BCA', '1234567890', 'D''four Laundry'),
    ('Transfer BRI', 'bank_transfer', 'BRI', '0987654321', 'D''four Laundry'),
    ('Transfer Mandiri', 'bank_transfer', 'Mandiri', '1122334455', 'D''four Laundry'),
    ('QRIS', 'qris', NULL, NULL, 'D''four Laundry')
ON DUPLICATE KEY UPDATE name = VALUES(name);


-- ============================================================
-- DEFAULT DATA: Admin User
-- Password: admin123 (bcrypt hash)
-- ============================================================
INSERT INTO users (email, name, phone, password_hash, role, login_method, is_active, email_verified_at) VALUES
    ('admin@dfour.com', 'Admin D''four', '081234567890', 
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
     'admin', 'email', 1, NOW())
ON DUPLICATE KEY UPDATE email = VALUES(email);


-- ============================================================
-- SAMPLE DATA: Customers (Opsional, untuk testing)
-- Uncomment jika ingin data sample
-- ============================================================
-- INSERT INTO customers (name, phone, address) VALUES
--     ('Budi Santoso', '081111111111', 'Jl. Merdeka No. 1, Jakarta'),
--     ('Siti Nurhaliza', '082222222222', 'Jl. Sudirman No. 25, Jakarta'),
--     ('Ahmad Dahlan', '083333333333', 'Jl. Diponegoro No. 10, Bandung'),
--     ('Dewi Lestari', '084444444444', 'Jl. Gatot Subroto No. 50, Surabaya'),
--     ('Rudi Hartono', '085555555555', 'Jl. Asia Afrika No. 15, Bandung');


-- ============================================================
-- VIEWS: Untuk kemudahan query
-- ============================================================

-- View: Transaksi dengan info pelanggan dan pembayaran
CREATE OR REPLACE VIEW v_transactions_detail AS
SELECT 
    t.id AS transaction_id,
    t.customer_id,
    c.name AS customer_name,
    c.phone AS customer_phone,
    t.service_type,
    t.weight,
    t.quantity,
    t.price,
    t.status,
    t.payment_status,
    t.payment_method,
    pm.name AS payment_method_name,
    t.notes,
    t.estimated_done,
    t.picked_up_at,
    t.created_at,
    t.updated_at
FROM transactions t
JOIN customers c ON t.customer_id = c.id
LEFT JOIN payment_methods pm ON t.payment_method_id = pm.id
ORDER BY t.created_at DESC;

-- View: Ringkasan pelanggan dengan status registrasi
CREATE OR REPLACE VIEW v_customers_status AS
SELECT 
    c.id,
    c.name,
    c.phone,
    c.address,
    c.user_id,
    CASE WHEN c.user_id IS NOT NULL THEN 'Terdaftar' ELSE 'Belum Daftar' END AS registration_status,
    u.email AS user_email,
    c.registered_at,
    c.created_at,
    (SELECT COUNT(*) FROM transactions t WHERE t.customer_id = c.id) AS total_transactions,
    (SELECT SUM(price) FROM transactions t WHERE t.customer_id = c.id AND t.payment_status = 'paid') AS total_paid
FROM customers c
LEFT JOIN users u ON c.user_id = u.id
ORDER BY c.name ASC;

-- View: Ringkasan pembayaran
CREATE OR REPLACE VIEW v_payments_detail AS
SELECT
    p.id AS payment_id,
    p.transaction_id,
    t.customer_id,
    c.name AS customer_name,
    pm.name AS payment_method,
    pm.type AS payment_type,
    p.amount,
    p.status,
    p.paid_at,
    u.name AS confirmed_by_name,
    p.notes,
    p.created_at
FROM payments p
JOIN transactions t ON p.transaction_id = t.id
JOIN customers c ON t.customer_id = c.id
JOIN payment_methods pm ON p.payment_method_id = pm.id
LEFT JOIN users u ON p.confirmed_by = u.id
ORDER BY p.created_at DESC;


-- ============================================================
-- END OF SCHEMA
-- ============================================================

-- Untuk menjalankan file ini:
-- mysql -u root -p laundry_dfour < database/schema_full.sql
-- atau import via phpMyAdmin
