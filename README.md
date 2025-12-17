# D'four Laundry Management System 🧺

Sistem manajemen laundry modern menggunakan **PHP Native**, **Tailwind CSS**, dan **MySQL**.

## 🚀 Fitur Utama

- ✅ **Dashboard** - Statistik penjualan dan ringkasan
- ✅ **Manajemen Pelanggan** - CRUD data pelanggan
- ✅ **Manajemen Transaksi** - Kelola order laundry
- ✅ **Cek Status Order** - Portal untuk customer
- ✅ **Update Status Real-time** - Tracking progress pesanan
- ✅ **Responsive Design** - Mobile-friendly interface

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- PDO MySQL Extension (biasanya sudah termasuk di PHP)
- Node.js & npm (untuk Tailwind CSS)
- Web Server (Apache/Nginx) - **Recommended: XAMPP**

## 🛠️ Instalasi

### 1. Clone atau Download Project

```bash
git clone https://github.com/qifor27/laundry-D-four-system.git
cd laundry-D-four-system
```

Atau letakkan di folder htdocs XAMPP:
```bash
cd C:\xampp\htdocs\laundry-D-four
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Generate Tailwind CSS

**Development mode (watch):**
```bash
npm run dev
```

**Production build:**
```bash
npm run build
```

### 4. Setup MySQL Database

**a. Pastikan MySQL di XAMPP sudah running**

**b. Buat database MySQL:**
```bash
php database/create_database.php
```

Output yang diharapkan:
```
✅ Database 'laundry_dfour' berhasil dibuat!
📊 Character Set: utf8mb4
🔤 Collation: utf8mb4_unicode_ci
```

**c. Inisialisasi tabel dan data:**
```bash
php database/init_mysql.php
```

Output yang diharapkan:
```
✅ Table 'customers' created
✅ Table 'service_types' created
✅ Table 'transactions' created
📦 Inserting default service types...
✅ Default service types inserted
🎉 Database initialized successfully!
```

### 5. Jalankan Aplikasi

**Menggunakan XAMPP (Recommended):**

1. Pastikan Apache dan MySQL di XAMPP sudah running
2. Akses aplikasi di browser:
   ```
   http://localhost/laundry-D-four
   ```

**Atau menggunakan PHP Built-in Server:**
```bash
php -S localhost:8000
```
Akses di: `http://localhost:8000`

## 📁 Struktur Folder

```
laundry-D-four/
├── assets/
│   ├── css/
│   │   ├── input.css          # Tailwind source
│   │   └── style.css          # Compiled CSS
│   ├── js/
│   │   └── main.js            # JavaScript utilities
│   └── images/
├── config/
│   ├── database.php           # SQLite connection (legacy)
│   └── database_mysql.php     # MySQL connection (active)
├── includes/
│   ├── header.php             # Header component
│   ├── footer.php             # Footer component
│   └── functions.php          # Helper functions
├── database/
│   ├── create_database.php    # Create MySQL database
│   ├── init_mysql.php         # MySQL schema initialization
│   ├── init.php               # SQLite schema (legacy)
│   └── migrate_data.php       # Migration script
├── pages/
│   ├── dashboard.php          # Dashboard page
│   ├── customers.php          # Customers management
│   ├── transactions.php       # Transactions management
│   └── check-order.php        # Customer portal
├── api/
│   ├── customers-api.php      # Customers API
│   └── transactions-api.php   # Transactions API
├── docs/                      # Documentation files
├── index.php                  # Entry point
├── package.json
├── tailwind.config.js
└── start-server.bat           # Quick start script
```

## 🎨 Teknologi

- **Backend:** PHP Native dengan PDO
- **Database:** MySQL 5.7+
- **Frontend:** HTML, Tailwind CSS, Vanilla JavaScript
- **Architecture:** MVC-like pattern

## ⚙️ Konfigurasi Database

**File:** `config/database_mysql.php`

```php
private $host = "localhost";
private $db_name = "laundry_dfour";
private $username = "root";
private $password = "";
private $charset = "utf8mb4";
```

**Catatan:** Sesuaikan konfigurasi di atas dengan pengaturan MySQL Anda jika berbeda.

## 📊 Database Schema

### Tabel `customers`
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `name` - VARCHAR(255) NOT NULL
- `phone` - VARCHAR(20) UNIQUE NOT NULL
- `address` - TEXT
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### Tabel `transactions`
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `customer_id` - INT NOT NULL (Foreign key ke customers)
- `service_type` - VARCHAR(255) NOT NULL
- `weight` - DECIMAL(10,2) DEFAULT 0
- `quantity` - INT DEFAULT 1
- `price` - DECIMAL(10,2) NOT NULL
- `status` - ENUM('pending', 'processing', 'ready', 'completed', 'cancelled')
- `notes` - TEXT
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### Tabel `service_types`
- `id` - INT AUTO_INCREMENT PRIMARY KEY
- `name` - VARCHAR(255) NOT NULL UNIQUE
- `unit` - VARCHAR(10) NOT NULL (kg atau pcs)
- `price_per_unit` - DECIMAL(10,2) NOT NULL
- `description` - TEXT
- `is_active` - TINYINT(1) DEFAULT 1
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP

**Default Service Types:**
1. Cuci Kering - Rp 5.000/kg
2. Cuci Setrika - Rp 7.000/kg
3. Setrika Saja - Rp 4.000/kg
4. Bed Cover Single - Rp 15.000/pcs
5. Bed Cover Double - Rp 25.000/pcs
6. Selimut - Rp 20.000/pcs
7. Boneka Kecil - Rp 10.000/pcs
8. Boneka Besar - Rp 25.000/pcs

## 🔧 Development Workflow

### Menggunakan XAMPP (Recommended):

1. **Start Apache dan MySQL di XAMPP Control Panel**

2. **Jalankan Tailwind watch mode (Terminal 1):**
   ```bash
   npm run dev
   ```
   > Auto-compile CSS saat ada perubahan

3. **Akses aplikasi:**
   ```
   http://localhost/laundry-D-four
   ```

### Atau menggunakan PHP Built-in Server:

1. **Jalankan Tailwind watch mode (Terminal 1):**
   ```bash
   npm run dev
   ```

2. **Jalankan PHP server (Terminal 2):**
   ```bash
   php -S localhost:8000
   ```

3. **Edit files dan refresh browser** - Tailwind akan auto-compile

## 📱 Halaman Utama

1. **Dashboard** (`pages/dashboard.php`)
   - Statistik penjualan harian & bulanan
   - Jumlah pesanan aktif
   - Total pelanggan
   - Transaksi terbaru

2. **Pelanggan** (`pages/customers.php`)
   - Tambah/Edit/Hapus pelanggan
   - Search & filter
   - Modal forms

3. **Transaksi** (`pages/transactions.php`)
   - Buat transaksi baru
   - Update status pesanan
   - Filter berdasarkan status
   - Auto-calculate pricing

4. **Cek Order** (`pages/check-order.php`)
   - Portal untuk customer
   - Cek status by phone number
   - Progress tracking visualization

## 🎯 Fitur JavaScript

- Modal management
- AJAX form submissions
- Real-time filtering
- Currency & phone formatting
- Notifications system
- Auto-calculate pricing

## 📝 Helper Functions

File `includes/functions.php` berisi:
- `formatRupiah()` - Format currency
- `formatDate()` - Format tanggal
- `sanitize()` - Sanitize input
- `getStatusBadge()` - Status badge styling
- `setFlashMessage()` - Flash messages
- `baseUrl()` - Base URL helper
- `jsonResponse()` - API response

## 🔒 Security

- PDO Prepared Statements (SQL Injection prevention)
- Input sanitization
- XSS protection with `htmlspecialchars()`
- Session management
- Foreign key constraints

## 🎨 Custom CSS Classes

**Buttons:**
- `.btn-primary` - Primary button
- `.btn-secondary` - Secondary button
- `.btn-success` - Success button
- `.btn-danger` - Danger button

**Components:**
- `.card` - Standard card
- `.card-gradient` - Gradient card
- `.badge` - Status badge
- `.form-input` - Form input
- `.modal-overlay` - Modal overlay
- `.modal-content` - Modal content

## � Troubleshooting

### 1. Error: "Connection refused" saat membuat database

**Penyebab:** MySQL belum running

**Solusi:**
- Buka XAMPP Control Panel
- Klik tombol "Start" pada MySQL
- Tunggu hingga status berubah menjadi hijau
- Jalankan ulang `php database/create_database.php`

### 2. Error: "Access denied for user 'root'"

**Penyebab:** Username atau password MySQL salah

**Solusi:**
- Edit file `config/database_mysql.php`
- Sesuaikan `$username` dan `$password` dengan konfigurasi MySQL Anda
- Simpan dan jalankan ulang script database

### 3. Error: "Database 'laundry_dfour' doesn't exist"

**Penyebab:** Database belum dibuat

**Solusi:**
```bash
php database/create_database.php
php database/init_mysql.php
```

### 4. CSS tidak muncul / tampilan berantakan

**Penyebab:** Tailwind CSS belum di-compile

**Solusi:**
```bash
npm install
npm run build
```

### 5. Error: "Table doesn't exist"

**Penyebab:** Tabel belum diinisialisasi

**Solusi:**
```bash
php database/init_mysql.php
```

### 6. Port 8000 sudah digunakan (jika pakai PHP Built-in Server)

**Solusi:** Gunakan port lain
```bash
php -S localhost:8080
```

### 7. Halaman blank / error 500

**Solusi:**
- Cek error log di `C:\xampp\apache\logs\error.log`
- Pastikan semua file PHP tidak ada syntax error
- Pastikan MySQL sudah running
- Pastikan database sudah dibuat dan tabel sudah ada


## �📄 License

MIT License - Bebas digunakan untuk project pribadi atau komersial.

## 👨‍💻 Author

**D'four Laundry Team**

---

🎉 **Happy Coding!** Jika ada pertanyaan, silakan hubungi developer.
