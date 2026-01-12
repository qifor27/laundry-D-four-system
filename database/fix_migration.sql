-- ================================================================
-- MIGRATION SCRIPT: Memperbaiki Database laundry_dfour
-- Jalankan script ini untuk menyinkronkan database dengan backend
-- 
-- CARA MENJALANKAN:
-- 1. Buka phpMyAdmin
-- 2. Pilih database laundry_dfour
-- 3. Tab SQL -> Paste script ini -> Execute
-- ================================================================

-- ============================================================
-- 1. FIX TRANSACTIONS TABLE - MODIFIKASI KOLOM STATUS
-- ============================================================

-- Tambahkan kolom yang kurang di transactions
ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid' AFTER status;

ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL AFTER payment_status;

ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS payment_method_id INT NULL AFTER payment_method;

ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS estimated_done TIMESTAMP NULL AFTER notes;

ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS picked_up_at TIMESTAMP NULL AFTER estimated_done;

-- Ubah ENUM status transactions ke format yang benar
-- CATATAN: Backup data dulu jika punya data penting!
ALTER TABLE transactions 
MODIFY COLUMN status ENUM('pending', 'washing', 'drying', 'ironing', 'done', 'picked_up', 'cancelled') DEFAULT 'pending';

-- Update data lama yang mungkin punya status berbeda
UPDATE transactions SET status = 'pending' WHERE status = 'processing';
UPDATE transactions SET status = 'done' WHERE status = 'ready';
UPDATE transactions SET status = 'picked_up' WHERE status = 'completed';


-- ============================================================
-- 2. CREATE PAYMENT_METHODS TABLE
-- ============================================================

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default payment methods
INSERT IGNORE INTO payment_methods (name, type, bank_name, account_number, account_holder) VALUES
    ('Cash', 'cash', NULL, NULL, NULL),
    ('Transfer BCA', 'bank_transfer', 'BCA', '1234567890', 'D''four Laundry');


-- ============================================================
-- 3. CREATE PAYMENTS TABLE
-- ============================================================

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- VERIFIKASI - Jalankan query ini untuk memastikan berhasil
-- ============================================================

-- Cek tabel baru
-- SHOW TABLES;

-- Cek struktur transactions
-- DESCRIBE transactions;

-- Cek payment methods
-- SELECT * FROM payment_methods;
