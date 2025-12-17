# 🔄 Branch: migrate-to-mysql

Branch ini berisi migrasi database dari SQLite ke MySQL untuk aplikasi Laundry D'four Management System.

## 📋 Status: ✅ SELESAI

Semua file telah diupdate dan database MySQL telah berhasil dibuat dan diinisialisasi.

---

## 🚀 Quick Start

### 1. Pastikan MySQL Running
Buka XAMPP Control Panel dan pastikan MySQL sudah running.

### 2. Database Sudah Dibuat
Database `laundry_dfour` sudah dibuat dan tabel-tabel sudah diinisialisasi.

### 3. Test Aplikasi
Buka di browser:
```
http://localhost/Laundry%20D'four/pages/dashboard.php
```

---

## 📁 File-file Penting

### Dokumentasi:
- `MIGRATION_GUIDE.md` - Panduan lengkap migrasi
- `MIGRATION_COMPLETE.md` - Summary hasil migrasi

### Konfigurasi:
- `config/database_mysql.php` - Konfigurasi MySQL
- `database/init_mysql.php` - Inisialisasi tabel
- `database/migrate_data.php` - Migrasi data dari SQLite
- `database/create_database.php` - Script pembuat database

---

## 🔧 Konfigurasi Database

```php
Host: localhost
Database: laundry_dfour
Username: root
Password: (kosong)
Charset: utf8mb4
```

Edit di `config/database_mysql.php` jika perlu mengubah konfigurasi.

---

## 📊 Commits di Branch Ini

```
a96fe62 - Add migration completion summary and documentation
19493f0 - Update all files to use MySQL database configuration
44f2cec - Add MySQL migration files and documentation
90b7f9c - Update .gitignore before MySQL migration
```

---

## 🔀 Merge ke Main

Jika testing berhasil dan ingin merge ke main:

```bash
# Pastikan semua perubahan sudah di-commit
git status

# Pindah ke branch main
git checkout main

# Merge dari migrate-to-mysql
git merge migrate-to-mysql

# Push ke remote
git push origin main
```

---

## 🔄 Rollback

Jika ingin kembali ke SQLite:

```bash
# Pindah ke branch main
git checkout main

# Branch main masih menggunakan SQLite
```

---

## 📞 Troubleshooting

Lihat file `MIGRATION_GUIDE.md` untuk troubleshooting lengkap.

### Quick Fixes:

**Error: Unknown database**
```bash
e:\xampp2\php\php.exe database/create_database.php
```

**Error: Table doesn't exist**
```bash
e:\xampp2\php\php.exe database/init_mysql.php
```

---

**Last Updated:** 2025-12-17 21:11 WIB
