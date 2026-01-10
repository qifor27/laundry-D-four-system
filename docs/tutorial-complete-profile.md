# 📚 Tutorial: Complete Profile untuk Google Users

**Fitur:** Halaman untuk user Google melengkapi nomor HP  
**Level:** Backend - Priority HIGH  
**Estimasi Waktu:** 1-2 jam  
**Last Updated:** 2026-01-07

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Alur Logic (Flowchart)](#2-alur-logic-flowchart)
3. [File yang Akan Dibuat](#3-file-yang-akan-dibuat)
4. [Langkah 1: API Update Phone](#langkah-1-api-update-phone)
5. [Langkah 2: Halaman Complete Profile](#langkah-2-halaman-complete-profile)
6. [Langkah 3: Update Google Auth (Opsional)](#langkah-3-update-google-auth-opsional)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa Fitur Ini Diperlukan?

Ketika user login dengan **Google OAuth**, data yang kita dapat dari Google hanya:
- Google ID
- Email
- Nama
- Profile Picture

**TIDAK ADA** nomor HP!

Padahal di sistem laundry, nomor HP penting untuk:
1. **Menghubungkan user dengan data customer** yang sudah ada
2. **Notifikasi** (WhatsApp/SMS)
3. **Identifikasi** saat mengambil cucian

### 📊 Flow Saat Ini di `api/google-auth.php`

```
User Login Google → Cek phone kosong? → Ya → Redirect ke complete-profile.php
                                       → Tidak → Lanjut ke dashboard
```

Kode yang sudah ada (line 122-130):
```php
if (empty($userData['phone'])){
    // Redirect ke halaman complete profile
    echo json_encode([
        'success' => true,
        'needs_phone' => true,
        'redirect' => getBaseUrl(). '/pages/complete-profile.php'
    ]);
    exit;
}
```

> **Catatan:** Path di kode existing adalah `/pages/complete-profile.php`, tapi kita akan buat di `/pages/auth/complete-profile.php` agar konsisten dengan file auth lainnya. Nanti kita update path-nya.

---

## 2. Alur Logic (Flowchart)

```
┌──────────────────────────────────────────────────────────────────┐
│                    COMPLETE PROFILE FLOW                          │
└──────────────────────────────────────────────────────────────────┘

    ┌─────────────────┐
    │ User masuk ke   │
    │ complete-profile│
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐     Tidak
    │ Sudah login?    │─────────────────┐
    └────────┬────────┘                 │
             │ Ya                       │
             ▼                          ▼
    ┌─────────────────┐      ┌─────────────────┐
    │ Phone sudah ada?│      │ Redirect ke     │
    └────────┬────────┘      │ login.php       │
             │               └─────────────────┘
    ┌────────┴────────┐
    │Ya               │Tidak
    ▼                 ▼
┌─────────────┐  ┌─────────────────┐
│ Redirect ke │  │ Tampilkan form  │
│ dashboard   │  │ input phone     │
└─────────────┘  └────────┬────────┘
                          │
                          ▼
               ┌─────────────────┐
               │ User submit     │ 
               │ nomor HP        │
               └────────┬────────┘
                        │
                        ▼
               ┌─────────────────┐
               │ API Validasi:   │
               │ - Format valid? │
               │ - Belum dipakai?│
               └────────┬────────┘
                        │
            ┌───────────┴───────────┐
            │                       │
       Gagal│                       │Sukses
            ▼                       ▼
    ┌─────────────┐      ┌─────────────────┐
    │ Show error  │      │ Update users    │
    │ message     │      │ SET phone = ?   │
    └─────────────┘      └────────┬────────┘
                                  │
                                  ▼
                       ┌─────────────────┐
                       │ Cek customers   │
                       │ WHERE phone = ? │
                       └────────┬────────┘
                                │
                    ┌───────────┴───────────┐
                    │                       │
               Ada  │                       │ Tidak Ada
                    ▼                       ▼
         ┌─────────────────┐     ┌─────────────────┐
         │ UPDATE customers│     │ INSERT customers│
         │ SET user_id = ? │     │ (name, phone,   │
         └────────┬────────┘     │  user_id)       │
                  │              └────────┬────────┘
                  │                       │
                  └───────────┬───────────┘
                              │
                              ▼
                   ┌─────────────────┐
                   │ Redirect ke     │
                   │ customer-       │
                   │ dashboard.php   │
                   └─────────────────┘
```

---

## 3. File yang Akan Dibuat

| No | File | Deskripsi |
|----|------|-----------|
| 1 | `api/auth/update-phone.php` | API untuk menyimpan nomor HP |
| 2 | `pages/auth/complete-profile.php` | Halaman form input nomor HP |

### Struktur Folder Setelah Ditambahkan:
```
api/
└── auth/
    ├── forgot-password.php
    ├── login.php
    ├── register.php
    ├── reset-password.php
    └── update-phone.php       ← BARU

pages/
└── auth/
    ├── admin-login.php
    ├── forgot-password.php
    ├── login.php
    ├── register.php
    ├── reset-password.php
    ├── verify-email.php
    └── complete-profile.php   ← BARU
```

---

## Langkah 1: API Update Phone

### 📄 File: `api/auth/update-phone.php`

Buat file baru di lokasi: `c:\xampp\htdocs\laundry-D-four-system\api\auth\update-phone.php`

### Penjelasan Struktur Kode:

```
┌─────────────────────────────────────────────────────────┐
│                    STRUKTUR API                         │
├─────────────────────────────────────────────────────────┤
│ 1. Header & Method Check                                │
│ 2. Require Dependencies                                 │
│ 3. Cek User Login                                       │
│ 4. Ambil & Validasi Input                              │
│ 5. Cek Phone Belum Digunakan                           │
│ 6. Update Tabel users                                   │
│ 7. Link/Buat Customer                                   │
│ 8. Return Response                                      │
└─────────────────────────────────────────────────────────┘
```

### Kode Lengkap:

```php
<?php
/**
 * Update Phone API
 * 
 * API untuk menyimpan nomor HP user yang login via Google.
 * Setelah update, akan otomatis link dengan data customer jika ada.
 * 
 * Endpoint: POST /api/auth/update-phone.php
 * Body: { "phone": "08xxxxxxxxxx" }
 * 
 * Response Success:
 * { "success": true, "message": "...", "redirect": "..." }
 * 
 * Response Error:
 * { "success": false, "error": "..." }
 */

// Set response header sebagai JSON
header('Content-Type: application/json');

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false, 
        'error' => 'Method not allowed'
    ]);
    exit;
}

// Load dependencies
// __DIR__ = direktori file ini (api/auth/)
// Naik 2 level untuk ke root project
require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../includes/auth.php';

try {
    // ============================================
    // STEP 1: Cek apakah user sudah login
    // ============================================
    
    if (!isLoggedIn()) {
        // User belum login, kembalikan error 401
        http_response_code(401); // Unauthorized
        throw new Exception('Anda harus login terlebih dahulu');
    }
    
    // Ambil data user dari session
    $userData = getUserData();
    $userId = $userData['id'];
    
    // ============================================
    // STEP 2: Ambil dan validasi input JSON
    // ============================================
    
    // Baca raw body request (JSON)
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Cek apakah input valid
    if (!$input || empty($input['phone'])) {
        throw new Exception('Nomor HP wajib diisi');
    }
    
    // Bersihkan input dari spasi
    $phone = trim($input['phone']);
    
    // ============================================
    // STEP 3: Validasi format nomor HP Indonesia
    // ============================================
    
    // Regex: Harus dimulai dengan 08, diikuti 8-12 digit angka
    // Contoh valid: 081234567890, 08987654321
    // Contoh invalid: 021234567, +6281234567890
    if (!preg_match('/^08[0-9]{8,12}$/', $phone)) {
        throw new Exception('Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx');
    }
    
    // ============================================
    // STEP 4: Koneksi database
    // ============================================
    
    // Singleton pattern - ambil instance database
    $db = Database::getInstance()->getConnection();
    
    // ============================================
    // STEP 5: Cek apakah nomor HP sudah digunakan user lain
    // ============================================
    
    // Prepared statement untuk mencegah SQL Injection
    $stmt = $db->prepare("
        SELECT id FROM users 
        WHERE phone = ? AND id != ?
    ");
    $stmt->execute([$phone, $userId]);
    
    if ($stmt->fetch()) {
        // Nomor sudah digunakan user lain
        throw new Exception('Nomor HP sudah terdaftar oleh akun lain');
    }
    
    // ============================================
    // STEP 6: Update nomor HP di tabel users
    // ============================================
    
    $stmt = $db->prepare("
        UPDATE users 
        SET phone = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$phone, $userId]);
    
    // Update session dengan phone baru
    $_SESSION['user_phone'] = $phone;
    
    // ============================================
    // STEP 7: Link dengan customer atau buat baru
    // ============================================
    
    // Cek apakah ada customer dengan phone ini
    $stmt = $db->prepare("
        SELECT id, user_id FROM customers 
        WHERE phone = ?
    ");
    $stmt->execute([$phone]);
    $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCustomer) {
        // Customer dengan phone ini sudah ada
        // Update untuk link dengan user account
        $stmt = $db->prepare("
            UPDATE customers 
            SET user_id = ?, registered_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$userId, $existingCustomer['id']]);
        
        $message = 'Profil berhasil dilengkapi! Data transaksi Anda sebelumnya sudah terhubung.';
    } else {
        // Belum ada customer dengan phone ini
        // Buat customer baru
        $stmt = $db->prepare("
            INSERT INTO customers (user_id, name, phone, registered_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $userData['name'], $phone]);
        
        $message = 'Profil berhasil dilengkapi!';
    }
    
    // ============================================
    // STEP 8: Return success response
    // ============================================
    
    // Tentukan redirect berdasarkan role
    $baseUrl = getBaseUrl();
    $role = $userData['role'] ?? 'user';
    
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        $redirectUrl = $baseUrl . '/pages/dashboard.php';
    } else {
        $redirectUrl = $baseUrl . '/pages/customer-dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'redirect' => $redirectUrl
    ]);
    
} catch (Exception $e) {
    // Handle semua error
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
```

### 📝 Penjelasan Detail per Bagian:

#### A. Header dan Method Check
```php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
```
- **Fungsi:** Memastikan hanya request POST yang diterima
- **HTTP 405:** Status code untuk "Method Not Allowed"

#### B. Require Dependencies
```php
require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../includes/auth.php';
```
- **`__DIR__`:** Konstanta PHP yang berisi path direktori file saat ini
- **`require_once`:** Load file sekali saja (hindari duplicate)

#### C. Validasi Phone Format
```php
if (!preg_match('/^08[0-9]{8,12}$/', $phone)) {
    throw new Exception('Format nomor HP tidak valid');
}
```
- **`^08`:** Harus dimulai dengan "08"
- **`[0-9]{8,12}`:** Diikuti 8-12 digit angka
- **`$`:** Akhir string
- **Contoh valid:** 081234567890, 08987654321
- **Contoh invalid:** 021234567, +6281234567890

#### D. Prepared Statement
```php
$stmt = $db->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
$stmt->execute([$phone, $userId]);
```
- **Fungsi:** Mencegah SQL Injection
- **`?`:** Placeholder untuk nilai
- **`execute([])`:** Isi placeholder secara aman

---

## Langkah 2: Halaman Complete Profile

### 📄 File: `pages/auth/complete-profile.php`

Buat file baru di lokasi: `c:\xampp\htdocs\laundry-D-four-system\pages\auth\complete-profile.php`

### Struktur Halaman:

```
┌─────────────────────────────────────────────────────────┐
│                    STRUKTUR PAGE                        │
├─────────────────────────────────────────────────────────┤
│ 1. PHP Logic (cek login, redirect)                     │
│ 2. HTML Head (meta, css, fonts)                        │
│ 3. Body                                                 │
│    ├── Bubble Decorations (background)                 │
│    ├── Card Container                                   │
│    │   ├── Logo                                        │
│    │   ├── Welcome Message (nama user)                 │
│    │   ├── Alert Error/Success                         │
│    │   ├── Form (input phone + submit button)          │
│    │   └── Back to Home link                           │
│    └── /Card Container                                 │
│ 4. JavaScript (form handling, AJAX)                    │
└─────────────────────────────────────────────────────────┘
```

### Kode Lengkap:

```php
<?php
/**
 * Complete Profile Page
 * 
 * Halaman untuk user yang login via Google melengkapi nomor HP.
 * Nomor HP diperlukan untuk menghubungkan user dengan data customer.
 * 
 * Flow:
 * 1. Cek apakah user sudah login
 * 2. Cek apakah phone sudah ada (jika ada, redirect ke dashboard)
 * 3. Tampilkan form input phone
 * 4. Submit ke API /api/auth/update-phone.php
 */

// Load dependencies
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

// ============================================
// STEP 1: Cek apakah user sudah login
// ============================================

if (!isLoggedIn()) {
    // Belum login, redirect ke halaman login
    header('Location: ' . baseUrl() . '/pages/auth/login.php');
    exit;
}

// Ambil data user dari session
$userData = getUserData();

// ============================================
// STEP 2: Cek apakah phone sudah ada
// ============================================

// Jika phone sudah ada, redirect ke dashboard sesuai role
if (!empty($_SESSION['user_phone'])) {
    $role = $userData['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
}

// Cek juga dari database (untuk memastikan)
require_once __DIR__ . '/../../config/database_mysql.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT phone FROM users WHERE id = ?");
$stmt->execute([$userData['id']]);
$userFromDb = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($userFromDb['phone'])) {
    // Update session dan redirect
    $_SESSION['user_phone'] = $userFromDb['phone'];
    $role = $userData['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
}

// Set page title
$pageTitle = "Lengkapi Profil - D'four Laundry";
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="<?= baseUrl() ?>/assets/css/style.css" rel="stylesheet">

    <style>
        /* Container utama - full screen centered */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card dengan glassmorphism effect */
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        /* Style input form */
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }

        /* Button primary dengan gradient */
        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert styles */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        /* Avatar user */
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .info-box p {
            color: #0369a1;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit">

    <!-- Bubble Decorations - Element dekoratif background -->
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
        <div class="bubble bubble-blue w-72 h-72 bottom-20 left-1/4"></div>
        <div class="bubble bubble-cyan w-64 h-64 bottom-10 right-1/3"></div>
    </div>

    <div class="auth-container relative z-10">
        <div class="auth-card">
            <!-- Logo -->
            <div class="text-center mb-6">
                <a href="<?= baseUrl() ?>" class="inline-flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
            </div>

            <!-- User Avatar & Welcome -->
            <div class="text-center mb-6">
                <?php if (!empty($userData['picture'])): ?>
                    <img src="<?= htmlspecialchars($userData['picture']) ?>" 
                         alt="Profile" 
                         class="user-avatar mx-auto mb-4">
                <?php else: ?>
                    <!-- Default avatar jika tidak ada picture -->
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-2xl">
                            <?= strtoupper(substr($userData['name'] ?? 'U', 0, 1)) ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <h1 class="text-xl font-bold text-gray-900 mb-1">
                    Halo, <?= htmlspecialchars($userData['name'] ?? 'User') ?>! 👋
                </h1>
                <p class="text-gray-600">Satu langkah lagi untuk melengkapi profil Anda</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <strong>📱 Mengapa perlu nomor HP?</strong><br>
                    Nomor HP digunakan untuk menghubungkan akun Anda dengan 
                    data transaksi laundry dan notifikasi status pesanan.
                </p>
            </div>

            <!-- Alerts -->
            <div id="alertError" class="alert alert-error">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="alertErrorText"></span>
            </div>

            <div id="alertSuccess" class="alert alert-success">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="alertSuccessText"></span>
            </div>

            <!-- Form -->
            <form id="completeProfileForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           required
                           class="form-input"
                           placeholder="Contoh: 081234567890"
                           pattern="^08[0-9]{8,12}$"
                           maxlength="14">
                    <p class="text-xs text-gray-500 mt-2">
                        Format: 08xxxxxxxxxx (10-14 digit)
                    </p>
                </div>

                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Simpan & Lanjutkan</span>
                    <span id="submitLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" 
                                    stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" 
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </form>

            <!-- Logout Link -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-500 text-sm mb-2">Bukan <?= htmlspecialchars($userData['name'] ?? 'Anda') ?>?</p>
                <a href="<?= baseUrl() ?>/api/logout.php" 
                   class="text-red-500 hover:text-red-600 text-sm font-medium">
                    Logout dari akun ini
                </a>
            </div>
        </div>
    </div>

    <script>
        // ============================================
        // JAVASCRIPT - Form Handling
        // ============================================
        
        const API_URL = '<?= baseUrl() ?>/api/auth/update-phone.php';

        /**
         * Tampilkan pesan error
         * @param {string} message - Pesan error
         */
        function showError(message) {
            const el = document.getElementById('alertError');
            document.getElementById('alertErrorText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');
            
            // Scroll ke alert
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        /**
         * Tampilkan pesan sukses
         * @param {string} message - Pesan sukses
         */
        function showSuccess(message) {
            const el = document.getElementById('alertSuccess');
            document.getElementById('alertSuccessText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertError').classList.remove('show');
        }

        /**
         * Sembunyikan semua alert
         */
        function hideAlerts() {
            document.getElementById('alertError').classList.remove('show');
            document.getElementById('alertSuccess').classList.remove('show');
        }

        /**
         * Set button ke state loading
         * @param {boolean} isLoading - True jika loading
         */
        function setLoading(isLoading) {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('submitText');
            const btnLoading = document.getElementById('submitLoading');
            
            btn.disabled = isLoading;
            
            if (isLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            } else {
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        }

        /**
         * Validasi format nomor HP
         * @param {string} phone - Nomor HP
         * @returns {boolean} True jika valid
         */
        function validatePhone(phone) {
            // Regex: 08 diikuti 8-12 digit angka
            const regex = /^08[0-9]{8,12}$/;
            return regex.test(phone);
        }

        // ============================================
        // Event Listener - Form Submit
        // ============================================
        
        document.getElementById('completeProfileForm').addEventListener('submit', async (e) => {
            // Cegah form submit default (reload page)
            e.preventDefault();
            
            // Sembunyikan alert sebelumnya
            hideAlerts();
            
            // Ambil nilai input
            const phone = document.getElementById('phone').value.trim();
            
            // Validasi client-side
            if (!phone) {
                showError('Nomor HP wajib diisi');
                return;
            }
            
            if (!validatePhone(phone)) {
                showError('Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx');
                return;
            }
            
            // Set loading state
            setLoading(true);
            
            try {
                // Kirim request ke API
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ phone: phone })
                });
                
                // Parse response JSON
                const data = await response.json();
                
                if (data.success) {
                    // Sukses - tampilkan pesan dan redirect
                    showSuccess(data.message || 'Profil berhasil dilengkapi!');
                    
                    // Redirect setelah 1.5 detik
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= baseUrl() ?>/pages/customer-dashboard.php';
                    }, 1500);
                } else {
                    // Error dari API
                    showError(data.error || 'Terjadi kesalahan');
                    setLoading(false);
                }
            } catch (error) {
                // Network error atau error lainnya
                console.error('Error:', error);
                showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
                setLoading(false);
            }
        });

        // ============================================
        // Auto-format input phone
        // ============================================
        
        document.getElementById('phone').addEventListener('input', function(e) {
            // Hapus karakter non-angka
            let value = this.value.replace(/\D/g, '');
            
            // Pastikan dimulai dengan 08
            if (value.length >= 2 && !value.startsWith('08')) {
                // Jika user mulai dengan 8, tambahkan 0 di depan
                if (value.startsWith('8')) {
                    value = '0' + value;
                }
            }
            
            // Batasi maksimal 14 karakter
            if (value.length > 14) {
                value = value.substring(0, 14);
            }
            
            this.value = value;
        });
    </script>
</body>

</html>
```

### 📝 Penjelasan Komponen Utama:

#### A. PHP Logic di Awal File
```php
// Cek login
if (!isLoggedIn()) {
    header('Location: ' . baseUrl() . '/pages/auth/login.php');
    exit;
}

// Cek phone sudah ada
if (!empty($_SESSION['user_phone'])) {
    header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    exit;
}
```
**Fungsi:** Middleware untuk memastikan:
1. User sudah login
2. User belum punya phone (jika sudah ada, redirect)

#### B. Glassmorphism Card
```css
.auth-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
}
```
**Fungsi:** Efek "kaca buram" yang modern

#### C. Form Validation dengan JavaScript
```javascript
if (!validatePhone(phone)) {
    showError('Format nomor HP tidak valid');
    return;
}
```
**Fungsi:** Validasi di client-side sebelum kirim ke server

#### D. Fetch API untuk AJAX
```javascript
const response = await fetch(API_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ phone: phone })
});
const data = await response.json();
```
**Fungsi:** Kirim request ke API tanpa reload halaman

---

## Langkah 3: Update Google Auth (Opsional)

File `api/google-auth.php` sudah memiliki logic untuk redirect ke complete-profile, tapi path-nya perlu diupdate:

### Perubahan di `api/google-auth.php`:

**Baris 127 (sebelum):**
```php
'redirect' => getBaseUrl(). '/pages/complete-profile.php'
```

**Ubah ke:**
```php
'redirect' => getBaseUrl(). '/pages/auth/complete-profile.php'
```

---

## Testing

### A. Test Manual

1. **Buka halaman login dengan Google**
   ```
   http://localhost/laundry-D-four-system/pages/auth/login.php
   ```

2. **Login dengan akun Google yang belum punya phone**
   - Seharusnya redirect ke `/pages/auth/complete-profile.php`

3. **Isi form phone**
   - Masukkan nomor HP valid: `081234567890`
   - Klik "Simpan & Lanjutkan"

4. **Cek hasil:**
   - Harus redirect ke customer-dashboard
   - Cek database: tabel `users` harus terisi phone
   - Cek database: tabel `customers` harus ter-link dengan user_id

### B. Test dengan cURL (Terminal/CMD)

```bash
# Test API update-phone (ganti SESSION_ID dengan session Anda)
curl -X POST http://localhost/laundry-D-four-system/api/auth/update-phone.php \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=SESSION_ID" \
  -d '{"phone":"081234567890"}'
```

### C. Test Cases

| No | Test Case | Input | Expected Result |
|----|-----------|-------|-----------------|
| 1 | Phone valid | 081234567890 | Success, redirect |
| 2 | Phone kosong | (empty) | Error: "wajib diisi" |
| 3 | Format salah | 021234567 | Error: "format tidak valid" |
| 4 | Phone sudah ada | (phone user lain) | Error: "sudah terdaftar" |
| 5 | Belum login | - | Redirect ke login |

---

## Troubleshooting

### ❌ Error: "Anda harus login terlebih dahulu"
**Solusi:**
- Pastikan sudah login via Google terlebih dahulu
- Cek apakah session sudah aktif

### ❌ Error: "Format nomor HP tidak valid"
**Solusi:**
- Gunakan format 08xxxxxxxxxx
- Pastikan hanya angka (tanpa spasi, strip, atau +62)

### ❌ Redirect loop
**Solusi:**
- Cek apakah phone di database sudah terisi
- Clear session: hapus cookie PHPSESSID

### ❌ AJAX tidak jalan
**Solusi:**
- Buka DevTools (F12) → Console
- Cek apakah ada error JavaScript
- Cek Network tab untuk response API

---

## 📚 Referensi File Terkait

| File | Deskripsi |
|------|-----------|
| `includes/auth.php` | Helper functions authentication |
| `includes/functions.php` | Helper functions umum |
| `config/database_mysql.php` | Koneksi database |
| `api/google-auth.php` | API Google OAuth |
| `api/auth/register.php` | Referensi logic register |
| `pages/auth/register.php` | Referensi UI form |

---

## ✅ Checklist Selesai

- [ ] Buat file `api/auth/update-phone.php`
- [ ] Buat file `pages/auth/complete-profile.php`
- [ ] Update path di `api/google-auth.php` (line 127)
- [ ] Test login Google → redirect ke complete-profile
- [ ] Test submit phone → redirect ke dashboard
- [ ] Cek database: tabel users ter-update
- [ ] Cek database: tabel customers ter-link

---

**Selamat Coding! 🎉**

Jika ada pertanyaan, silakan tanyakan. Ingat, mengetik sendiri kodenya akan membuat Anda lebih paham dibanding copy-paste!
