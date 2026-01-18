# 📋 MATERI PRESENTASI SIDANG PROJECT BASED LEARNING
# D'FOUR SMART LAUNDRY SYSTEM

---

## 📌 INFORMASI PROJECT

| Item | Keterangan |
|------|------------|
| **Nama Project** | D'Four Smart Laundry System |
| **Teknologi Backend** | PHP Native dengan PDO |
| **Teknologi Frontend** | Tailwind CSS, HTML5, JavaScript |
| **Database** | MySQL 5.7+ |
| **Architecture** | MVC-like Pattern |

---

## 👥 PEMBAGIAN PRESENTASI TIM

### 1️⃣ ROFIQ - Landing Page & Autentikasi
**Halaman yang dipresentasikan:**
- `index.php` - Landing Page
- `pages/auth/login.php` - Halaman Login
- `pages/auth/register.php` - Halaman Registrasi

### 2️⃣ REYHAN - Dashboard Admin & Manajemen Pelanggan
**Halaman yang dipresentasikan:**
- `pages/dashboard.php` - Dashboard Admin
- `pages/customers.php` - Manajemen Pelanggan (CRUD)

### 3️⃣ FIKRI - Manajemen Transaksi
**Halaman yang dipresentasikan:**
- `pages/transactions.php` - Manajemen Transaksi
- `pages/transaction-detail.php` - Detail Transaksi

### 4️⃣ REZA - Pembayaran & Payment Gateway
**Halaman yang dipresentasikan:**
- `pages/payment.php` - Halaman Pembayaran
- `pages/payment-methods.php` - Metode Pembayaran
- Integrasi Midtrans (QRIS & Virtual Account)

### 5️⃣ ICA - Laporan & Customer Dashboard
**Halaman yang dipresentasikan:**
- `pages/reports.php` - Laporan Bulanan
- `pages/customer-dashboard.php` - Dashboard Pelanggan

---

# 📄 DETAIL SETIAP HALAMAN

---

## 1️⃣ ROFIQ - Landing Page & Autentikasi

### A. Landing Page (`index.php`)

#### 📝 Deskripsi
Halaman utama yang menampilkan informasi layanan laundry, harga, dan lokasi. Halaman ini adalah halaman publik yang bisa diakses tanpa login.

#### 🔧 Teknologi yang Digunakan
| Teknologi | Fungsi |
|-----------|--------|
| **PHP** | Server-side rendering untuk menampilkan konten dinamis |
| **Tailwind CSS** | Styling responsif dengan utility-first approach |
| **HTML5** | Struktur halaman dengan semantic elements |

#### 🎨 Fitur Tailwind CSS yang Diterapkan
```css
/* Gradient Background */
bg-gradient-to-br from-purple-50 via-white to-fuchsia-50

/* Glassmorphism Effect */
backdrop-blur-lg bg-white/95

/* Responsive Design */
grid md:grid-cols-3 gap-8

/* Animasi */
hover:scale-105 transition-transform duration-200
```

#### 💡 Komponen Utama
1. **Navigation Bar** - Fixed floating pill style dengan backdrop blur
2. **Hero Section** - Tagline dan CTA buttons
3. **Services Section** - Grid 3 kolom untuk layanan
4. **Location Section** - Google Maps embed dengan info kontak
5. **Footer** - Gradient purple dengan bubble images

#### ❓ Form pada Halaman Ini
> **TIDAK ADA FORM** - Halaman ini hanya menampilkan informasi statis

---

### B. Halaman Login (`pages/auth/login.php`)

#### 📝 Deskripsi
Halaman untuk user melakukan login ke sistem. Mendukung login dengan email/password dan Google OAuth.

#### 🔧 Method Form
```html
<form id="loginForm" class="space-y-5">
```

| Aspek | Detail |
|-------|--------|
| **Method** | **POST** (via JavaScript Fetch API) |
| **Action** | `api/auth/login.php` |
| **Input Fields** | email, password |
| **Validasi** | Client-side (required) + Server-side (PHP) |

#### 🔄 Alur Proses Login
```
1. User input email & password
2. JavaScript mengirim data via Fetch API (POST)
3. API memverifikasi credentials di database
4. Jika valid → session dibuat, redirect ke dashboard
5. Jika invalid → tampilkan error message
```

#### 🔐 Fitur Keamanan
1. **PDO Prepared Statements** - Mencegah SQL Injection
2. **Password Hashing** - `password_verify()` untuk verifikasi
3. **Session Management** - PHP Session untuk state management
4. **XSS Protection** - `htmlspecialchars()` untuk sanitize input

#### 🎨 Fitur Tailwind CSS
```css
/* Split Layout */
flex flex-col md:flex-row min-h-screen

/* Glass Bubbles Animation */
.glass-bubble { animation: float 10s ease-in-out infinite; }

/* Form Styling */
form-input rounded-xl focus:ring-2 focus:ring-primary-500
```

---

### C. Halaman Register (`pages/auth/register.php`)

#### 📝 Deskripsi
Halaman untuk user baru mendaftar akun. Mengumpulkan data nama, email, phone, dan password.

#### 🔧 Method Form
```html
<form id="registerForm" class="space-y-4">
```

| Aspek | Detail |
|-------|--------|
| **Method** | **POST** (via JavaScript Fetch API) |
| **Action** | `api/auth/register.php` |
| **Input Fields** | name, email, phone, password, password_confirm |
| **Validasi** | Client-side + Server-side |

#### 🔄 Alur Proses Registrasi
```
1. User mengisi form registrasi
2. JavaScript memvalidasi input (password match, format)
3. Data dikirim via Fetch API (POST)
4. Server memvalidasi uniqueness (email, phone)
5. Password di-hash dengan password_hash()
6. Data disimpan ke tabel `users`
7. Email verifikasi dikirim (jika dikonfigurasi)
8. Redirect ke login dengan pesan sukses
```

#### 💾 Query Database
```php
$stmt = $db->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
```

---

## 2️⃣ REYHAN - Dashboard Admin & Manajemen Pelanggan

### A. Dashboard Admin (`pages/dashboard.php`)

#### 📝 Deskripsi
Halaman dashboard untuk admin yang menampilkan statistik penjualan, pesanan aktif, dan transaksi terbaru.

#### 🔐 Proteksi Halaman
```php
require_once __DIR__ . '/../includes/auth.php';
requireLogin(); // Redirect to login if not authenticated
```

#### 📊 Statistik yang Ditampilkan
| Statistik | Query SQL |
|-----------|-----------|
| **Penjualan Hari Ini** | `SUM(price) WHERE DATE(created_at) = CURDATE()` |
| **Pesanan Aktif** | `COUNT(*) WHERE status NOT IN ('done', 'picked_up', 'cancelled')` |
| **Total Pelanggan** | `COUNT(*) FROM customers` |
| **Pendapatan Bulan Ini** | `SUM(price) WHERE DATE_FORMAT = current month` |

#### 🔧 Method Query Database
```php
// Menggunakan PDO untuk query aman
$db = Database::getInstance()->getConnection();
$todayQuery = $db->query("SELECT COALESCE(SUM(price), 0) as total FROM transactions WHERE DATE(created_at) = CURDATE()");
```

#### 🎨 Komponen Tailwind CSS
```css
/* Statistics Cards */
.dash-stat-card - Card dengan gradient colors
.dash-stat-pink - Pink gradient untuk penjualan
.dash-stat-cyan - Cyan gradient untuk pesanan aktif
.dash-stat-light - Light gradient untuk pelanggan
.dash-stat-orange - Orange gradient untuk pendapatan

/* Hover Effects */
hover:scale-105 transition-transform duration-200
```

#### ❓ Form pada Halaman Ini
> **TIDAK ADA FORM** - Dashboard hanya menampilkan data read-only

---

### B. Manajemen Pelanggan (`pages/customers.php`)

#### 📝 Deskripsi
Halaman CRUD (Create, Read, Update, Delete) untuk mengelola data pelanggan laundry.

#### 🔧 Method Form - TAMBAH PELANGGAN
```html
<form method="POST" action="">
    <input type="hidden" name="action" value="create">
    <!-- fields: name, phone, address -->
</form>
```

| Aspek | Detail |
|-------|--------|
| **Method** | **POST** |
| **Action** | Same page (self-submit) |
| **Hidden Field** | `action` = create / update / delete |

#### 🔄 Proses Berdasarkan Action

**CREATE (Tambah Pelanggan):**
```php
if ($action === 'create') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address'] ?? '');
    
    $stmt = $db->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $address]);
}
```

**UPDATE (Edit Pelanggan):**
```php
if ($action === 'update') {
    $stmt = $db->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $address, $id]);
}
```

**DELETE (Hapus Pelanggan):**
```php
if ($action === 'delete') {
    $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
}
```

#### 🔐 Fitur Keamanan
1. **sanitize()** - Membersihkan input dari karakter berbahaya
2. **Prepared Statements** - Mencegah SQL Injection
3. **Foreign Key** - Proteksi integritas data dengan transaksi

---

## 3️⃣ FIKRI - Manajemen Transaksi

### A. Halaman Transaksi (`pages/transactions.php`)

#### 📝 Deskripsi
Halaman untuk mengelola semua transaksi laundry. Admin dapat membuat transaksi baru, update status, dan melihat detail.

#### 🔧 Method Form - TAMBAH TRANSAKSI
```html
<form method="POST" action="../api/transactions-api.php" id="transaction-form">
```

| Aspek | Detail |
|-------|--------|
| **Method** | **POST** |
| **Action** | `api/transactions-api.php` |
| **Input Fields** | customer_id, service_type, weight/quantity, notes |

#### 📊 Fitur Update Status via AJAX
```javascript
function updateStatus(transactionId, newStatus) {
    fetch('../api/transactions-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'update_status',
            id: transactionId,
            status: newStatus
        })
    });
}
```

#### 🔄 Alur Transaksi (Status Flow)
```
pending → washing → drying → ironing → done → picked_up
           ↘ cancelled (bisa dari status mana saja)
```

#### 💾 Struktur Tabel Transactions
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key, Auto Increment |
| customer_id | INT | Foreign Key ke customers |
| service_type | VARCHAR | Jenis layanan |
| weight | DECIMAL(10,2) | Berat (kg) |
| quantity | INT | Jumlah (pcs) |
| price | DECIMAL(10,2) | Total harga |
| status | ENUM | Status transaksi |
| payment_status | ENUM | unpaid, pending, paid |
| created_at | TIMESTAMP | Waktu dibuat |

#### 🎨 Komponen Tailwind CSS
```css
/* Status Badge */
.badge - Base styling untuk badge
.badge-yellow - Status pending
.badge-blue - Status processing
.badge-green - Status completed

/* Table Styling */
.table - Custom table dengan borders
.table-wrapper - Overflow handling untuk responsive
```

---

### B. Detail Transaksi (`pages/transaction-detail.php`)

#### 📝 Deskripsi
Halaman untuk melihat detail lengkap satu transaksi termasuk informasi pelanggan, layanan, harga, dan riwayat pembayaran.

#### 🔧 Method Akses
```php
// Mengambil ID dari URL parameter (GET)
$transactionId = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->execute([$transactionId]);
```

| Aspek | Detail |
|-------|--------|
| **Method** | **GET** (URL Parameter) |
| **Parameter** | `?id=123` |
| **Query** | JOIN transactions + customers + payments |

#### ❓ Form pada Halaman Ini
> Halaman ini bersifat **READ-ONLY** untuk menampilkan detail

---

## 4️⃣ REZA - Pembayaran & Payment Gateway

### A. Halaman Pembayaran (`pages/payment.php`)

#### 📝 Deskripsi
Halaman untuk pelanggan melakukan pembayaran transaksi. Terintegrasi dengan Midtrans untuk pembayaran online (QRIS & Virtual Account).

#### 🔧 Integrasi Midtrans
```javascript
// Midtrans Snap.js
snap.pay(snapToken, {
    onSuccess: function(result) {
        // Update payment status ke 'paid'
    },
    onPending: function(result) {
        // Update payment status ke 'pending'
    },
    onError: function(result) {
        // Handle error
    },
    onClose: function() {
        // User menutup popup
    }
});
```

#### 💳 Metode Pembayaran yang Didukung
| Metode | Keterangan |
|--------|------------|
| **Cash** | Pembayaran langsung di tempat |
| **Bank Transfer** | Transfer ke rekening (BCA, Mandiri, BNI, BRI) |
| **QRIS** | Scan QR code via Midtrans |
| **E-Wallet** | OVO, GoPay, ShopeePay via Midtrans |

#### 🔄 Alur Payment
```
1. User memilih transaksi yang akan dibayar
2. Sistem generate Snap Token dari Midtrans
3. User memilih metode pembayaran
4. Pembayaran diproses
5. Callback dari Midtrans update status
6. Admin dapat konfirmasi pembayaran manual
```

---

### B. Metode Pembayaran (`pages/payment-methods.php`)

#### 📝 Deskripsi
Halaman admin untuk mengelola metode pembayaran yang tersedia (CRUD rekening bank).

#### 🔧 Method Form
```html
<form method="POST" action="">
    <input type="hidden" name="action" value="create">
```

| Aspek | Detail |
|-------|--------|
| **Method** | **POST** |
| **Action** | Self-submit |
| **Fields** | name, type, bank_name, account_number, account_holder |

#### 💾 Tabel Payment Methods
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key |
| name | VARCHAR(50) | Nama metode |
| type | ENUM | cash, bank_transfer, ewallet, qris |
| bank_name | VARCHAR(50) | Nama bank |
| account_number | VARCHAR(30) | Nomor rekening |
| account_holder | VARCHAR(100) | Nama pemilik rekening |
| is_active | TINYINT | Status aktif/nonaktif |

---

## 5️⃣ ICA - Laporan & Customer Dashboard

### A. Laporan Bulanan (`pages/reports.php`)

#### 📝 Deskripsi
Halaman untuk melihat laporan penjualan bulanan dengan grafik dan tabel detail. Mendukung export ke CSV dan Print PDF.

#### 🔧 Method Filter
```html
<form method="GET" action="">
    <select name="month">...</select>
    <select name="year">...</select>
    <button type="submit">Filter</button>
</form>
```

| Aspek | Detail |
|-------|--------|
| **Method** | **GET** (Filter) |
| **Parameters** | month, year |
| **Export** | CSV download, Print PDF |

#### 📊 Data yang Ditampilkan
1. **Summary Statistics** - Total pendapatan, jumlah transaksi
2. **Chart** - Grafik pendapatan per hari
3. **Transaction Table** - Daftar transaksi detail
4. **Service Breakdown** - Pendapatan per jenis layanan

#### 🔗 Export Functions
```javascript
// Export CSV
function exportCSV() {
    window.location.href = 'api/export-report.php?format=csv&month=' + month + '&year=' + year;
}

// Print PDF
function printReport() {
    window.print();
}
```

---

### B. Customer Dashboard (`pages/customer-dashboard.php`)

#### 📝 Deskripsi
Halaman dashboard khusus untuk pelanggan terdaftar. Menampilkan transaksi aktif, riwayat pesanan, dan akses ke pembayaran.

#### 🔐 Akses Halaman
```php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Verifikasi role user
$userData = getUserData();
if ($userData['role'] !== 'user') {
    redirect('dashboard.php'); // Redirect admin ke admin dashboard
}
```

#### 📋 Fitur Customer Dashboard
| Fitur | Deskripsi |
|-------|-----------|
| **Pesanan Aktif** | Menampilkan transaksi yang sedang diproses |
| **Riwayat Pesanan** | Semua transaksi yang sudah selesai |
| **Status Tracking** | Progress visual status cucian |
| **Pembayaran** | Link ke halaman payment |
| **Profile** | Informasi akun pelanggan |

#### 🎨 Komponen UI
```css
/* Sidebar Navigation */
w-20 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900

/* Status Progress Bar */
progress-bar dengan animasi step-by-step

/* Card Design */
.payment-card dengan glassmorphism effect
```

---

# 🔧 RINGKASAN TEKNOLOGI

## PHP Methods yang Digunakan

| Halaman | Form Method | Tujuan |
|---------|-------------|--------|
| `login.php` | POST (AJAX) | Submit credentials |
| `register.php` | POST (AJAX) | Submit registration data |
| `customers.php` | POST | CRUD pelanggan |
| `transactions.php` | POST | CRUD transaksi |
| `payment-methods.php` | POST | CRUD metode pembayaran |
| `reports.php` | GET | Filter laporan |
| `transaction-detail.php` | GET | Ambil detail transaksi |

## Tailwind CSS Highlights

| Fitur | Class |
|-------|-------|
| **Responsive** | `md:grid-cols-2 lg:grid-cols-4` |
| **Gradient** | `bg-gradient-to-br from-purple-50 to-fuchsia-50` |
| **Glassmorphism** | `backdrop-blur-lg bg-white/95` |
| **Animation** | `hover:scale-105 transition-all duration-200` |
| **Dark Theme** | `from-indigo-900 via-purple-900 to-indigo-900` |

## Keamanan

| Teknik | Implementasi |
|--------|--------------|
| **SQL Injection Prevention** | PDO Prepared Statements |
| **XSS Prevention** | `htmlspecialchars()` |
| **Password Security** | `password_hash()` & `password_verify()` |
| **Session Management** | PHP Session dengan regenerate ID |
| **Input Sanitization** | Custom `sanitize()` function |

---

# 📝 TIPS MENJAWAB PERTANYAAN SIDANG

## ❓ Jika Ditanya tentang Form

**Pertanyaan:** "Method apa yang digunakan pada form ini?"

**Jawab:**
> "Form ini menggunakan method **POST** karena kita mengirimkan data sensitif / melakukan perubahan data ke server. POST lebih aman karena data tidak terlihat di URL dan tidak ada batasan ukuran data."

## ❓ Jika Ditanya tentang Database

**Pertanyaan:** "Bagaimana cara mencegah SQL Injection?"

**Jawab:**
> "Kami menggunakan **PDO Prepared Statements**. Query disiapkan terlebih dahulu dengan placeholder (?), lalu data dieksekusi terpisah. Ini mencegah input user dieksekusi sebagai SQL command."

```php
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

## ❓ Jika Ditanya tentang Tailwind CSS

**Pertanyaan:** "Kenapa menggunakan Tailwind CSS?"

**Jawab:**
> "Tailwind CSS dipilih karena:
> 1. **Utility-first** - Class langsung di HTML, development lebih cepat
> 2. **Responsive** - Prefix seperti `md:`, `lg:` untuk breakpoint
> 3. **Customizable** - Mudah dikonfigurasi via `tailwind.config.js`
> 4. **Purge** - File CSS final sangat kecil karena unused class dihapus"

---

# 📄 STRUKTUR DATABASE (ERD)

```
┌─────────────┐     ┌──────────────┐     ┌──────────────────┐
│   users     │────<│  customers   │────<│   transactions   │
└─────────────┘     └──────────────┘     └──────────────────┘
       │                                          │
       │                                          │
       ▼                                          ▼
┌─────────────────┐                      ┌──────────────┐
│ password_resets │                      │   payments   │
└─────────────────┘                      └──────────────┘
                                                 │
                                                 ▼
                                        ┌────────────────┐
                                        │ payment_methods│
                                        └────────────────┘
```

---

**Dokumen ini dibuat untuk keperluan Sidang Project Based Learning**  
**D'Four Smart Laundry System - 2026**
