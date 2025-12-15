# D'four Laundry Management System 🧺

Sistem manajemen laundry modern menggunakan **PHP Native**, **Tailwind CSS**, dan **SQLite**.

## 🚀 Fitur Utama

- ✅ **Dashboard** - Statistik penjualan dan ringkasan
- ✅ **Manajemen Pelanggan** - CRUD data pelanggan
- ✅ **Manajemen Transaksi** - Kelola order laundry
- ✅ **Cek Status Order** - Portal untuk customer
- ✅ **Update Status Real-time** - Tracking progress pesanan
- ✅ **Responsive Design** - Mobile-friendly interface

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- SQLite Extension (biasanya sudah termasuk di PHP)
- Node.js & npm (untuk Tailwind CSS)
- Web Server (Apache/Nginx) atau PHP Built-in Server

## 🛠️ Instalasi

### 1. Clone atau Download Project

```bash
cd "C:\Users\TOSHIBA\Desktop\Laundry D'four"
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

### 4. Inisialisasi Database

Jalankan file `database/init.php` untuk membuat database dan tabel:

```bash
php database/init.php
```

Output yang diharapkan:
```
✅ Default service types created
✅ Database initialized successfully!
📊 Tables created: customers, transactions, service_types
🔗 Indexes created for better performance
```

### 5. Jalankan Server

**Menggunakan PHP Built-in Server:**
```bash
php -S localhost:8000
```

Akses aplikasi di: `http://localhost:8000`

## 📁 Struktur Folder

```
dfour-laundry-native/
├── assets/
│   ├── css/
│   │   ├── input.css          # Tailwind source
│   │   └── style.css          # Compiled CSS
│   ├── js/
│   │   └── main.js            # JavaScript utilities
│   └── images/
├── config/
│   └── database.php           # Database connection
├── includes/
│   ├── header.php             # Header component
│   ├── footer.php             # Footer component
│   └── functions.php          # Helper functions
├── database/
│   ├── init.php               # Database schema
│   └── laundry.db            # SQLite database
├── pages/
│   ├── dashboard.php          # Dashboard page
│   ├── customers.php          # Customers management
│   ├── transactions.php       # Transactions management
│   └── check-order.php        # Customer portal
├── api/
│   ├── customers-api.php      # Customers API
│   └── transactions-api.php   # Transactions API
├── index.php                  # Entry point
├── package.json
└── tailwind.config.js
```

## 🎨 Teknologi

- **Backend:** PHP Native dengan PDO
- **Database:** SQLite
- **Frontend:** HTML, Tailwind CSS, Vanilla JavaScript
- **Architecture:** MVC-like pattern

## 📊 Database Schema

### Tabel `customers`
- `id` - Primary key
- `name` - Nama pelanggan
- `phone` - Nomor telepon (unique)
- `address` - Alamat
- `created_at` - Timestamp

### Tabel `transactions`
- `id` - Primary key
- `customer_id` - Foreign key ke customers
- `service_type` - Jenis layanan
- `weight` - Berat (kg)
- `quantity` - Jumlah (pcs)
- `price` - Total harga
- `status` - Status order
- `notes` - Catatan
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Tabel `service_types`
- `id` - Primary key
- `name` - Nama layanan
- `unit` - Satuan (kg/pcs)
- `price_per_unit` - Harga per satuan
- `description` - Deskripsi
- `is_active` - Status aktif

## 🔧 Development Workflow

1. **Jalankan Tailwind watch mode:**
   ```bash
   npm run dev
   ```

2. **Jalankan PHP server:**
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

## 📄 License

MIT License - Bebas digunakan untuk project pribadi atau komersial.

## 👨‍💻 Author

**D'four Laundry Team**

---

🎉 **Happy Coding!** Jika ada pertanyaan, silakan hubungi developer.
