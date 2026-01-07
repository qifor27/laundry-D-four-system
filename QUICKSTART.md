# 🚀 Quick Start Guide - D'four Laundry

Panduan lengkap untuk menjalankan aplikasi setelah clone dari GitHub.

**Last Updated: 2026-01-07**

---

## 📋 Prerequisites

Pastikan sudah terinstall:
- ✅ **XAMPP** (Apache + MySQL + PHP 7.4+)
- ✅ **Node.js** dan **npm** (untuk Tailwind CSS)
- ✅ **Git** (untuk clone repository)

---

## 🚀 Langkah-Langkah Setup

### 1️⃣ Clone Repository

```bash
git clone https://github.com/qifor27/laundry-D-four-system.git
cd laundry-D-four-system
```

> **Note:** Folder harus berada di `htdocs` XAMPP, contoh: `E:\xampp2\htdocs\laundry-D-four`

### 2️⃣ Install Dependencies

```bash
npm install
```

### 3️⃣ Setup Database MySQL

#### A. Start XAMPP
1. Buka **XAMPP Control Panel**
2. Start **Apache** dan **MySQL**

#### B. Buat Database

**Via phpMyAdmin:**
1. Buka `http://localhost/phpmyadmin`
2. Klik "New" → Nama database: `laundry_dfour`
3. Klik "Create"

**Atau via SQL:**
```sql
CREATE DATABASE IF NOT EXISTS laundry_dfour 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

#### C. Konfigurasi (Jika Perlu)

Edit `config/database_mysql.php` jika kredensial berbeda:
```php
private $host = "localhost";
private $db_name = "laundry_dfour";
private $username = "root";      // Ganti jika berbeda
private $password = "";          // Ganti jika ada password
```

#### D. Inisialisasi Database & Migrasi

Jalankan script-script berikut secara berurutan:

```bash
# 1. Inisialisasi tabel dasar (customers, transactions, service_types)
E:\xampp2\php\php.exe database/init_mysql.php

# 2. Migrasi tabel authentication (users, password_resets)
E:\xampp2\php\php.exe database/migrate_auth.php

# 3. Migrasi Customer-User Integration (phone di users, user_id di customers)
E:\xampp2\php\php.exe database/migrate_customer_user.php

# 4. Buat akun admin
E:\xampp2\php\php.exe database/create_admin.php
```

> **Note:** Sesuaikan path PHP dengan lokasi XAMPP Anda. 
> Contoh: `C:\xampp\php\php.exe` atau `E:\xampp2\php\php.exe`

**Output yang diharapkan untuk tiap script:**
- ✅ Tables created successfully
- ✅ Admin baru berhasil dibuat

### 4️⃣ Build Tailwind CSS

```bash
# Production build
npm run build

# Atau development mode (auto-compile)
npm run dev
```

### 5️⃣ Akses Aplikasi

Buka browser:
```
http://localhost/laundry-D-four
```

> **Note:** Pastikan folder project sesuai dengan URL. Jika folder bernama `laundry-D-four-system`, gunakan URL tersebut.

---

## 🔐 Login Credentials

### Admin Login
| Field | Value |
|-------|-------|
| URL | `http://localhost/laundry-D-four/pages/auth/admin-login.php` |
| Email | `admin@dfour.com` |
| Password | `admin123` |

### Customer Login/Register
| Halaman | URL |
|---------|-----|
| Login | `http://localhost/laundry-D-four/pages/auth/login.php` |
| Register | `http://localhost/laundry-D-four/pages/auth/register.php` |

---

## 🎯 Halaman yang Tersedia

### 🌐 Public Pages (Tanpa Login)
| Halaman | URL |
|---------|-----|
| Landing Page | `/` atau `/index.php` |
| Cek Order | `/pages/check-order.php` |
| Customer Login | `/pages/auth/login.php` |
| Customer Register | `/pages/auth/register.php` |

### 👤 Customer Pages (Login sebagai User)
| Halaman | URL |
|---------|-----|
| Customer Dashboard | `/pages/customer-dashboard.php` |

### 🔧 Admin Pages (Login sebagai Admin)
| Halaman | URL |
|---------|-----|
| Admin Login | `/pages/auth/admin-login.php` |
| Dashboard | `/pages/dashboard.php` |
| Pelanggan | `/pages/customers.php` |
| Transaksi | `/pages/transactions.php` |

---

## 📚 Flow Kerja Aplikasi

```
┌─────────────────────────────────────────────────────────────────┐
│                        LANDING PAGE                             │
│                         (index.php)                             │
└───────────────┬────────────────────────────────┬────────────────┘
                │                                │
                ▼                                ▼
┌───────────────────────────┐      ┌───────────────────────────┐
│     CUSTOMER FLOW         │      │      ADMIN FLOW           │
│                           │      │                           │
│  1. Register dengan HP    │      │  1. Login di admin-login  │
│  2. Login                 │      │  2. Dashboard (statistik) │
│  3. Customer Dashboard    │      │  3. Kelola Pelanggan      │
│  4. Lihat transaksi       │      │  4. Kelola Transaksi      │
│  5. Cek status order      │      │  5. Update status         │
└───────────────────────────┘      └───────────────────────────┘
```

---

## ⚙️ Konfigurasi Opsional

### Google OAuth (Opsional)
Untuk mengaktifkan login dengan Google:

1. Buat project di [Google Cloud Console](https://console.cloud.google.com)
2. Enable Google+ API
3. Buat OAuth 2.0 credentials
4. Edit `config/google-oauth.php`:
```php
define('GOOGLE_CLIENT_ID', 'your-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
```

### Email SMTP (Opsional)
Untuk mengaktifkan email verifikasi:

1. Buat App Password di Gmail
2. Edit `config/email.php`:
```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

---

## 🔧 Troubleshooting

### 1. "php' is not recognized..."
Gunakan path lengkap: `E:\xampp2\php\php.exe` atau tambahkan ke PATH Windows.

### 2. Database Connection Error
- Pastikan MySQL di XAMPP sudah running
- Cek kredensial di `config/database_mysql.php`
- Pastikan database `laundry_dfour` sudah dibuat

### 3. CSS tidak muncul
```bash
npm run build
```

### 4. Error "Table doesn't exist"
Jalankan semua migration script:
```bash
php database/init_mysql.php
php database/migrate_auth.php
php database/migrate_customer_user.php
```

### 5. Redirect URL salah
Pastikan folder project sesuai dengan yang di URL. Jika perlu, sesuaikan base URL di aplikasi.

---

## 📚 Dokumentasi Lainnya

| File | Deskripsi |
|------|-----------|
| `docs/learning_by_doing.md` | Panduan lengkap struktur project |
| `docs/to-do.md` | TODO list development |
| `docs/registration-flow.md` | Alur registrasi customer |
| `docs/GITTutor.md` | Tutorial Git & GitHub |

---

## 💡 Tips Development

1. **Jalankan 2 terminal:**
   - Terminal 1: `npm run dev` (Tailwind watch)
   - Terminal 2: Buka browser ke localhost

2. **Gunakan F12** untuk debug di browser console

3. **Cek error PHP** di:
   - XAMPP logs: `xampp/apache/logs/error.log`
   - Atau enable error display di PHP

4. **Commit sering** dengan pesan yang jelas

---

**Selamat Coding! 🎉**
