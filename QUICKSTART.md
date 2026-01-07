# 🚀 Quick Start Guide - Laundry D'four

Panduan lengkap untuk menjalankan aplikasi setelah clone dari GitHub.

## 📋 Prerequisites

Pastikan sudah terinstall:
- ✅ **XAMPP** (atau PHP 7.4+, MySQL, Apache)
- ✅ **Node.js** dan **npm** (untuk Tailwind CSS)
- ✅ **Git** (untuk clone repository)

---

## 🚀 Langkah-Langkah Setup (Untuk yang Baru Clone)

### 1️⃣ **Clone Repository**

```bash
git clone https://github.com/username/laundry-D-four.git
cd laundry-D-four
```

### 2️⃣ **Install Dependencies**

Install package Node.js untuk Tailwind CSS:
```bash
npm install
```

### 3️⃣ **Setup Database MySQL**

#### A. Buat Database

**Opsi 1: Via phpMyAdmin**
1. Buka `http://localhost/phpmyadmin`
2. Klik tab "SQL"
3. Copy-paste isi file `database/create_database.sql`
4. Klik "Go" atau "Jalankan"

**Opsi 2: Via MySQL CLI**
```bash
mysql -u root -p < database/create_database.sql
```

**Opsi 3: Manual**
```sql
CREATE DATABASE IF NOT EXISTS laundry_dfour 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

#### B. Konfigurasi Database (Jika Perlu)

Edit file `config/database_mysql.php` jika kredensial MySQL Anda berbeda:

```php
private $host = "localhost";
private $db_name = "laundry_dfour";
private $username = "root";      // Ganti jika berbeda
private $password = "";          // Ganti jika ada password
```

#### C. Inisialisasi Tabel & Data

Jalankan script inisialisasi untuk membuat tabel dan data default:

**Jika PHP sudah di PATH:**
```bash
php database/init_mysql.php
```

**Jika PHP belum di PATH (gunakan path lengkap):**
```bash
"C:\xampp\php\php.exe" database/init_mysql.php
```

**Output yang diharapkan:**
```
🚀 Starting MySQL database initialization...

✅ Table 'customers' created
✅ Table 'service_types' created 
✅ Table 'transactions' created

📦 Inserting default service types...
  ✓ Cuci Kering
  ✓ Cuci Setrika
  ✓ Setrika Saja
  ✓ Bed Cover Single
  ✓ Bed Cover Double
  ✓ Selimut
  ✓ Boneka Kecil
  ✓ Boneka Besar
✅ Default service types inserted

🎉 Database initialized successfully!
📊 Tables created: customers, transactions, service_types
🔗 Indexes created for better performance
🗄️  Database: laundry_dfour
```

### 4️⃣ **Build Tailwind CSS**

Compile CSS untuk pertama kali:
```bash
npm run build
```

Atau untuk development mode (auto-compile saat ada perubahan):
```bash
npm run dev
```

### 5️⃣ **Jalankan Server**

**Opsi 1: Menggunakan Batch File (Termudah)**
- Double-click file `start-server.bat`
- Browser akan otomatis membuka di `http://localhost:8000`

**Opsi 2: Manual via Command Line**

Jika PHP sudah di PATH:
```bash
php -S localhost:8000
```

Jika PHP belum di PATH:
```bash
"C:\xampp\php\php.exe" -S localhost:8000
```

### 6️⃣ **Akses Aplikasi**

Buka browser dan akses:
```
http://localhost:8000
```

---

## 📍 Lokasi PHP di Windows

Biasanya PHP ada di salah satu lokasi berikut:

- **XAMPP:** `C:\xampp\php\php.exe`
- **Laragon:** `C:\laragon\bin\php\php-8.x-Win32\php.exe`
- **WAMP:** `C:\wamp64\bin\php\php-8.x\php.exe`

---

## 🔧 Troubleshooting

### 1. "php' is not recognized..."

**Solusi 1:** Tambahkan PHP ke PATH Windows
1. Cari "Environment Variables" di Windows Search
2. Edit "Path" di System Variables
3. Tambahkan path PHP (misal: `C:\xampp\php`)
4. Restart terminal/command prompt

**Solusi 2:** Gunakan path lengkap
```bash
"C:\xampp\php\php.exe" -S localhost:8000
```

### 2. Port 8000 sudah digunakan

Gunakan port lain:
```bash
php -S localhost:8080
```
Lalu akses di `http://localhost:8080`

### 3. Database Connection Error

**Cek apakah MySQL sudah running:**
- Buka XAMPP Control Panel
- Pastikan MySQL dalam status "Running"
- Jika belum, klik tombol "Start" pada MySQL

**Cek kredensial database:**
- Pastikan username dan password di `config/database_mysql.php` sesuai dengan MySQL Anda
- Default XAMPP: username=`root`, password=`` (kosong)

**Cek apakah database sudah dibuat:**
```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'laundry_dfour';"
```

### 4. CSS tidak muncul

Pastikan Tailwind sudah di-compile:
```bash
npm run build
```

Cek apakah file `assets/css/output.css` sudah ada dan tidak kosong.

### 5. Error saat npm install

Hapus folder `node_modules` dan `package-lock.json`, lalu install ulang:
```bash
rm -rf node_modules package-lock.json
npm install
```

---

## 📚 Workflow Development

### Saat Mengembangkan Aplikasi:

Buka **2 terminal** secara bersamaan:

**Terminal 1 - Tailwind Watch:**
```bash
npm run dev
```
> Auto-compile CSS saat ada perubahan file

**Terminal 2 - PHP Server:**
```bash
php -S localhost:8000
```
> Menjalankan aplikasi

### Saat Production Build:

```bash
npm run build
```
> Compile CSS dengan minify untuk production

---

## 🎯 Halaman yang Tersedia

Setelah aplikasi berjalan, Anda bisa akses:

### 🌐 **Public Page** (Akses Langsung)
- **Landing Page:** `http://localhost:8000/` → Otomatis redirect ke Cek Order
- **Cek Order:** `http://localhost:8000/pages/check-order.php` - Customer bisa cek status pesanan dengan nomor HP

### 🔐 **Admin Pages** (Klik button "Admin" di navbar)
1. **Dashboard:** `http://localhost:8000/pages/dashboard.php` - Statistik & overview
2. **Pelanggan:** `http://localhost:8000/pages/customers.php` - Manajemen data pelanggan
3. **Transaksi:** `http://localhost:8000/pages/transactions.php` - Manajemen transaksi laundry

> **Note:** Saat ini belum ada authentication. Semua halaman bisa diakses langsung via URL. Authentication dengan Google OAuth akan ditambahkan di update berikutnya.

---

## 🎨 Next Steps

Setelah aplikasi berjalan, Anda bisa:

1. ✅ Tambah pelanggan di halaman **Pelanggan**
2. ✅ Buat transaksi baru di halaman **Transaksi**
3. ✅ Update status pesanan
4. ✅ Customer bisa cek status via halaman **Cek Order** (menggunakan nomor HP)

---

## 💡 Tips

- Gunakan **Ctrl+C** untuk stop server
- Refresh browser (F5) untuk melihat perubahan
- Check console browser (F12) jika ada error JavaScript
- Database MySQL: `laundry_dfour`
- Gunakan `npm run dev` saat development untuk auto-compile CSS
- Gunakan `npm run build` sebelum deploy ke production

---

## 📦 Struktur Database

Aplikasi menggunakan 3 tabel utama:

1. **customers** - Data pelanggan (nama, telepon, alamat)
2. **service_types** - Jenis layanan laundry (nama, harga, satuan)
3. **transactions** - Transaksi laundry (customer, service, status, dll)

Lihat detail schema di `database/init_mysql.php`

---

## 🆘 Butuh Bantuan?

Jika masih ada masalah:
1. Cek file `README.md` untuk dokumentasi lengkap
2. Cek file `docs/learning_by_doing.md` untuk panduan belajar
3. Pastikan semua prerequisites sudah terinstall dengan benar
4. Cek error log di console/terminal

---

**Selamat Coding! 🎉**
