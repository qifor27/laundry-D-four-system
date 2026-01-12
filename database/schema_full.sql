-- ============================================================
-- D'four Smart Laundry System - Full Database Schema
-- Database: MySQL 5.7+ / MariaDB 10.3+
-- Created: 2026-01-08
-- ============================================================

-- Buat database (jalankan manual jika perlu)
-- CREATE DATABASE IF NOT EXISTS dfour_laundry CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE dfour_laundry;

-- ============================================================
-- DROP TABLES (Urutan: child tables dulu, parent tables terakhir)
-- Uncomment jika ingin reset database
-- ============================================================
-- DROP TABLE IF EXISTS password_resets;
-- DROP TABLE IF EXISTS transactions;
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
    role ENUM('admin', 'user') DEFAULT 'user' COMMENT 'admin=Karyawan, user=Pelanggan',
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
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    payment_method VARCHAR(50) NULL COMMENT 'cash, transfer, ewallet',
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
    
    -- Foreign Key
    CONSTRAINT fk_transaction_customer FOREIGN KEY (customer_id) 
        REFERENCES customers(id) ON DELETE CASCADE
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
    ('Gaun/Dress', 'pcs', 25000.00, 'Dry clean gaun atau dress');


-- ============================================================
-- DEFAULT DATA: Admin User
-- Password: admin123 (bcrypt hash)
-- ============================================================
INSERT INTO users (email, name, phone, password_hash, role, login_method, is_active, email_verified_at) VALUES
    ('admin@dfour.com', 'Admin D''four', '081234567890', 
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
     'admin', 'email', 1, NOW());


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
-- SAMPLE DATA: Transactions (Opsional, untuk testing)
-- Uncomment jika ingin data sample
-- ============================================================
-- INSERT INTO transactions (customer_id, service_type, weight, quantity, price, status, notes) VALUES
--     (1, 'Cuci Setrika', 3.5, 1, 24500, 'pending', 'Baju kerja'),
--     (1, 'Bed Cover Double', 0, 1, 25000, 'washing', NULL),
--     (2, 'Cuci Kering', 5, 1, 25000, 'done', 'Cuci biasa'),
--     (3, 'Setrika Saja', 2, 1, 8000, 'picked_up', 'Sudah diambil'),
--     (4, 'Cuci Express', 2, 1, 24000, 'drying', 'Butuh cepat');


-- ============================================================
-- VIEWS (Opsional, untuk kemudahan query)
-- ============================================================

-- View: Transaksi dengan info pelanggan
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
    t.notes,
    t.created_at,
    t.updated_at
FROM transactions t
JOIN customers c ON t.customer_id = c.id
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
    (SELECT COUNT(*) FROM transactions t WHERE t.customer_id = c.id) AS total_transactions
FROM customers c
LEFT JOIN users u ON c.user_id = u.id
ORDER BY c.name ASC;


-- ============================================================
-- STORED PROCEDURES (Opsional)
-- ============================================================

-- Procedure: Get Monthly Report
 

-- ============================================================
-- END OF SCHEMA
-- ============================================================

-- Untuk menjalankan file ini:
-- mysql -u root -p < database/schema_full.sql
-- atau import via phpMyAdmin
