# Panduan Migrasi Database dari SQLite ke MySQL

## 📋 Daftar Isi
1. [Persiapan](#persiapan)
2. [Langkah-langkah Migrasi](#langkah-langkah-migrasi)
3. [Perbedaan SQLite vs MySQL](#perbedaan-sqlite-vs-mysql)
4. [Testing](#testing)
5. [Rollback](#rollback)

---

## 🔧 Persiapan

### 1. Pastikan MySQL sudah terinstall
- Buka XAMPP Control Panel
- Start Apache dan MySQL
- Pastikan MySQL berjalan di port 3306 (default)

### 2. Buat Database Baru
Buka phpMyAdmin (http://localhost/phpmyadmin) atau gunakan MySQL CLI:

```sql
CREATE DATABASE laundry_dfour CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Konfigurasi Database (Opsional)
Edit file `config/database_mysql.php` jika perlu mengubah:
- Host (default: localhost)
- Database name (default: laundry_dfour)
- Username (default: root)
- Password (default: kosong)

---

## 🚀 Langkah-langkah Migrasi

### Step 1: Backup Data SQLite (Opsional)
Jika sudah ada data di SQLite, backup terlebih dahulu:
```bash
cp database/laundry.db database/laundry.db.backup
```

### Step 2: Inisialisasi Database MySQL
Jalankan script inisialisasi:
```bash
php database/init_mysql.php
```

Output yang diharapkan:
```
🚀 Starting MySQL database initialization...

✅ Table 'customers' created
✅ Table 'service_types' created
✅ Table 'transactions' created

📦 Inserting default service types...
  ✓ Cuci Kering
  ✓ Cuci Setrika
  ✓ Setrika Saja
  ...

🎉 Database initialized successfully!
```

### Step 3: Update File Konfigurasi
Ganti referensi database di semua file PHP:

**Dari:**
```php
require_once __DIR__ . '/config/database.php';
```

**Menjadi:**
```php
require_once __DIR__ . '/config/database_mysql.php';
```

File yang perlu diupdate:
- `api/customers.php`
- `api/transactions.php`
- `api/services.php`
- `pages/dashboard.php`
- `pages/customers.php`
- `pages/transactions.php`
- `pages/check-order.php`

### Step 4: Migrasi Data (Jika ada data di SQLite)
Jika Anda memiliki data di SQLite yang perlu dipindahkan, jalankan script migrasi:
```bash
php database/migrate_data.php
```

### Step 5: Testing
Test semua fitur aplikasi:
- ✅ Tambah customer baru
- ✅ Buat transaksi baru
- ✅ Update status transaksi
- ✅ Cek order
- ✅ Dashboard statistics

---

## 📊 Perbedaan SQLite vs MySQL

### Tipe Data

| SQLite | MySQL | Keterangan |
|--------|-------|------------|
| `INTEGER PRIMARY KEY AUTOINCREMENT` | `INT AUTO_INCREMENT PRIMARY KEY` | ID auto increment |
| `TEXT` | `VARCHAR(255)` atau `TEXT` | String |
| `REAL` | `DECIMAL(10,2)` | Angka desimal |
| `INTEGER DEFAULT 1` | `TINYINT(1) DEFAULT 1` | Boolean |
| `DATETIME DEFAULT CURRENT_TIMESTAMP` | `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` | Timestamp |

### Fitur Tambahan MySQL

1. **ENUM untuk status**
   ```sql
   status ENUM('pending', 'processing', 'ready', 'completed', 'cancelled')
   ```

2. **ON UPDATE CURRENT_TIMESTAMP**
   ```sql
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   ```

3. **Storage Engine**
   ```sql
   ENGINE=InnoDB
   ```

4. **Character Set & Collation**
   ```sql
   DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
   ```

### Query yang Perlu Disesuaikan

**SQLite:**
```php
$db->exec("PRAGMA foreign_keys = ON;");
```

**MySQL:**
```php
// Foreign keys sudah aktif secara default di InnoDB
// Tidak perlu command tambahan
```

---

## 🧪 Testing

### 1. Test Koneksi Database
```bash
php -r "require 'config/database_mysql.php'; echo 'Connection OK';"
```

### 2. Test CRUD Operations

**Create:**
```bash
curl -X POST http://localhost/Laundry%20D'four/api/customers.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","phone":"08123456789","address":"Test Address"}'
```

**Read:**
```bash
curl http://localhost/Laundry%20D'four/api/customers.php
```

### 3. Cek di phpMyAdmin
- Buka http://localhost/phpmyadmin
- Pilih database `laundry_dfour`
- Verifikasi semua tabel dan data

---

## 🔄 Rollback

Jika terjadi masalah dan ingin kembali ke SQLite:

### 1. Revert perubahan di Git
```bash
git checkout main -- config/database.php
git checkout main -- database/init.php
```

### 2. Restore file yang diubah
Kembalikan semua `require_once` ke file database.php yang lama

### 3. Restore backup (jika ada)
```bash
cp database/laundry.db.backup database/laundry.db
```

---

## 📝 Checklist Migrasi

- [ ] Backup database SQLite (jika ada data)
- [ ] Buat database MySQL baru
- [ ] Jalankan `init_mysql.php`
- [ ] Update semua file PHP untuk menggunakan `database_mysql.php`
- [ ] Migrasi data (jika perlu)
- [ ] Test semua fitur aplikasi
- [ ] Verifikasi di phpMyAdmin
- [ ] Commit perubahan ke Git
- [ ] Update dokumentasi

---

## 🆘 Troubleshooting

### Error: "SQLSTATE[HY000] [1049] Unknown database"
**Solusi:** Database belum dibuat. Buat database terlebih dahulu di phpMyAdmin.

### Error: "Access denied for user 'root'@'localhost'"
**Solusi:** Periksa username dan password di `config/database_mysql.php`

### Error: "SQLSTATE[HY000] [2002] No connection could be made"
**Solusi:** MySQL belum running. Start MySQL di XAMPP Control Panel.

### Data tidak muncul setelah migrasi
**Solusi:** Pastikan script `migrate_data.php` sudah dijalankan dengan benar.

---

## 📞 Support

Jika ada pertanyaan atau masalah, silakan:
1. Cek error log di XAMPP: `xampp/mysql/data/mysql_error.log`
2. Cek PHP error log
3. Review dokumentasi MySQL: https://dev.mysql.com/doc/

---

**Dibuat pada:** 2025-12-17  
**Branch:** migrate-to-mysql  
**Status:** In Progress
