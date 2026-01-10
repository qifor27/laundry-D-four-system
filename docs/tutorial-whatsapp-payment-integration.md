# 📚 Tutorial: WhatsApp & Payment Integration

**Fitur:** Notifikasi WhatsApp + Metode Pembayaran Terintegrasi  
**Branch:** `feature/backend-whatsapp-payment-integration`  
**Estimasi Waktu:** 4-5 jam  
**Last Updated:** 2026-01-10  
**Biaya WhatsApp:** GRATIS (Manual Click-to-Chat)

---

## 📋 Daftar Isi

1. [Overview](#1-overview)
2. [File yang Dibuat](#2-file-yang-dibuat)
3. [Database Schema](#3-database-schema)
4. [Implementasi Payment Methods](#4-implementasi-payment-methods)
5. [Implementasi WhatsApp](#5-implementasi-whatsapp)
6. [Integrasi Keduanya](#6-integrasi-keduanya)
7. [Testing](#7-testing)

---

## 1. Overview

### 🎯 Tujuan Fitur

Fitur ini menggabungkan **Payment Methods** dan **WhatsApp Integration**:

1. Admin dapat mengelola rekening bank (Cash, BCA, BNI, dll)
2. Pesan WhatsApp otomatis menampilkan semua rekening yang aktif
3. Admin dapat konfirmasi pembayaran dan kirim notifikasi WhatsApp

### 📊 Alur Kerja

```
┌─────────────────────────────────────────────────────────────────┐
│                      ALUR LENGKAP                               │
├─────────────────────────────────────────────────────────────────┤
│ 1. Admin tambah rekening bank di halaman Payment Methods        │
│    ↓                                                            │
│ 2. Customer buat pesanan → Status: UNPAID                       │
│    ↓                                                            │
│ 3. Admin klik tombol WA "Siap Diambil"                          │
│    → Pesan otomatis tampilkan semua rekening dari database      │
│    ↓                                                            │
│ 4. Customer bayar (Cash/Transfer)                               │
│    ↓                                                            │
│ 5. Admin konfirmasi pembayaran → Status: PAID                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. File yang Dibuat

### 💳 Payment Methods

| File | Fungsi |
|------|--------|
| `database/migrate_payments.php` | Migrasi database |
| `api/payment-methods-api.php` | API CRUD rekening |
| `api/payments-api.php` | API konfirmasi bayar |
| `includes/payment-helper.php` | Helper functions |
| `pages/payment-methods.php` | Halaman admin kelola rekening |

### 📱 WhatsApp Integration

| File | Fungsi |
|------|--------|
| `includes/whatsapp-helper.php` | Generate URL & pesan WhatsApp |

### 🔗 Integrasi

| File | Fungsi |
|------|--------|
| `pages/transaction-detail.php` | UI tombol WA + konfirmasi bayar |

---

## 3. Database Schema

### Tabel `payment_methods`

```sql
CREATE TABLE payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,           -- "Cash", "Transfer BCA"
    type ENUM('cash', 'bank_transfer'),
    bank_name VARCHAR(50) NULL,          -- "BCA", "BNI"
    account_number VARCHAR(30) NULL,     -- "1234567890"
    account_holder VARCHAR(100) NULL,    -- "D'four Laundry"
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
    amount DECIMAL(12, 2) NOT NULL,
    status ENUM('unpaid', 'pending', 'paid', 'cancelled'),
    paid_at TIMESTAMP NULL,
    confirmed_by INT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Update Tabel `transactions`

```sql
ALTER TABLE transactions 
ADD COLUMN payment_status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid',
ADD COLUMN payment_method_id INT NULL;
```

---

## 4. Implementasi Payment Methods

### A. Jalankan Migrasi

```bash
cd c:\xampp\htdocs\laundry-D-four-system
C:\xampp\php\php.exe database/migrate_payments.php
```

### B. API Endpoints

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `?action=list` | Semua metode |
| GET | `?action=active` | Metode aktif |
| GET | `?action=banks` | Hanya bank transfer |
| POST | `action=create` | Tambah metode |
| POST | `action=update` | Update metode |
| POST | `action=delete` | Hapus metode |
| POST | `action=toggle` | Toggle aktif/nonaktif |

### C. Halaman Admin

URL: `http://localhost/laundry-D-four-system/pages/payment-methods.php`

Fitur:
- Tambah rekening baru
- Edit rekening
- Aktifkan/nonaktifkan rekening
- Hapus rekening

---

## 5. Implementasi WhatsApp

### A. Cara Kerja (Manual - Gratis)

```
1. Admin klik tombol "📱 WA: Siap Diambil"
2. Browser buka WhatsApp Web dengan pesan terisi
3. Admin klik Send
4. Selesai!
```

### B. Fungsi Utama

```php
// Generate URL WhatsApp
generateWhatsAppURL($phone, $message)

// Format nomor HP
formatPhoneNumber($phone) // 08xxx → 628xxx

// Ambil daftar bank dari database
getBankListForWA()

// Generate URL untuk berbagai jenis pesan
getOrderCreatedWAUrl($transaction, $customer)
getStatusUpdateWAUrl($transaction, $customer, $status)
getReadyForPickupWAUrl($transaction, $customer)
getPaymentReminderWAUrl($transaction, $customer)
```

### C. Contoh Pesan WhatsApp

```
*D'four Laundry*

Halo Budi! 🎉

Kabar baik! Pesanan Anda sudah selesai:
📦 No. Pesanan: #123
💰 Total: Rp 50.000

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat (Cash)
2️⃣ Transfer Bank:
   🏦 BCA - 1234567890
      A/N: D'four Laundry
   🏦 BNI - 0987654321
      A/N: Reyhan

📍 Lokasi: Jl. Contoh No. 123
🕐 Jam: 08.00 - 21.00 WIB

Ditunggu kedatangannya! 😊
```

> **Note:** Daftar bank otomatis dari database!

---

## 6. Integrasi Keduanya

### Diagram Hubungan

```
payment_methods (Database)
        │
        ├──► pages/payment-methods.php
        │    (Admin tambah BCA, BNI, BRI)
        │
        └──► includes/whatsapp-helper.php
             │
             └──► getBankListForWA()
                  (Ambil semua bank aktif)
                  │
                  ▼
             Pesan WhatsApp
             (Tampilkan semua bank)
                  │
                  ▼
             pages/transaction-detail.php
             (Tombol WA + Konfirmasi Bayar)
```

### Kode Integrasi

```php
// Di whatsapp-helper.php
function getBankListForWA() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("
        SELECT bank_name, account_number, account_holder 
        FROM payment_methods 
        WHERE type = 'bank_transfer' AND is_active = 1
    ");
    $banks = $stmt->fetchAll();
    
    $text = "";
    foreach ($banks as $bank) {
        $text .= "\n   🏦 {$bank['bank_name']} - {$bank['account_number']}";
        $text .= "\n      A/N: {$bank['account_holder']}";
    }
    return $text;
}
```

---

## 7. Testing

### A. Test Payment Methods

1. Buka: `http://localhost/laundry-D-four-system/pages/payment-methods.php`
2. Tambah rekening baru (BNI, BRI, dll)
3. Cek apakah tersimpan

### B. Test WhatsApp

1. Buka: `http://localhost/laundry-D-four-system/pages/transaction-detail.php?id=1`
2. Klik tombol "📱 WA: Siap Diambil"
3. Cek apakah pesan berisi semua bank yang aktif

### C. Test Integrasi

1. Tambah bank baru di Payment Methods
2. Klik tombol WhatsApp
3. **Bank baru harus otomatis muncul di pesan!**

---

## ✅ Checklist Implementasi

- [ ] Jalankan `migrate_payments.php`
- [ ] Test halaman `payment-methods.php`
- [ ] Tambah rekening bank (BCA, BNI, BRI, dll)
- [ ] Test halaman `transaction-detail.php`
- [ ] Test tombol WhatsApp
- [ ] Pastikan bank dinamis muncul di pesan

---

## 📊 Summary

| Fitur | Deskripsi |
|-------|-----------|
| **Rekening Bank** | Kelola di admin panel |
| **Status Bayar** | Unpaid → Paid |
| **Konfirmasi Manual** | Admin klik di detail transaksi |
| **WhatsApp Gratis** | Click-to-Chat (Rp 0) |
| **Bank Dinamis** | Otomatis dari database |

---

**Selamat Coding! 💳📱**
