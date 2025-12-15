# 🚀 Quick Start Guide

## Langkah-Langkah Menjalankan Aplikasi

### Opsi 1: Menggunakan Batch File (Termudah)

1. **Double-click file `start-server.bat`**
2. Browser akan otomatis membuka di `http://localhost:8000`
3. Done! ✅

### Opsi 2: Manual (Step by Step)

#### 1️⃣ **Install Dependencies**
```bash
npm install
```

#### 2️⃣ **Build Tailwind CSS**
```bash
npm run build
```

Atau untuk development mode (auto-compile saat ada perubahan):
```bash
npm run dev
```

#### 3️⃣ **Inisialisasi Database**

**Jika PHP sudah di PATH:**
```bash
php database/init.php
```

**Jika PHP belum di PATH (gunakan path lengkap):**
```bash
"C:\xampp\php\php.exe" database/init.php
```

Output yang diharapkan:
```
✅ Default service types created
✅ Database initialized successfully!
📊 Tables created: customers, transactions, service_types
🔗 Indexes created for better performance
```

#### 4️⃣ **Jalankan Server**

**Jika PHP sudah di PATH:**
```bash
php -S localhost:8000
```

**Jika PHP belum di PATH:**
```bash
"C:\xampp\php\php.exe" -S localhost:8000
```

#### 5️⃣ **Akses Aplikasi**

Buka browser dan akses:
```
http://localhost:8000
```

## 📍 Lokasi PHP di Windows

Biasanya PHP ada di salah satu lokasi berikut:

- **XAMPP:** `C:\xampp\php\php.exe`
- **Laragon:** `C:\laragon\bin\php\php-8.x-Win32\php.exe`
- **WAMP:** `C:\wamp64\bin\php\php-8.x\php.exe`

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

### 3. Database error

Hapus file `database/laundry.db` dan jalankan ulang init:
```bash
php database/init.php
```

### 4. CSS tidak muncul

Pastikan Tailwind sudah di-compile:
```bash
npm run build
```

## 📚 Workflow Development

### Saat Mengembangkan Aplikasi:

**Terminal 1 - Tailwind Watch:**
```bash
npm run dev
```
> Auto-compile CSS saat ada perubahan

**Terminal 2 - PHP Server:**
```bash
php -S localhost:8000
```
> Menjalankan aplikasi

### Saat Production Build:

```bash
npm run build
```
> Compile CSS dengan minify

## 🎯 Halaman yang Tersedia

1. **Dashboard:** `http://localhost:8000/pages/dashboard.php`
2. **Pelanggan:** `http://localhost:8000/pages/customers.php`
3. **Transaksi:** `http://localhost:8000/pages/transactions.php`
4. **Cek Order:** `http://localhost:8000/pages/check-order.php`

## 🎨 Next Steps

Setelah aplikasi berjalan, Anda bisa:

1. ✅ Tambah pelanggan di halaman Pelanggan
2. ✅ Buat transaksi baru di halaman Transaksi
3. ✅ Update status pesanan
4. ✅ Customer bisa cek status via halaman Cek Order

## 💡 Tips

- Gunakan **Ctrl+C** untuk stop server
- Refresh browser untuk melihat perubahan
- Check console browser (F12) jika ada error JavaScript
- Database SQLite ada di `database/laundry.db`

---

Selamat Coding! 🎉
