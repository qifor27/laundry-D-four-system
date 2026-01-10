# 📚 Tutorial: Payment Methods

**Fitur:** Kelola metode pembayaran (Cash & Transfer Bank)  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 2-3 jam  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Database Schema](#2-database-schema)
3. [Implementasi](#3-implementasi)
4. [Testing](#4-testing)

---

## 1. Penjelasan Fitur

### Metode Pembayaran

| Metode | Deskripsi |
|--------|-----------|
| **Cash** | Bayar tunai di tempat |
| **Transfer Bank** | Transfer ke rekening laundry |

### Alur Pembayaran

```
1. Customer buat pesanan → Status: UNPAID
2. Customer bayar (cash/transfer)
3. Admin konfirmasi → Status: PAID
```

---

## 2. Database Schema

### Tabel `payment_methods`

```sql
CREATE TABLE payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    type ENUM('cash', 'bank_transfer'),
    bank_name VARCHAR(50) NULL,
    account_number VARCHAR(30) NULL,
    account_holder VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel `payments`

```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount DECIMAL(12, 2),
    status ENUM('unpaid', 'pending', 'paid', 'cancelled'),
    paid_at TIMESTAMP NULL,
    confirmed_by INT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Update `transactions`

```sql
ALTER TABLE transactions 
ADD COLUMN payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid',
ADD COLUMN payment_method_id INT NULL;
```

---

## 3. Implementasi

### File yang Dibuat

| File | Fungsi |
|------|--------|
| `database/migrate_payments.php` | Migrasi database |
| `api/payment-methods-api.php` | API CRUD rekening |
| `api/payments-api.php` | API konfirmasi bayar |
| `includes/payment-helper.php` | Helper functions |
| `pages/payment-methods.php` | Halaman admin |

### Jalankan Migrasi

```bash
C:\xampp\php\php.exe database/migrate_payments.php
```

### API Endpoints

| Method | Action | Fungsi |
|--------|--------|--------|
| GET | `?action=list` | Semua metode |
| GET | `?action=active` | Metode aktif |
| POST | `action=create` | Tambah metode |
| POST | `action=update` | Update metode |
| POST | `action=delete` | Hapus metode |

---

## 4. Testing

### A. Halaman Admin

```
http://localhost/laundry-D-four-system/pages/payment-methods.php
```

### B. Test API

```
GET http://localhost/laundry-D-four-system/api/payment-methods-api.php?action=list
```

---

## ✅ Checklist

- [ ] Jalankan migrasi
- [ ] Test halaman payment-methods.php
- [ ] Tambah rekening (BCA, BNI, dll)
- [ ] Test konfirmasi pembayaran

---

**Selamat Coding! 💳**
