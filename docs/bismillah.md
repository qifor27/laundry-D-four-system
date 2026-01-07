# Dokumentasi Implementasi D'four Smart Laundry System

> **Nama Proyek:** D'four Smart Laundry System  
> **Teknologi:** PHP Native, MySQL, Tailwind CSS, Google OAuth 2.0  
> **Versi:** 1.0.0

---

## Daftar Isi

1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Struktur Database](#2-struktur-database)
3. [Penjelasan REST API](#3-penjelasan-rest-api)
4. [Implementasi CRUD](#4-implementasi-crud)
5. [Sistem Autentikasi](#5-sistem-autentikasi)
6. [Frontend & UI/UX](#6-frontend--uiux)
7. [Panduan Pengujian](#7-panduan-pengujian)

---

## 1. Arsitektur Sistem

### 1.1 Struktur Folder

```
laundry-D-four/
├── api/                    # REST API endpoints
│   ├── auth/               # Auth API (login, register, dll)
│   ├── customers-api.php   # CRUD Pelanggan
│   ├── transactions-api.php # CRUD Transaksi
│   ├── google-auth.php     # Google OAuth handler
│   └── logout.php          # Logout handler
├── config/                 # Konfigurasi
│   ├── database_mysql.php  # Koneksi database
│   ├── google-oauth.php    # Kredensial Google OAuth
│   └── email.php           # Konfigurasi email
├── database/               # Database scripts
│   ├── init_mysql.php      # Inisialisasi database
│   ├── migrate_auth.php    # Migrasi auth columns
│   └── create_admin.php    # Buat akun admin
├── includes/               # Shared components
│   ├── functions.php       # Helper functions
│   ├── auth.php            # Auth middleware
│   ├── email.php           # Email functions
│   ├── header-admin.php    # Header admin
│   └── sidebar-admin.php   # Sidebar admin
├── pages/                  # Halaman web
│   ├── auth/               # Auth pages
│   │   ├── login.php       # Login pelanggan
│   │   ├── register.php    # Registrasi
│   │   ├── admin-login.php # Login admin
│   │   ├── forgot-password.php
│   │   └── reset-password.php
│   ├── dashboard.php       # Dashboard admin
│   ├── customers.php       # Manajemen pelanggan
│   ├── transactions.php    # Manajemen transaksi
│   ├── customer-dashboard.php # Dashboard pelanggan
│   └── check-order.php     # Cek status order
├── assets/                 # Static assets
│   └── css/style.css       # Compiled Tailwind CSS
├── index.php               # Landing page
└── tailwind.config.js      # Tailwind configuration
```

### 1.2 Diagram Arsitektur

```
+-------------------------------------------------------------------+
|                       FRONTEND (Browser)                          |
|  +-------------+  +-------------+  +---------------+              |
|  | Landing     |  | Auth Pages  |  | Admin/Customer|              |
|  | Page        |  | (Login,     |  | Dashboard     |              |
|  | (index.php) |  | Register)   |  |               |              |
|  +------+------+  +------+------+  +-------+-------+              |
+---------|-----------------|-----------------|-----------------------+
          |                 |                 |
          | HTTP Request    | AJAX/Fetch      | AJAX/Fetch
          v                 v                 v
+-------------------------------------------------------------------+
|                        BACKEND (PHP)                              |
|  +-------------------------------------------------------------+  |
|  |                   includes/auth.php                         |  |
|  |       (Session Management, Role-based Access Control)       |  |
|  +-------------------------------------------------------------+  |
|                                                                   |
|  +---------------+  +---------------+  +---------------+          |
|  | api/auth/     |  | api/customers |  | api/transactions|        |
|  | - login.php   |  | -api.php      |  | -api.php      |          |
|  | - register    |  | (CRUD)        |  | (CRUD)        |          |
|  +-------+-------+  +-------+-------+  +-------+-------+          |
+-----------|-----------------|-----------------|--------------------+
            |                 |                 |
            v                 v                 v
+-------------------------------------------------------------------+
|                      DATABASE (MySQL)                             |
|  +----------+  +----------+  +------------+  +---------------+    |
|  |  users   |  | customers|  | transactions|  | service_types |   |
|  +----------+  +----------+  +------------+  +---------------+    |
+-------------------------------------------------------------------+
```

---

## 2. Struktur Database

### 2.1 Entity Relationship Diagram (ERD)

```
+------------------+         +------------------+
|     users        |         |   customers      |
+------------------+         +------------------+
| id (PK)          |         | id (PK)          |
| google_id        |         | name             |
| email (UNIQUE)   |         | phone (UNIQUE)   |
| name             |         | address          |
| password_hash    |         | created_at       |
| profile_picture  |         +--------+---------+
| role             |                  |
| login_method     |                  | 1:N
| is_active        |                  |
| email_verified   |                  v
| created_at       |         +------------------+
| last_login       |         |  transactions    |
+------------------+         +------------------+
                             | id (PK)          |
+------------------+         | customer_id (FK) |
| service_types    |         | service_type     |
+------------------+    N:1  | weight           |
| id (PK)          |<--------| total_price      |
| name             |         | status           |
| unit             |         | notes            |
| price_per_unit   |         | created_at       |
| is_active        |         | completed_at     |
+------------------+         +------------------+

+------------------+
| password_resets  |
+------------------+
| id (PK)          |
| email            |
| token (UNIQUE)   |
| expires_at       |
| used_at          |
| created_at       |
+------------------+
```

### 2.2 Detail Tabel

#### Tabel `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT AUTO_INCREMENT | Primary Key |
| google_id | VARCHAR(255) | ID dari Google OAuth |
| email | VARCHAR(255) UNIQUE | Email unik |
| name | VARCHAR(255) | Nama lengkap |
| password_hash | VARCHAR(255) | Hash password (bcrypt) |
| role | ENUM | superadmin, admin, cashier, user |
| login_method | ENUM | email, google |
| email_verified_at | TIMESTAMP | Waktu verifikasi email |

#### Tabel `customers`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT AUTO_INCREMENT | Primary Key |
| name | VARCHAR(100) | Nama pelanggan |
| phone | VARCHAR(20) UNIQUE | Nomor HP |
| address | TEXT | Alamat (opsional) |

#### Tabel `transactions`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT AUTO_INCREMENT | Primary Key |
| customer_id | INT | Foreign Key ke customers |
| service_type | VARCHAR(50) | Jenis layanan |
| weight | DECIMAL(5,2) | Berat (kg) |
| total_price | INT | Total harga |
| status | ENUM | pending, washing, drying, ironing, done, picked_up |
| notes | TEXT | Catatan tambahan |

---

## 3. Penjelasan REST API

### 3.1 Apa itu REST API?

**REST API** (Representational State Transfer Application Programming Interface) adalah standar arsitektur untuk komunikasi antara **Frontend** (tampilan web) dan **Backend** (server/database) menggunakan protokol HTTP.

#### Analogi Sederhana

Bayangkan REST API seperti **pelayan di restoran**:

```
+----------------+                              +----------------+
|                |  "Pesan nasi goreng dong"   |                |
|   CUSTOMER     | ---------------------------> |     DAPUR      |
|  (Frontend)    |                              |   (Backend)    |
|                | <--------------------------- |                |
|                |     Ini nasi gorengnya       |                |
+----------------+                              +----------------+
                           ^
                           |
                   REST API = Pelayan
                   (menyampaikan pesanan)
```

- **Customer (Frontend):** Halaman web yang dilihat user
- **Dapur (Backend):** Server PHP + Database MySQL
- **Pelayan (REST API):** Jembatan yang menyampaikan request dan response

---

### 3.2 Cara Kerja REST API

```
+--------------------------------------------------------------------+
|                     ALUR KOMUNIKASI REST API                       |
+--------------------------------------------------------------------+
|                                                                    |
|  1. USER klik tombol "Tampilkan Pelanggan"                         |
|                          |                                         |
|                          v                                         |
|  2. JAVASCRIPT mengirim HTTP Request                               |
|     +-----------------------------------------------------+        |
|     | fetch('/api/customers-api.php', { method: 'GET' })  |        |
|     +-----------------------------------------------------+        |
|                          |                                         |
|                          v                                         |
|  3. SERVER (PHP) menerima request                                  |
|     - Memproses request                                            |
|     - Query ke database                                            |
|     - Menyiapkan response                                          |
|                          |                                         |
|                          v                                         |
|  4. SERVER mengirim RESPONSE (format JSON)                         |
|     +-----------------------------------------------------+        |
|     | { "success": true, "data": [...] }                  |        |
|     +-----------------------------------------------------+        |
|                          |                                         |
|                          v                                         |
|  5. JAVASCRIPT menerima response dan menampilkan di tabel          |
|                                                                    |
+--------------------------------------------------------------------+
```

---

### 3.3 HTTP Methods (CRUD Operations)

REST API menggunakan **HTTP Methods** untuk menentukan jenis operasi:

| Method | Operasi | Fungsi | Contoh Penggunaan |
|--------|---------|--------|-------------------|
| **GET** | **R**ead | Mengambil/membaca data | Lihat daftar pelanggan |
| **POST** | **C**reate | Membuat data baru | Tambah pelanggan baru |
| **PUT** | **U**pdate | Mengubah/update data | Edit nama pelanggan |
| **DELETE** | **D**elete | Menghapus data | Hapus pelanggan |

#### Ilustrasi:

```
+---------------------------------------------------------------------+
|                        CRUD = 4 HTTP Methods                        |
+---------------------------------------------------------------------+
|                                                                     |
|   [+] CREATE (POST)                                                 |
|   |-- Request:  POST /api/customers-api.php                         |
|   |-- Body:     { "name": "Budi", "phone": "0812..." }              |
|   +-- Response: { "success": true, "message": "Berhasil" }          |
|                                                                     |
|   [=] READ (GET)                                                    |
|   |-- Request:  GET /api/customers-api.php                          |
|   |-- Body:     (tidak ada)                                         |
|   +-- Response: { "success": true, "data": [...] }                  |
|                                                                     |
|   [~] UPDATE (PUT)                                                  |
|   |-- Request:  PUT /api/customers-api.php                          |
|   |-- Body:     { "id": 1, "name": "Budi Updated" }                 |
|   +-- Response: { "success": true, "message": "Berhasil" }          |
|                                                                     |
|   [-] DELETE (DELETE)                                               |
|   |-- Request:  DELETE /api/customers-api.php?id=1                  |
|   |-- Body:     (tidak ada)                                         |
|   +-- Response: { "success": true, "message": "Berhasil" }          |
|                                                                     |
+---------------------------------------------------------------------+
```

---

### 3.4 Format Data: JSON

REST API menggunakan **JSON (JavaScript Object Notation)** untuk pertukaran data karena:
- Ringan dan mudah dibaca
- Didukung semua bahasa pemrograman
- Format standar di web modern

#### Contoh Format JSON:

```json
// Request Body (POST - Tambah Pelanggan)
{
    "name": "Budi Santoso",
    "phone": "081234567890",
    "address": "Jl. Merdeka No. 10"
}

// Response Body (GET - Data Pelanggan)
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Budi Santoso",
            "phone": "081234567890",
            "address": "Jl. Merdeka No. 10"
        },
        {
            "id": 2,
            "name": "Ani Wijaya",
            "phone": "081298765432",
            "address": "Jl. Sudirman No. 5"
        }
    ]
}
```

---

### 3.5 Contoh Kode REST API

#### Frontend (JavaScript) - Mengirim Request

```javascript
// ===== GET: Ambil Semua Pelanggan =====
async function getCustomers() {
    const response = await fetch('/api/customers-api.php');
    const data = await response.json();
    console.log(data); // { success: true, data: [...] }
}

// ===== POST: Tambah Pelanggan Baru =====
async function addCustomer() {
    const response = await fetch('/api/customers-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: 'Budi Santoso',
            phone: '081234567890',
            address: 'Jl. Merdeka No. 10'
        })
    });
    const data = await response.json();
    console.log(data); // { success: true, message: "Berhasil" }
}

// ===== PUT: Update Pelanggan =====
async function updateCustomer() {
    const response = await fetch('/api/customers-api.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: 1,
            name: 'Budi Updated',
            phone: '081234567890'
        })
    });
    const data = await response.json();
}

// ===== DELETE: Hapus Pelanggan =====
async function deleteCustomer(id) {
    const response = await fetch(`/api/customers-api.php?id=${id}`, {
        method: 'DELETE'
    });
    const data = await response.json();
}
```

#### Backend (PHP) - Menerima & Memproses Request

```php
<?php
// File: api/customers-api.php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database_mysql.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE

switch ($method) {
    case 'GET':
        // READ - Ambil data pelanggan
        $stmt = $db->query("SELECT * FROM customers");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $customers]);
        break;

    case 'POST':
        // CREATE - Tambah pelanggan baru
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
        $stmt->execute([$input['name'], $input['phone'], $input['address']]);
        echo json_encode(['success' => true, 'message' => 'Pelanggan berhasil ditambahkan']);
        break;

    case 'PUT':
        // UPDATE - Edit pelanggan
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE customers SET name=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$input['name'], $input['phone'], $input['address'], $input['id']]);
        echo json_encode(['success' => true, 'message' => 'Pelanggan berhasil diupdate']);
        break;

    case 'DELETE':
        // DELETE - Hapus pelanggan
        $id = $_GET['id'];
        $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Pelanggan berhasil dihapus']);
        break;
}
```

---

### 3.6 Daftar API Endpoints Project Ini

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/customers-api.php` | GET | Ambil semua pelanggan |
| `/api/customers-api.php?id=1` | GET | Ambil pelanggan by ID |
| `/api/customers-api.php` | POST | Tambah pelanggan baru |
| `/api/customers-api.php` | PUT | Update pelanggan |
| `/api/customers-api.php?id=1` | DELETE | Hapus pelanggan |
| `/api/transactions-api.php` | GET | Ambil semua transaksi |
| `/api/transactions-api.php` | POST | Buat transaksi baru |
| `/api/transactions-api.php` | PUT | Update status transaksi |
| `/api/auth/login.php` | POST | Login user |
| `/api/auth/register.php` | POST | Registrasi user |
| `/api/google-auth.php` | POST | Login dengan Google |
| `/api/logout.php` | POST | Logout user |

---

### 3.7 Kenapa Menggunakan REST API?

| Tanpa REST API | Dengan REST API |
|----------------|-----------------|
| Halaman reload setiap aksi | Update tanpa reload (AJAX) |
| Pengalaman user lambat | Cepat dan responsif |
| Sulit dipakai mobile app | Bisa dipakai web, mobile, desktop |
| Kode tercampur (PHP+HTML) | Terpisah (Frontend & Backend) |
| Sulit di-maintain | Mudah dikembangkan |

---

### 3.8 External API yang Digunakan

Selain REST API internal, project ini juga menggunakan:

| API | Fungsi | URL |
|-----|--------|-----|
| **Google Identity Services** | Tombol "Login dengan Google" | `accounts.google.com/gsi/client` |
| **Google Token Verification** | Validasi token Google | `oauth2.googleapis.com/tokeninfo` |

---

## 4. Implementasi CRUD

### 4.1 CRUD Pelanggan (Customers)

**File:** `api/customers-api.php`

#### CREATE - Tambah Pelanggan Baru
```php
// Method: POST
// Endpoint: /api/customers-api.php

// Request Body:
{
    "name": "John Doe",
    "phone": "081234567890",
    "address": "Jl. Contoh No. 123"
}

// Response:
{
    "success": true,
    "message": "Pelanggan berhasil ditambahkan",
    "data": { "id": 1, "name": "John Doe", ... }
}
```

#### READ - Ambil Data Pelanggan
```php
// Method: GET
// Endpoint: /api/customers-api.php

// Get All:
/api/customers-api.php

// Get By ID:
/api/customers-api.php?id=1

// Search:
/api/customers-api.php?search=john
```

#### UPDATE - Edit Pelanggan
```php
// Method: PUT
// Endpoint: /api/customers-api.php

// Request Body:
{
    "id": 1,
    "name": "John Updated",
    "phone": "081234567890"
}
```

#### DELETE - Hapus Pelanggan
```php
// Method: DELETE
// Endpoint: /api/customers-api.php?id=1
```

### 4.2 CRUD Transaksi (Transactions)

**File:** `api/transactions-api.php`

#### CREATE - Buat Transaksi Baru
```php
// Method: POST
{
    "customer_id": 1,
    "service_type": "Cuci Setrika",
    "weight": 3.5,
    "total_price": 24500,
    "notes": "Antarkan besok siang"
}
```

#### READ - Ambil Data Transaksi
```php
// Get All dengan Pagination:
/api/transactions-api.php?page=1&limit=10

// Filter by Status:
/api/transactions-api.php?status=pending

// Filter by Customer Phone:
/api/transactions-api.php?phone=081234567890
```

#### UPDATE - Update Status Transaksi
```php
// Method: PUT
{
    "id": 1,
    "status": "washing"  // atau "done", "picked_up", dll
}
```

### 4.3 Kode Implementasi CRUD

**Contoh: api/customers-api.php**
```php
<?php
require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // READ - Get customers
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $customer]);
        } else {
            $stmt = $db->query("SELECT * FROM customers ORDER BY name");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        }
        break;
        
    case 'POST':
        // CREATE - Add new customer
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['phone'], $data['address'] ?? '']);
        echo json_encode(['success' => true, 'message' => 'Customer created']);
        break;
        
    case 'PUT':
        // UPDATE - Edit customer
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE customers SET name=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$data['name'], $data['phone'], $data['address'], $data['id']]);
        echo json_encode(['success' => true, 'message' => 'Customer updated']);
        break;
        
    case 'DELETE':
        // DELETE - Remove customer
        $id = $_GET['id'];
        $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Customer deleted']);
        break;
}
```

---

## 5. Sistem Autentikasi

### 5.1 Fitur Autentikasi

| Fitur | Deskripsi | File |
|-------|-----------|------|
| **Registration** | Daftar dengan email/password | `pages/auth/register.php`, `api/auth/register.php` |
| **Email Verification** | Verifikasi email via link | `pages/auth/verify-email.php` |
| **Email/Password Login** | Login tradisional | `pages/auth/login.php`, `api/auth/login.php` |
| **Google OAuth 2.0** | Login dengan Google | `api/google-auth.php` |
| **Forgot Password** | Reset password via email | `pages/auth/forgot-password.php` |
| **Reset Password** | Form reset password | `pages/auth/reset-password.php` |
| **Role-based Access** | Akses berdasarkan role | `includes/auth.php` |
| **Session Management** | Kelola sesi login | `includes/auth.php` |

### 5.2 Alur Registrasi

```
+----------------+
| Halaman Daftar |
| (register.php) |
+-------+--------+
        |
        | Submit Form
        v
+----------------+
|api/auth/       |
|register.php    |
+-------+--------+
        |
        | 1. Validasi input
        | 2. Cek email unik
        | 3. Hash password (bcrypt)
        | 4. Generate verification token
        | 5. Insert ke database
        | 6. Kirim email verifikasi
        v
+----------------+
|Email Verifikasi|
| (link + token) |
+-------+--------+
        |
        | User klik link
        v
+----------------+
|verify-email.php|
| Token valid?   |
+-------+--------+
        |
        | Update email_verified_at
        v
+----------------+
| Akun Aktif!    |
| Redirect Login |
+----------------+
```

### 5.3 Alur Login

```
+--------------------------------------------------------------------+
|                         LOGIN FLOW                                  |
+--------------------------------------------------------------------+
|                                                                     |
|  +-------------+          +-------------+                           |
|  | Email/Pass  |          |   Google    |                           |
|  |   Login     |          |   OAuth     |                           |
|  +------+------+          +------+------+                           |
|         |                        |                                  |
|         v                        v                                  |
|  +-------------+          +-------------+                           |
|  |Verify Hash  |          |Verify Token |                           |
|  |(bcrypt)     |          |(Google API) |                           |
|  +------+------+          +------+------+                           |
|         |                        |                                  |
|         +----------+-------------+                                  |
|                    v                                                |
|             +-------------+                                         |
|             |Create/Update|                                         |
|             |  Session    |                                         |
|             +------+------+                                         |
|                    |                                                |
|                    v                                                |
|             +-------------+                                         |
|             | CHECK ROLE  |                                         |
|             +------+------+                                         |
|                    |                                                |
|      +-------------+-------------+                                  |
|      v                           v                                  |
| +-------------+           +-------------+                           |
| |superadmin   |           |   user      |                           |
| |admin/cashier|           | (customer)  |                           |
| +------+------+           +------+------+                           |
|        |                         |                                  |
|        v                         v                                  |
| +-------------+           +-------------+                           |
| |Admin        |           |Customer     |                           |
| |Dashboard    |           |Dashboard    |                           |
| +-------------+           +-------------+                           |
|                                                                     |
+--------------------------------------------------------------------+
```

### 5.4 Role-Based Access Control (RBAC)

```php
// File: includes/auth.php

// Cek apakah user login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('pages/auth/login.php');
    }
}

// Cek role tertentu
function requireRole($roles) {
    requireLogin();
    $userRole = $_SESSION['user_role'];
    if (!in_array($userRole, $roles)) {
        redirect('pages/dashboard.php');
    }
}

// Penggunaan di halaman admin:
requireRole(['superadmin', 'admin', 'cashier']);
```

### 5.5 Keamanan

| Aspek | Implementasi |
|-------|--------------|
| **Password Hashing** | `bcrypt` via `password_hash()` |
| **Session Security** | HTTPOnly cookies, SameSite=Lax |
| **CSRF Protection** | Token-based validation |
| **SQL Injection** | PDO Prepared Statements |
| **XSS Prevention** | `htmlspecialchars()` on output |
| **Session Timeout** | 30 menit inaktivitas |

---

## 6. Frontend & UI/UX

### 6.1 Teknologi Frontend

- **CSS Framework:** Tailwind CSS v3.x
- **Font:** Google Fonts (Outfit)
- **Icons:** Heroicons (SVG inline)
- **JavaScript:** Vanilla JS (no jQuery)

### 6.2 Design System

#### Color Palette
```css
/* Primary (Purple) */
--primary-500: #9333ea;
--primary-600: #7e22ce;

/* Secondary (Green) */
--secondary-500: #22c55e;
--secondary-600: #16a34a;

/* Bubble Colors (Decorative) */
--bubble-pink: #ec4899;
--bubble-purple: #a855f7;
--bubble-blue: #3b82f6;
--bubble-cyan: #06b6d4;
```

#### Component Classes
```css
/* Cards dengan Glassmorphism */
.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Button Primary */
.btn-primary {
    background: linear-gradient(135deg, #9333ea, #7e22ce);
    color: white;
    border-radius: 12px;
}

/* Bubble Decorations */
.bubble {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
}
```

### 6.3 Halaman Utama

| Halaman | Deskripsi | Akses |
|---------|-----------|-------|
| `index.php` | Landing page dengan info layanan & lokasi | Public |
| `pages/auth/login.php` | Login pelanggan | Public |
| `pages/auth/register.php` | Registrasi pelanggan | Public |
| `pages/auth/admin-login.php` | Login admin (terpisah) | Public |
| `pages/dashboard.php` | Dashboard admin dengan statistik | Admin |
| `pages/customers.php` | Manajemen pelanggan (CRUD) | Admin |
| `pages/transactions.php` | Manajemen transaksi (CRUD) | Admin |
| `pages/customer-dashboard.php` | Dashboard pelanggan | User |
| `pages/check-order.php` | Cek status order | User (login) |

### 6.4 Responsive Design

Semua halaman responsif untuk:
- **Desktop:** > 1024px
- **Tablet:** 768px - 1024px
- **Mobile:** < 768px

---

## 7. Panduan Pengujian

### 7.1 Persiapan Testing

```bash
# 1. Start XAMPP (Apache + MySQL)

# 2. Inisialisasi database
cd E:\xampp2\htdocs\laundry-D-four
E:\xampp2\php\php.exe database/init_mysql.php

# 3. Jalankan migrasi auth
E:\xampp2\php\php.exe database/migrate_auth.php

# 4. Buat akun admin (opsional)
E:\xampp2\php\php.exe database/create_admin.php

# 5. Compile Tailwind CSS
npm run build
```

### 7.2 Skenario Pengujian

#### A. Test Registrasi & Login

| # | Langkah | Expected Result |
|---|---------|-----------------|
| 1 | Buka http://localhost/laundry-D-four/ | Landing page muncul |
| 2 | Klik "Daftar" | Form registrasi muncul |
| 3 | Isi form dan submit | Akun berhasil dibuat |
| 4 | Klik "Login" | Form login muncul |
| 5 | Login dengan email/password | Redirect ke Customer Dashboard |
| 6 | Klik "Sign in with Google" | Google popup muncul, login berhasil |

#### B. Test Admin CRUD

| # | Langkah | Expected Result |
|---|---------|-----------------|
| 1 | Login sebagai admin | Redirect ke Admin Dashboard |
| 2 | Statistik dashboard | Menampilkan total pelanggan, transaksi, dll |
| 3 | Klik "Pelanggan" > "Tambah" | Modal form muncul |
| 4 | Isi data dan submit | Pelanggan baru muncul di tabel |
| 5 | Klik Edit pada pelanggan | Form edit muncul dengan data |
| 6 | Update dan simpan | Data berhasil diupdate |
| 7 | Klik Hapus | Konfirmasi, lalu data terhapus |
| 8 | Buat transaksi baru | Transaksi tersimpan dengan status "pending" |
| 9 | Update status transaksi | Status berubah sesuai pilihan |

#### C. Test Customer Flow

| # | Langkah | Expected Result |
|---|---------|-----------------|
| 1 | Login sebagai customer | Redirect ke Customer Dashboard |
| 2 | Lihat riwayat pesanan | Menampilkan list transaksi user |
| 3 | Klik "Cek dengan Nomor HP" | Form pencarian muncul |
| 4 | Masukkan nomor HP | Menampilkan transaksi terkait |

### 7.3 Akun Testing

| Email | Password | Role |
|-------|----------|------|
| rofiqislamy88@gmail.com | (Google OAuth) | superadmin |
| test@example.com | password123 | user |

---

## Kesimpulan

Sistem D'four Smart Laundry telah mengimplementasikan:

1. **REST API** untuk komunikasi Frontend-Backend
2. **CRUD Lengkap** untuk Pelanggan dan Transaksi
3. **Autentikasi Multi-metode** (Email/Password + Google OAuth)
4. **Role-Based Access Control** (superadmin, admin, cashier, user)
5. **UI Modern** dengan Tailwind CSS dan Glassmorphism
6. **Keamanan** dengan bcrypt, prepared statements, dan session management

---

*Dokumen ini dibuat untuk keperluan presentasi dan pengujian oleh dosen penguji.*
