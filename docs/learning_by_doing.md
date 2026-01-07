# 📚 Learning by Doing - D'four Laundry Project

Panduan lengkap untuk memahami struktur project dan teknologi yang digunakan.

**Last Updated: 2026-01-07**

---

## 🎯 Tujuan Dokumen

Dokumen ini dibuat untuk membantu Anda memahami:
1. **Struktur folder dan file** dalam project ini
2. **Fungsi masing-masing komponen**
3. **Kaitan dengan teknologi**: Tailwind CSS, PHP Native, JavaScript, dan MySQL
4. **Fitur-fitur yang sudah diimplementasikan**

---

## 📁 Struktur Project Overview

```
laundry-D-four/
├── 📂 api/                    # API endpoints
│   ├── 📂 auth/               # Authentication APIs
│   │   ├── login.php          # Login API
│   │   ├── register.php       # Registration API
│   │   ├── forgot-password.php
│   │   └── reset-password.php
│   ├── customers-api.php      # Customer CRUD API
│   ├── transactions-api.php   # Transaction CRUD API
│   ├── google-auth.php        # Google OAuth handler
│   └── logout.php             # Logout API
│
├── 📂 assets/                 # Static files
│   ├── 📂 css/
│   │   ├── input.css          # Tailwind source
│   │   └── style.css          # Compiled CSS
│   ├── 📂 js/
│   │   └── main.js            # Custom JavaScript
│   └── 📂 images/
│       └── bubbles/           # Bubble decorations
│
├── 📂 config/                 # Configuration
│   ├── database_mysql.php     # MySQL connection (Singleton)
│   ├── google-oauth.php       # Google OAuth config
│   └── email.php              # SMTP email config
│
├── 📂 database/               # Database scripts
│   ├── init_mysql.php         # Initialize tables
│   ├── migrate_auth.php       # Auth tables migration
│   ├── migrate_customer_user.php # Customer-User integration
│   └── create_admin.php       # Create admin account
│
├── 📂 docs/                   # Documentation
│   ├── to-do.md               # Development TODO
│   ├── registration-flow.md   # Customer registration flow
│   ├── GITTutor.md            # Git tutorial
│   └── learning_by_doing.md   # This file!
│
├── 📂 includes/               # Reusable PHP components
│   ├── auth.php               # Authentication helpers
│   ├── email.php              # Email sending functions
│   ├── functions.php          # Helper functions
│   ├── header.php             # Page header
│   ├── header-admin.php       # Admin header with sidebar
│   └── footer.php             # Page footer
│
├── 📂 pages/                  # Application pages
│   ├── 📂 auth/               # Authentication pages
│   │   ├── login.php          # Customer login
│   │   ├── register.php       # Customer registration
│   │   ├── admin-login.php    # Admin login
│   │   ├── forgot-password.php
│   │   ├── reset-password.php
│   │   └── verify-email.php
│   ├── dashboard.php          # Admin dashboard
│   ├── customer-dashboard.php # Customer dashboard
│   ├── customers.php          # Customer management
│   ├── transactions.php       # Transaction management
│   └── check-order.php        # Public order checking
│
├── 📄 index.php               # Landing page
├── 📄 package.json            # npm configuration
├── 📄 tailwind.config.js      # Tailwind configuration
└── 📄 .gitignore              # Git ignore rules
```

---

## 🔐 Authentication System

### Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Session Management | PHP Sessions |
| Password Hashing | `password_hash()` / `password_verify()` |
| Google OAuth | Google Sign-In JavaScript + JWT verification |
| Email Verification | PHPMailer (SMTP) |

### File-File Terkait

#### `includes/auth.php`
Helper functions untuk authentication:
```php
isLoggedIn()        // Check if user logged in
requireLogin()      // Redirect to login if not authenticated
getUserData()       // Get current user data from session
loginUser($data)    // Create user session
logoutUser()        // Destroy session
hasRole($roles)     // Check user role
getBaseUrl()        // Get application base URL
```

#### `api/auth/login.php`
API endpoint untuk login:
- Menerima: `{ email, password }`
- Validasi password dengan `password_verify()`
- Membuat session jika berhasil
- Return: `{ success, user, redirect }`

#### `api/auth/register.php`
API endpoint untuk registrasi:
- Menerima: `{ name, email, phone, password }`
- Hash password dengan `password_hash()`
- **Auto-link ke customer** berdasarkan phone number
- Return: `{ success, message }`

#### `api/google-auth.php`
Handler untuk Google OAuth:
- Menerima JWT token dari Google Sign-In
- Verifikasi token dengan Google API
- Create/update user di database
- **Check if user has phone** → redirect ke complete-profile jika belum

---

## 👤 Customer-User Integration

### Konsep
Sistem menghubungkan data **Customer** (pelanggan laundry) dengan **User** (akun login) berdasarkan **nomor telepon**.

### Database Schema

```sql
-- Tabel users (akun login)
CREATE TABLE users (
    id INT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    phone VARCHAR(20) UNIQUE,      -- Untuk linking
    password_hash VARCHAR(255),
    google_id VARCHAR(255),
    role ENUM('superadmin','admin','cashier','user'),
    login_method VARCHAR(20),
    is_active BOOLEAN,
    email_verified_at TIMESTAMP
);

-- Tabel customers (data pelanggan)
CREATE TABLE customers (
    id INT PRIMARY KEY,
    user_id INT,                   -- FK ke users (linking)
    name VARCHAR(255),
    phone VARCHAR(20),             -- Untuk matching
    address TEXT,
    registered_at TIMESTAMP,       -- Kapan user ter-link
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Alur Integrasi

```
┌─────────────────────────────────────────────────────────────┐
│  USER REGISTER DENGAN PHONE: 081234567890                   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  INSERT ke tabel USERS dengan phone tersebut                │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  CEK: Apakah phone ada di tabel CUSTOMERS?                  │
│                                                             │
│  ✅ ADA → UPDATE customers SET user_id = [new_user_id]      │
│  ❌ TIDAK → INSERT customer baru dengan user_id             │
└─────────────────────────────────────────────────────────────┘
```

### File-File Terkait

- `api/auth/register.php` - Logic auto-link saat registrasi
- `api/customers-api.php` - Include `is_registered` status
- `pages/customers.php` - Tampilkan badge "Terdaftar/Belum Daftar"
- `database/migrate_customer_user.php` - Migration script

---

## 🎨 Tailwind CSS Setup

### Konfigurasi (`tailwind.config.js`)

```javascript
module.exports = {
  content: [
    "./**/*.php",           // Scan semua file PHP
    "./assets/js/**/*.js"   // Scan file JavaScript
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#faf5ff',
          500: '#9333ea',
          600: '#7e22ce',
          // ...
        },
        secondary: {
          500: '#22c55e',
          // ...
        }
      },
      fontFamily: {
        'outfit': ['Outfit', 'sans-serif']
      }
    }
  }
}
```

### Commands

```bash
# Development (watch mode)
npm run dev

# Production (minified)
npm run build
```

### Custom Components di `input.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .card {
    @apply bg-white rounded-2xl shadow-xl p-6;
  }
  
  .btn-primary {
    @apply bg-gradient-to-r from-primary-500 to-primary-600 
           text-white px-6 py-3 rounded-xl font-semibold;
  }
  
  .form-input {
    @apply w-full px-4 py-3 border-2 border-gray-200 
           rounded-xl focus:border-primary-500;
  }
}
```

---

## 📡 API Structure

### Response Format Standard

```json
// Success
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}

// Error
{
  "success": false,
  "error": "Error message here"
}
```

### Customer API (`api/customers-api.php`)

| Action | Method | Description |
|--------|--------|-------------|
| `get_all` | GET | List semua customer dengan status registrasi |
| `get_by_id` | GET | Get customer by ID |
| `get_by_phone` | GET | Search customer by phone |
| `create` | POST | Create customer (auto-link jika phone ada di users) |
| `update` | POST | Update customer |
| `delete` | POST | Delete customer |

### Transaction API (`api/transactions-api.php`)

| Action | Method | Description |
|--------|--------|-------------|
| `get_all` | GET | List semua transaksi |
| `get_by_phone` | GET | Get transaksi by customer phone |
| `create` | POST | Create transaksi baru |
| `update_status` | POST | Update status transaksi |
| `delete` | POST | Delete transaksi |

---

## 🗄️ Database Tables

### `users`
Akun login pengguna (admin, kasir, customer).

### `customers`
Data pelanggan laundry (bisa ter-link atau tidak ke users).

### `transactions`
Data transaksi laundry.

### `service_types`
Jenis layanan dan harga.

### `password_resets`
Token untuk reset password.

---

## 🔄 Application Flow

### 1. Customer Registration Flow
```
Register Page → API /register → Insert users → Link/Create customer → Redirect to Login
```

### 2. Customer Login Flow
```
Login Page → API /login → Verify password → Create session → Redirect to Customer Dashboard
```

### 3. Google OAuth Flow
```
Click Google Button → Google Popup → JWT Token → API /google-auth → Verify Token → 
Create/Update user → Check phone → Complete Profile or Dashboard
```

### 4. Admin Transaction Flow
```
Admin Login → Dashboard → Create Transaction → Select Customer → Select Service → 
Calculate Price → Save → Update Status → Customer can check via Check Order page
```

---

## 🛠️ Development Workflow

### Start Development

1. **Start XAMPP** (Apache + MySQL)
2. **Start Tailwind watch**:
   ```bash
   cd laundry-D-four
   npm run dev
   ```
3. **Open browser**: `http://localhost/laundry-D-four`

### Database Setup

```bash
# Initialize database
php database/init_mysql.php

# Run migrations
php database/migrate_auth.php
php database/migrate_customer_user.php

# Create admin account
php database/create_admin.php
```

### Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@dfour.com | admin123 |

---

## 📚 Related Documentation

- `docs/to-do.md` - Development TODO list
- `docs/registration-flow.md` - Detail alur registrasi
- `docs/GITTutor.md` - Git & GitHub tutorial

---

*Happy Coding! 🚀*
