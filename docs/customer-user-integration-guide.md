# Customer-User Integration - Panduan Implementasi Manual

## Phase 1: Database Migration

### File Baru: `database/migrate_customer_user.php`

```php
<?php
/**
 * Migration: Customer-User Integration
 * Jalankan: php database/migrate_customer_user.php
 */

require_once __DIR__ . '/../config/database_mysql.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Customer-User Integration Migration ===\n\n";
    
    // 1. Tambah kolom phone ke users
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) UNIQUE NULL AFTER name");
        echo "✅ Added 'phone' column to users table\n";
    } else {
        echo "⏭️  Column 'phone' already exists in users\n";
    }
    
    // 2. Tambah kolom user_id ke customers
    $stmt = $db->query("SHOW COLUMNS FROM customers LIKE 'user_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE customers ADD COLUMN user_id INT NULL AFTER id");
        echo "✅ Added 'user_id' column to customers table\n";
    } else {
        echo "⏭️  Column 'user_id' already exists in customers\n";
    }
    
    // 3. Tambah kolom registered_at ke customers
    $stmt = $db->query("SHOW COLUMNS FROM customers LIKE 'registered_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE customers ADD COLUMN registered_at TIMESTAMP NULL AFTER address");
        echo "✅ Added 'registered_at' column to customers table\n";
    } else {
        echo "⏭️  Column 'registered_at' already exists in customers\n";
    }
    
    // 4. Tambah foreign key (opsional, skip jika error)
    try {
        $db->exec("ALTER TABLE customers ADD CONSTRAINT fk_customer_user 
                   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "✅ Added foreign key constraint\n";
    } catch (Exception $e) {
        echo "⏭️  Foreign key already exists or skipped\n";
    }
    
    echo "\n🎉 Migration completed!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

---

## Phase 2: Update Registration

### File: `pages/auth/register.php`

**Tambahkan input field nomor HP setelah field email:**

```html
<!-- Cari bagian input email, lalu tambahkan di bawahnya: -->

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
    <input type="tel" name="phone" id="phone" required
           class="form-input" 
           placeholder="08xxxxxxxxxx"
           pattern="^08[0-9]{8,12}$">
    <p class="text-xs text-gray-500 mt-1">Format: 08xxxxxxxxxx</p>
</div>
```

**Update JavaScript form submission (tambahkan phone):**

```javascript
// Cari bagian fetch register, update body:
body: JSON.stringify({ 
    name, 
    email, 
    phone: document.getElementById('phone').value.trim(),  // TAMBAH INI
    password 
})
```

---

### File: `api/auth/register.php`

**Tambahkan validasi dan simpan phone. Cari bagian setelah validasi password:**

```php
// Tambahkan setelah validasi password
$phone = isset($input['phone']) ? trim($input['phone']) : '';

// Validasi phone
if (empty($phone)) {
    throw new Exception('Nomor HP wajib diisi');
}
if (!preg_match('/^08[0-9]{8,12}$/', $phone)) {
    throw new Exception('Format nomor HP tidak valid');
}

// Cek phone sudah terdaftar di users
$stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
$stmt->execute([$phone]);
if ($stmt->fetch()) {
    throw new Exception('Nomor HP sudah terdaftar');
}
```

**Update query INSERT users (tambahkan phone):**

```php
// Ubah query INSERT menjadi:
$stmt = $db->prepare("
    INSERT INTO users (email, name, phone, password_hash, role, login_method, verification_token, verification_expires)
    VALUES (?, ?, ?, ?, 'user', 'email', ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
");
$stmt->execute([$email, $name, $phone, $passwordHash, $verificationToken]);
$userId = $db->lastInsertId();
```

**Tambahkan logic untuk link/create customer (setelah INSERT user berhasil):**

```php
// Cek apakah phone sudah ada di customers
$stmt = $db->prepare("SELECT id FROM customers WHERE phone = ?");
$stmt->execute([$phone]);
$existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingCustomer) {
    // Link user ke customer existing
    $stmt = $db->prepare("UPDATE customers SET user_id = ?, registered_at = NOW() WHERE id = ?");
    $stmt->execute([$userId, $existingCustomer['id']]);
} else {
    // Buat customer baru
    $stmt = $db->prepare("INSERT INTO customers (user_id, name, phone, registered_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $name, $phone]);
}
```

---

## Phase 3: Update Customer CRUD

### File: `api/customers-api.php`

File ini menggunakan case berdasarkan `action`, bukan HTTP method. Ada 4 case yang perlu diubah:

---

#### Case 1: `get_all` (Baris 109-115) - **PALING PENTING**

```php
case 'get_all':
    // UBAH DARI:
    // $stmt = $db->query("SELECT * FROM customers ORDER BY name ASC");
    
    // MENJADI:
    $stmt = $db->query("
        SELECT c.*, 
               u.email as user_email,
               CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.id
        ORDER BY c.name ASC
    ");
    
    $response['success'] = true;
    $response['data'] = $stmt->fetchAll();
    break;
```

---

#### Case 2: `get_by_id` (Baris 81-95)

```php
case 'get_by_id':
    $id = $_GET['id'] ?? 0;
    
    // UBAH QUERY MENJADI:
    $stmt = $db->prepare("
        SELECT c.*, 
               u.email as user_email,
               CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        throw new Exception('Pelanggan tidak ditemukan');
    }
    
    $response['success'] = true;
    $response['data'] = $customer;
    break;
```

---

#### Case 3: `get_by_phone` (Baris 97-107)

```php
case 'get_by_phone':
    $phone = $_GET['phone'] ?? '';
    
    // UBAH QUERY MENJADI:
    $stmt = $db->prepare("
        SELECT c.*, 
               u.email as user_email,
               CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.phone LIKE ?
    ");
    $stmt->execute(["%{$phone}%"]);
    $customers = $stmt->fetchAll();
    
    $response['success'] = true;
    $response['data'] = $customers;
    break;
```

---

#### Case 4: `create` (Baris 19-41) - Auto-link user

```php
case 'create':
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    // Validation (sama seperti sebelumnya)
    if (empty($name) || empty($phone)) {
        throw new Exception('Nama dan nomor telepon wajib diisi');
    }
    
    if (!validatePhone($phone)) {
        throw new Exception('Format nomor telepon tidak valid');
    }
    
    // TAMBAHKAN: Cek apakah phone sudah ada di users
    $stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $userId = $user ? $user['id'] : null;
    $registeredAt = $user ? date('Y-m-d H:i:s') : null;
    
    // UBAH INSERT: Tambah user_id dan registered_at
    $stmt = $db->prepare("INSERT INTO customers (user_id, name, phone, address, registered_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $phone, $address, $registeredAt]);
    
    $response['success'] = true;
    $response['message'] = 'Pelanggan berhasil ditambahkan';
    $response['customer_id'] = $db->lastInsertId();
    break;
```

---

### File: `pages/customers.php`

**Tambahkan kolom Status di tabel:**

```html
<!-- Di bagian <thead>, tambahkan kolom: -->
<th>Status</th>

<!-- Di bagian <tbody> template, tambahkan: -->
<td>
    ${customer.is_registered 
        ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terdaftar</span>'
        : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Belum Daftar</span>'
    }
</td>
```

**Tambahkan tombol Undang (jika belum terdaftar):**

```html
<!-- Di bagian aksi, tambah kondisi: -->
${!customer.is_registered 
    ? `<button onclick="inviteCustomer('${customer.phone}')" class="text-blue-600 hover:text-blue-800">
         Undang
       </button>`
    : ''
}
```

**Tambahkan function JavaScript untuk invite:**

```javascript
function inviteCustomer(phone) {
    const registerUrl = `${window.location.origin}/laundry-D-four/pages/auth/register.php?phone=${phone}`;
    
    // Copy ke clipboard
    navigator.clipboard.writeText(registerUrl).then(() => {
        alert('Link registrasi sudah dicopy!\n\nKirim ke customer via WhatsApp.');
    });
    
    // Atau langsung buka WhatsApp
    // const waUrl = `https://wa.me/62${phone.substring(1)}?text=Silakan daftar di: ${encodeURIComponent(registerUrl)}`;
    // window.open(waUrl, '_blank');
}
```

---

## Phase 4: Update Customer Dashboard

### File: `pages/customer-dashboard.php`

**Fetch order berdasarkan phone user yang login:**

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user = getUserData();

// Ambil phone dari user
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT phone FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$userPhone = $userData['phone'] ?? '';

// Ambil transaksi berdasarkan phone
$transactions = [];
if ($userPhone) {
    $stmt = $db->prepare("
        SELECT t.*, c.name as customer_name 
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE c.phone = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$userPhone]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Tampilkan $transactions di halaman -->
```

---

### File: `pages/auth/register.php`

**Auto-fill phone dari URL parameter:**

```php
// Di bagian atas file, setelah require
$prefillPhone = $_GET['phone'] ?? '';
```

```html
<!-- Di input phone, tambahkan value: -->
<input type="tel" name="phone" id="phone" required
       class="form-input" 
       placeholder="08xxxxxxxxxx"
       value="<?= htmlspecialchars($prefillPhone) ?>"
       <?= $prefillPhone ? 'readonly' : '' ?>>
```

---

## Phase 5: Update Google Auth (Opsional)

### File: `api/google-auth.php`

**Handling untuk user Google yang belum punya phone:**

Setelah user login Google, redirect ke halaman untuk melengkapi phone jika belum ada.

```php
// Setelah login berhasil, cek apakah phone kosong
if (empty($user['phone'])) {
    // Redirect ke halaman complete profile
    echo json_encode([
        'success' => true,
        'needs_phone' => true,
        'redirect' => getBaseUrl() . '/pages/auth/complete-profile.php'
    ]);
    exit;
}
```

---

## Phase 5B: Buat Halaman Complete Profile

User yang login via Google dan belum punya nomor HP akan diarahkan ke halaman ini.

### File Baru #1: `pages/auth/complete-profile.php`

```php
<?php
/**
 * Complete Profile Page
 * Untuk user Google yang perlu melengkapi nomor HP
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Harus sudah login
requireLogin();

$user = getUserData();
$pageTitle = "Lengkapi Profil - D'four Laundry";

// Jika sudah punya phone, redirect ke dashboard
if (!empty($user['phone'])) {
    $role = $user['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= baseUrl() ?>/assets/css/style.css" rel="stylesheet">
    
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }
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
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
        }
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; display: none; }
        .alert.show { display: block; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit">
    
    <div class="auth-container">
        <div class="auth-card">
            <!-- Welcome Message -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white text-2xl">👋</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang, <?= htmlspecialchars($user['name']) ?>!</h1>
                <p class="text-gray-600">Lengkapi profil Anda dengan menambahkan nomor HP</p>
            </div>
            
            <!-- Alerts -->
            <div id="alertError" class="alert alert-error"></div>
            <div id="alertSuccess" class="alert alert-success"></div>
            
            <!-- Form -->
            <form id="completeProfileForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" 
                           class="form-input bg-gray-50" readonly>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                    <input type="tel" name="phone" id="phone" required
                           class="form-input" 
                           placeholder="08xxxxxxxxxx"
                           pattern="^08[0-9]{8,12}$">
                    <p class="text-xs text-gray-500 mt-1">Format: 08xxxxxxxxxx</p>
                </div>
                
                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Simpan & Lanjutkan</span>
                    <span id="submitLoading" class="hidden">Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('completeProfileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value.trim();
            const alertError = document.getElementById('alertError');
            const alertSuccess = document.getElementById('alertSuccess');
            const btn = document.getElementById('submitBtn');
            
            // Validate phone format
            if (!/^08[0-9]{8,12}$/.test(phone)) {
                alertError.textContent = 'Format nomor HP tidak valid';
                alertError.classList.add('show');
                return;
            }
            
            btn.disabled = true;
            document.getElementById('submitText').classList.add('hidden');
            document.getElementById('submitLoading').classList.remove('hidden');
            
            try {
                const response = await fetch('<?= baseUrl() ?>/api/auth/update-phone.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alertSuccess.textContent = 'Profil berhasil dilengkapi! Mengalihkan...';
                    alertSuccess.classList.add('show');
                    alertError.classList.remove('show');
                    
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= baseUrl() ?>/pages/customer-dashboard.php';
                    }, 1500);
                } else {
                    alertError.textContent = data.error || 'Gagal menyimpan';
                    alertError.classList.add('show');
                }
            } catch (error) {
                alertError.textContent = 'Terjadi kesalahan. Coba lagi.';
                alertError.classList.add('show');
            } finally {
                btn.disabled = false;
                document.getElementById('submitText').classList.remove('hidden');
                document.getElementById('submitLoading').classList.add('hidden');
            }
        });
    </script>
</body>
</html>
```

---

### File Baru #2: `api/auth/update-phone.php`

```php
<?php
/**
 * Update Phone API
 * Endpoint: POST /api/auth/update-phone.php
 * Body: { "phone": "08xxxxxxxxxx" }
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Harus sudah login
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    
    // Validasi
    if (empty($phone)) {
        throw new Exception('Nomor HP wajib diisi');
    }
    if (!preg_match('/^08[0-9]{8,12}$/', $phone)) {
        throw new Exception('Format nomor HP tidak valid');
    }
    
    $db = Database::getInstance()->getConnection();
    $user = getUserData();
    $userId = $user['id'];
    
    // Cek phone sudah dipakai user lain
    $stmt = $db->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
    $stmt->execute([$phone, $userId]);
    if ($stmt->fetch()) {
        throw new Exception('Nomor HP sudah digunakan akun lain');
    }
    
    // Update phone di users
    $stmt = $db->prepare("UPDATE users SET phone = ? WHERE id = ?");
    $stmt->execute([$phone, $userId]);
    
    // Cek apakah phone sudah ada di customers
    $stmt = $db->prepare("SELECT id FROM customers WHERE phone = ?");
    $stmt->execute([$phone]);
    $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCustomer) {
        // Link user ke customer existing
        $stmt = $db->prepare("UPDATE customers SET user_id = ?, registered_at = NOW() WHERE id = ?");
        $stmt->execute([$userId, $existingCustomer['id']]);
    } else {
        // Buat customer baru
        $stmt = $db->prepare("INSERT INTO customers (user_id, name, phone, registered_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $user['name'], $phone]);
    }
    
    // Update session dengan phone baru
    $_SESSION['user_phone'] = $phone;
    
    // Determine redirect
    $role = $user['role'] ?? 'user';
    $baseUrl = getBaseUrl();
    
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        $redirect = $baseUrl . '/pages/dashboard.php';
    } else {
        $redirect = $baseUrl . '/pages/customer-dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Nomor HP berhasil disimpan',
        'redirect' => $redirect
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
```

---

## Checklist Testing

Setelah implementasi, test skenario berikut:

- [ ] Customer baru register → User + Customer ter-link
- [ ] Admin tambah customer dengan HP yang sudah punya akun → Auto-link
- [ ] Admin tambah customer baru → Status "Belum Daftar"
- [ ] Customer existing register → Link ke data customer lama
- [ ] Customer login → Lihat order history berdasarkan HP
- [ ] Tombol "Undang" muncul untuk customer belum daftar

---

**Selamat mengerjakan!** 💪
