-- Script untuk membuat database MySQL
-- Jalankan di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS laundry_dfour 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Gunakan database yang baru dibuat
USE laundry_dfour;

-- Tampilkan konfirmasi
SELECT 'Database laundry_dfour berhasil dibuat!' AS status;
