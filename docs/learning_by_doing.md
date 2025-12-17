# 📚 Learning by Doing - D'four Laundry Management System

## 🎯 Pendahuluan

Dokumen ini dibuat untuk membantu Anda memahami projekt **D'four Laundry Management System** secara mendalam. Di sini Anda akan mempelajari materi-materi yang diperlukan sebelum melanjutkan pengembangan projek ini, serta penjelasan detail tentang fungsi setiap folder dan file dalam projek.

---

## 📖 BAGIAN 1: Materi yang Perlu Dipelajari

Sebelum melanjutkan pengembangan projek ini, ada beberapa konsep dan teknologi yang perlu Anda kuasai terlebih dahulu.

### 1.1 PHP Native (Core PHP)

#### A. Dasar-dasar PHP
- **Variabel dan Tipe Data**: String, Integer, Float, Boolean, Array, Object
- **Control Structure**: if-else, switch-case, for, foreach, while
- **Functions**: Membuat dan menggunakan function, parameter, return value
- **Include & Require**: Perbedaan include, require, include_once, require_once

#### B. PHP OOP (Object Oriented Programming)
- **Class dan Object**: Cara membuat class, instantiasi object
- **Properties dan Methods**: Mendefinisikan dan mengakses properties/methods
- **Visibility**: public, private, protected
- **Inheritance**: Konsep pewarisan class

#### C. Database dengan PDO (PHP Data Objects)
- **Koneksi Database**: Membuat koneksi menggunakan PDO
- **Prepared Statements**: Mencegah SQL Injection dengan prepared statements
- **CRUD Operations**: Create, Read, Update, Delete data
- **Error Handling**: try-catch, PDO error modes
- **Transaction**: beginTransaction(), commit(), rollback()

#### D. Session Management
- **Session Basics**: session_start(), $_SESSION
- **Session Security**: Session hijacking prevention
- **Flash Messages**: Temporary messages menggunakan session

#### E. Security Best Practices
- **Input Validation**: Validasi data dari user
- **Input Sanitization**: Membersihkan data dengan filter_var(), htmlspecialchars()
- **SQL Injection Prevention**: Menggunakan prepared statements
- **XSS Prevention**: Cross-Site Scripting protection
- **CSRF Protection**: Cross-Site Request Forgery (untuk development lanjutan)

---

### 1.2 SQLite Database

#### A. Pengenalan SQLite
- **Apa itu SQLite?**: Database relational yang serverless dan self-contained
- **Kelebihan SQLite**: 
  - Tidak perlu instalasi server database terpisah
  - File-based, portable, cocok untuk aplikasi kecil-menengah
  - Support full SQL
  - ACID compliant (Atomicity, Consistency, Isolation, Durability)

#### B. SQL Fundamentals
- **DDL (Data Definition Language)**: CREATE TABLE, ALTER TABLE, DROP TABLE
- **DML (Data Manipulation Language)**: INSERT, UPDATE, DELETE
- **DQL (Data Query Language)**: SELECT, WHERE, JOIN, ORDER BY, LIMIT
- **Indexes**: Membuat index untuk optimasi query
- **Foreign Keys**: Relasi antar tabel
- **Constraints**: PRIMARY KEY, UNIQUE, NOT NULL, DEFAULT

#### C. SQLite dengan PHP
- **PDO Driver**: Menggunakan PDO dengan SQLite (`sqlite:`)
- **File Database**: Lokasi dan permission file .db
- **Backup & Export**: Cara backup database SQLite

---

### 1.3 Tailwind CSS

#### A. Utility-First CSS
- **Konsep Utility-First**: Berbeda dengan CSS tradisional (Bootstrap)
- **Inline Styling dengan Classes**: Styling langsung di HTML
- **Responsive Design**: Breakpoint (sm:, md:, lg:, xl:, 2xl:)
- **State Variants**: hover:, focus:, active:, disabled:

#### B. Tailwind Core Concepts
- **Typography**: text-sm, text-lg, font-bold, text-center
- **Colors**: bg-blue-500, text-white, border-gray-300
- **Spacing**: p-4 (padding), m-2 (margin), space-x-4
- **Layout**: flex, grid, container, mx-auto
- **Sizing**: w-full, h-screen, max-w-md
- **Borders & Shadows**: rounded-lg, shadow-md, border-2

#### C. Tailwind Configuration
- **tailwind.config.js**: Customisasi theme, colors, extend
- **Input CSS**: @tailwind directives (base, components, utilities)
- **Build Process**: Compile dan watch mode dengan npm
- **Custom Components**: Membuat reusable components dengan @layer

#### D. Tailwind Best Practices
- **Component Extraction**: Kapan harus extract ke @layer components
- **Consistency**: Menggunakan spacing scale yang konsisten
- **Performance**: Purge unused CSS di production
- **Responsive-First**: Mobile-first approach

---

### 1.4 JavaScript (Vanilla JS)

#### A. Modern JavaScript
- **ES6+ Features**: const, let, arrow functions, template literals
- **DOM Manipulation**: querySelector, addEventListener, classList
- **Events**: click, submit, change, keyup events
- **Async JavaScript**: Promises, async/await

#### B. AJAX dengan Fetch API
- **Fetch Basics**: GET, POST, PUT, DELETE requests
- **JSON**: Parsing JSON, JSON.stringify()
- **Error Handling**: .catch(), try-catch
- **Response Handling**: .json(), .text()

#### C. JavaScript untuk UI/UX
- **Modal Management**: Show/hide modals
- **Form Validation**: Client-side validation
- **Notifications**: Success/error messages
- **Data Formatting**: Currency, date, phone number formatting

---

### 1.5 MVC Architecture Pattern

#### A. Konsep MVC
- **Model**: Data layer (database operations)
- **View**: Presentation layer (HTML/CSS)
- **Controller**: Business logic layer (processing)

#### B. MVC dalam PHP Native
- **Separation of Concerns**: Memisahkan logic, data, dan presentation
- **Routing**: Bagaimana request di-handle
- **File Organization**: Struktur folder yang terorganisir

---

## 📁 BAGIAN 2: Penjelasan Folder dan File Projek

Mari kita bahas setiap folder dan file dalam projek ini secara detail.

---

### 2.1 Root Directory Files

#### 📄 `index.php` (Entry Point)
**Fungsi**: Halaman utama yang menjadi pintu masuk aplikasi.

**Konsep PHP Native**:
- Sebagai **Front Controller** pattern
- Menerima semua request dan routing ke halaman yang sesuai
- Menggunakan `$_GET` parameter untuk routing sederhana

**Isi file**:
```php
<?php require_once 'includes/header.php'; ?>
<!-- Dashboard content -->
<?php require_once 'includes/footer.php'; ?>
```

**Penjelasan**:
- `require_once`: Memastikan file hanya di-include sekali
- Header/Footer pattern untuk code reusability
- Routing default ke dashboard

---

#### 📄 `package.json`
**Fungsi**: Configuration file untuk Node.js dependencies dan npm scripts.

**Isi penting**:
```json
{
  "scripts": {
    "dev": "tailwindcss -i ./assets/css/input.css -o ./assets/css/style.css --watch",
    "build": "tailwindcss -i ./assets/css/input.css -o ./assets/css/style.css --minify"
  },
  "devDependencies": {
    "tailwindcss": "^3.4.0"
  }
}
```

**Kaitannya dengan Tailwind CSS**:
- **dev script**: Compile Tailwind CSS dalam mode watch (auto-reload saat ada perubahan)
- **build script**: Compile dan minify untuk production
- **Input**: `assets/css/input.css` (source dengan @tailwind directives)
- **Output**: `assets/css/style.css` (compiled CSS yang digunakan di HTML)

---

#### 📄 `package-lock.json`
**Fungsi**: Lock file yang menyimpan exact version dari dependencies.

**Kaitannya dengan Tailwind CSS**:
- Memastikan semua developer menggunakan versi Tailwind yang sama
- Auto-generated saat `npm install`
- Jangan di-edit manual

---

#### 📄 `tailwind.config.js`
**Fungsi**: Configuration file untuk customize Tailwind CSS.

**Struktur dasar**:
```javascript
module.exports = {
  content: [
    "./pages/**/*.php",
    "./includes/**/*.php",
    "./index.php"
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#3b82f6',
        'secondary': '#64748b'
      }
    }
  },
  plugins: []
}
```

**Penjelasan**:
- **content**: File-file yang menggunakan Tailwind classes (untuk purging)
- **theme.extend**: Custom colors, spacing, fonts, dll
- **plugins**: Plugin tambahan seperti forms, typography

**Kaitannya dengan Tailwind CSS**:
- Menentukan file mana saja yang akan di-scan untuk classes
- Customize default theme Tailwind
- Optimize production build dengan tree-shaking

---

#### 📄 `start-server.bat`
**Fungsi**: Batch script untuk Windows untuk menjalankan server dengan mudah.

**Isi typical**:
```batch
@echo off
echo Starting D'four Laundry Server...
php -S localhost:8000
```

**Penjelasan**:
- Double-click untuk start PHP built-in server
- Alternative dari mengetik command manual
- Hanya untuk development, bukan production

---

#### 📄 `.gitignore`
**Fungsi**: Menentukan file/folder yang tidak perlu di-commit ke Git.

**Isi typical**:
```
node_modules/
database/laundry.db
assets/css/style.css
```

**Penjelasan**:
- **node_modules/**: Dependencies berat, install via npm
- **laundry.db**: Database development, setiap dev punya sendiri
- **style.css**: Generated file, compile dari source

---

#### 📄 `README.md`
**Fungsi**: Dokumentasi projek untuk developer.

**Berisi**:
- Deskripsi projek
- Cara instalasi
- Struktur folder
- Development workflow
- Technologies used

---

#### 📄 `QUICKSTART.md`
**Fungsi**: Panduan quick start untuk developer baru.

**Berisi**:
- Step-by-step setup
- Common commands
- Troubleshooting

---

### 2.2 Folder `/config`

#### 📄 `config/database.php`
**Fungsi**: Configuration dan koneksi database menggunakan PDO.

**Konsep PHP Native**:
- **Singleton Pattern**: Satu instance koneksi untuk seluruh aplikasi
- **PDO (PHP Data Objects)**: Database abstraction layer

**Struktur typical**:
```php
<?php
class Database {
    private static $pdo = null;
    
    public static function connect() {
        if (self::$pdo === null) {
            try {
                $dbPath = __DIR__ . '/../database/laundry.db';
                self::$pdo = new PDO('sqlite:' . $dbPath);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
```

**Penjelasan detail**:
- **static $pdo**: Variable static untuk menyimpan single instance
- **PDO connection string**: `sqlite:path/to/database.db`
- **ATTR_ERRMODE**: Set ke EXCEPTION untuk error handling yang baik
- **ATTR_DEFAULT_FETCH_MODE**: Set ke ASSOC supaya hasil query jadi associative array
- **__DIR__**: Magic constant yang berisi directory path dari file ini

**Kaitannya dengan SQLite**:
- SQLite tidak butuh username/password
- Connection string hanya butuh path ke file .db
- File .db akan auto-created jika belum ada (namun table tidak)

**Cara penggunaan**:
```php
require_once 'config/database.php';
$pdo = Database::connect();
$stmt = $pdo->query("SELECT * FROM customers");
```

---

### 2.3 Folder `/database`

#### 📄 `database/init.php`
**Fungsi**: Script untuk initialize database schema (create tables, indexes, sample data).

**Konsep PHP Native**:
- **DDL (Data Definition Language)**: CREATE TABLE statements
- **PDO exec()**: Execute SQL tanpa return data

**Struktur typical**:
```php
<?php
require_once '../config/database.php';

$pdo = Database::connect();

// Create tables
$pdo->exec("
    CREATE TABLE IF NOT EXISTS customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT UNIQUE NOT NULL,
        address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_id INTEGER NOT NULL,
        service_type TEXT NOT NULL,
        weight REAL,
        quantity INTEGER,
        price INTEGER NOT NULL,
        status TEXT DEFAULT 'pending',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    )
");

// Create indexes
$pdo->exec("CREATE INDEX IF NOT EXISTS idx_customer_phone ON customers(phone)");
$pdo->exec("CREATE INDEX IF NOT EXISTS idx_transaction_status ON transactions(status)");

echo "✅ Database initialized successfully!\n";
```

**Penjelasan detail**:
- **IF NOT EXISTS**: Mencegah error jika table sudah ada
- **AUTOINCREMENT**: Auto-increment primary key
- **FOREIGN KEY**: Relasi dengan ON DELETE CASCADE (hapus child saat parent dihapus)
- **CREATE INDEX**: Optimasi query performance
- **DEFAULT CURRENT_TIMESTAMP**: Auto-fill waktu pembuatan

**Kaitannya dengan SQLite**:
- SQLite support AUTOINCREMENT (berbeda dengan MySQL yang AUTO_INCREMENT)
- FOREIGN KEY di SQLite perlu di-enable dengan `PRAGMA foreign_keys = ON`
- Tipe data: INTEGER, REAL (float), TEXT, BLOB, NULL

**Cara menjalankan**:
```bash
php database/init.php
```

---

#### 📄 `database/laundry.db`
**Fungsi**: File database SQLite yang menyimpan semua data.

**Karakteristik**:
- Binary file, bukan text
- Portable (bisa dicopy ke computer lain)
- Size akan bertambah seiring data bertambah

**Kaitannya dengan SQLite**:
- Single file database
- Bisa dibuka dengan SQLite browser/viewer tools
- Lock mechanism untuk concurrent access

**Permission**:
- Folder dan file harus writable oleh PHP
- Biasanya permission 755 untuk folder, 644 untuk file

---

### 2.4 Folder `/includes`

Folder ini berisi **reusable components** yang di-include di berbagai halaman.

---

#### 📄 `includes/header.php`
**Fungsi**: Header component yang berisi HTML head, navigation, dan opening tags.

**Konsep PHP Native**:
- **Template Partials**: Memecah template jadi bagian-bagian
- **DRY Principle**: Don't Repeat Yourself

**Struktur typical**:
```php
<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D'four Laundry Management System</title>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-2xl font-bold">D'four Laundry</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="<?= baseUrl('index.php') ?>" 
                           class="<?= $current_page === 'index' ? 'bg-blue-700' : '' ?> px-3 py-2 rounded hover:bg-blue-700">
                            Dashboard
                        </a>
                        <a href="<?= baseUrl('pages/customers.php') ?>" 
                           class="<?= $current_page === 'customers' ? 'bg-blue-700' : '' ?> px-3 py-2 rounded hover:bg-blue-700">
                            Pelanggan
                        </a>
                        <a href="<?= baseUrl('pages/transactions.php') ?>" 
                           class="<?= $current_page === 'transactions' ? 'bg-blue-700' : '' ?> px-3 py-2 rounded hover:bg-blue-700">
                            Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto px-4 py-8">
```

**Penjelasan detail**:
- **session_start()**: Harus dipanggil di awal sebelum output HTML
- **__DIR__**: Current directory constant
- **$current_page**: Untuk active menu highlighting
- **basename()**: Get filename dari path
- **Short echo tag**: `<?= ?>` sama dengan `<?php echo ?>`

**Kaitannya dengan Tailwind CSS**:
- **Utility classes**: bg-gradient-to-r, from-blue-600, to-blue-800
- **Responsive classes**: hidden md:flex (hide di mobile, show di medium+)
- **Container**: container mx-auto untuk center content
- **Spacing utilities**: px-4, py-8, space-x-4
- **State variants**: hover:bg-blue-700

**Tips Tailwind CSS**:
- `container`: Max width based on breakpoint
- `mx-auto`: Center horizontal dengan margin auto
- `px-4`: Padding horizontal 1rem (16px)
- `space-x-4`: Gap antara children elemen

---

#### 📄 `includes/footer.php`
**Fungsi**: Footer component dengan closing tags dan JavaScript.

**Struktur typical**:
```php
    </main>
    
    <footer class="bg-gray-800 text-white text-center py-6 mt-12">
        <div class="container mx-auto">
            <p>&copy; <?= date('Y') ?> D'four Laundry. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="<?= baseUrl('assets/js/main.js') ?>"></script>
</body>
</html>
```

**Penjelasan detail**:
- Menutup tag `<main>` yang dibuka di header.php
- **date('Y')**: Dynamic year untuk copyright
- Memuat JavaScript di bawah (best practice untuk page load speed)

**Kaitannya dengan Tailwind CSS**:
- **mt-12**: Margin top 3rem (48px)
- Footer styling dengan bg, text, padding utilities

---

#### 📄 `includes/functions.php`
**Fungsi**: Helper functions yang digunakan di seluruh aplikasi.

**Konsep PHP Native**:
- **Helper Functions**: Reusable utility functions
- **DRY Principle**: Centralized common operations

**Fungsi-fungsi penting**:

```php
<?php

/**
 * Format angka ke format Rupiah
 * @param int $number
 * @return string
 */
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd M Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Sanitize input untuk mencegah XSS
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Get status badge HTML dengan Tailwind CSS
 * @param string $status
 * @return string
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-yellow-100 text-yellow-800">Pending</span>',
        'process' => '<span class="badge bg-blue-100 text-blue-800">Proses</span>',
        'ready' => '<span class="badge bg-green-100 text-green-800">Selesai</span>',
        'delivered' => '<span class="badge bg-gray-100 text-gray-800">Terambil</span>'
    ];
    return $badges[$status] ?? $status;
}

/**
 * Set flash message di session
 * @param string $type (success, error, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get dan clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Generate base URL
 * @param string $path
 * @return string
 */
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = dirname($_SERVER['SCRIPT_NAME']);
    $base = $base === '/' ? '' : $base;
    return $protocol . '://' . $host . $base . '/' . $path;
}

/**
 * Send JSON response untuk API
 * @param array $data
 * @param int $status_code
 */
function jsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
```

**Penjelasan detail setiap fungsi**:

1. **formatRupiah()**:
   - `number_format()`: Format number dengan thousand separator
   - Parameter: (number, decimals, dec_point, thousands_sep)

2. **formatDate()**:
   - `strtotime()`: Convert string date ke Unix timestamp
   - `date()`: Format timestamp ke string

3. **sanitize()**:
   - `trim()`: Remove whitespace di awal/akhir
   - `strip_tags()`: Remove HTML/PHP tags
   - `htmlspecialchars()`: Convert special chars ke HTML entities (XSS prevention)
   - `ENT_QUOTES`: Encode both single and double quotes

4. **getStatusBadge()**:
   - Return HTML dengan Tailwind classes
   - `??` operator: Null coalescing (PHP 7+)

5. **setFlashMessage() & getFlashMessage()**:
   - Session-based temporary messages
   - Auto-clear setelah di-read (one-time display)

6. **baseUrl()**:
   - Dynamic base URL generation
   - Handle subdirectory installation
   - Support HTTP dan HTTPS

7. **jsonResponse()**:
   - Set HTTP status code
   - Set Content-Type header
   - Output JSON dan terminate script

**Kaitannya dengan PHP Native**:
- **Type Hinting**: Modern PHP practice (PHP 7+)
- **Doc Blocks**: PHPDoc untuk documentation
- **Session**: $_SESSION superglobal

---

### 2.5 Folder `/pages`

Folder ini berisi halaman-halaman utama aplikasi.

---

#### 📄 `pages/dashboard.php`
**Fungsi**: Halaman dashboard dengan statistik dan overview.

**Konsep PHP Native**:
- **Database Queries**: Fetch data dari SQLite
- **Aggregation**: COUNT, SUM queries

**Struktur typical**:
```php
<?php
require_once '../includes/header.php';

$pdo = Database::connect();


$stmt = $pdo->query("SELECT COUNT(*) as total FROM customers");
$total_customers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status != 'delivered'");
$active_orders = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(price) as total FROM transactions WHERE DATE(created_at) = DATE('now')");
$today_revenue = $stmt->fetch()['total'] ?? 0;


$stmt = $pdo->query("
    SELECT t.*, c.name as customer_name 
    FROM transactions t
    JOIN customers c ON t.customer_id = c.id
    ORDER BY t.created_at DESC
    LIMIT 5
");
$recent_transactions = $stmt->fetchAll();
?>

<!-- HTML dengan Tailwind CSS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card: Total Customers -->
    <div class="card-gradient p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Pelanggan</h3>
        <p class="text-4xl font-bold text-blue-600"><?= $total_customers ?></p>
    </div>
    
    <!-- Card: Active Orders -->
    <div class="card-gradient p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Pesanan Aktif</h3>
        <p class="text-4xl font-bold text-yellow-600"><?= $active_orders ?></p>
    </div>
    
    <!-- Card: Today Revenue -->
    <div class="card-gradient p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Pendapatan Hari Ini</h3>
        <p class="text-4xl font-bold text-green-600"><?= formatRupiah($today_revenue) ?></p>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card p-6">
    <h2 class="text-2xl font-bold mb-4">Transaksi Terbaru</h2>
    <table class="w-full">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2">ID</th>
                <th class="text-left py-2">Pelanggan</th>
                <th class="text-left py-2">Layanan</th>
                <th class="text-left py-2">Total</th>
                <th class="text-left py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_transactions as $trans): ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3">#<?= $trans['id'] ?></td>
                <td><?= sanitize($trans['customer_name']) ?></td>
                <td><?= sanitize($trans['service_type']) ?></td>
                <td><?= formatRupiah($trans['price']) ?></td>
                <td><?= getStatusBadge($trans['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
```

**Penjelasan detail**:
- **JOIN query**: Menggabungkan data dari tabel transactions dan customers
- **Aggregate functions**: COUNT(), SUM()
- **Date functions**: DATE('now') di SQLite
- **?? operator**: Default value 0 jika null
- **foreach loop**: Iterate hasil query

**Kaitannya dengan Tailwind CSS**:
- **Grid system**: grid grid-cols-1 md:grid-cols-3 gap-6
- **Responsive**: 1 column mobile, 3 columns desktop
- **Card styling**: card-gradient, p-6, rounded-lg, shadow-lg
- **Table styling**: w-full, border-b, hover:bg-gray-50

**Kaitannya dengan SQLite**:
- **Date functions**: SQLite uses DATE('now') (berbeda dengan MySQL NOW())
- **Implicit join**: INNER JOIN default

---

#### 📄 `pages/customers.php`
**Fungsi**: Halaman manajemen data pelanggan (CRUD).

**Konsep PHP Native**:
- **CRUD Operations**: Create, Read, Update, Delete
- **Modal Forms**: Add/Edit customer dengan JavaScript
- **AJAX**: Asynchronous form submission

**Fitur**:
- List semua customers dalam table
- Search/filter customers
- Add new customer (modal form)
- Edit customer (modal form)
- Delete customer (confirmation)

**Struktur query**:
```php
// Read all customers
$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll();

// Search customers (jika ada search query)
if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $stmt = $pdo->prepare("
        SELECT * FROM customers 
        WHERE name LIKE :search OR phone LIKE :search
        ORDER BY created_at DESC
    ");
    $stmt->execute(['search' => $search]);
    $customers = $stmt->fetchAll();
}
```

**Kaitannya dengan Tailwind CSS**:
- **Form inputs**: Styled dengan form-input class
- **Buttons**: btn-primary, btn-secondary, btn-danger
- **Modal**: modal-overlay, modal-content classes
- **Table**: Responsive table dengan scroll horizontal di mobile

**JavaScript Integration**:
```javascript
// Open add customer modal
function openAddModal() {
    document.getElementById('customerModal').classList.remove('hidden');
}

// Submit via AJAX
async function submitCustomer(formData) {
    const response = await fetch('/api/customers-api.php', {
        method: 'POST',
        body: formData
    });
    const result = await response.json();
    // Handle response
}
```

---

#### 📄 `pages/transactions.php`
**Fungsi**: Halaman manajemen transaksi laundry.

**Konsep PHP Native**:
- **Foreign Key Relationship**: Join dengan customers table
- **Calculated Fields**: Auto-calculate price
- **Status Management**: Update status pesanan

**Fitur**:
- List semua transactions
- Filter by status (pending, process, ready, delivered)
- Add new transaction
- Update status
- View transaction details

**Query dengan JOIN**:
```php
$stmt = $pdo->query("
    SELECT 
        t.*,
        c.name as customer_name,
        c.phone as customer_phone
    FROM transactions t
    INNER JOIN customers c ON t.customer_id = c.id
    ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll();
```

**Price Calculation**:
```javascript
// Auto-calculate price based on service type and weight/quantity
function calculatePrice() {
    const serviceType = document.getElementById('service_type').value;
    const weight = parseFloat(document.getElementById('weight').value) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    
    let price = 0;
    if (serviceType === 'Cuci Kering' || serviceType === 'Cuci Setrika') {
        price = weight * 5000; // 5000 per kg
    } else if (serviceType === 'Setrika') {
        price = quantity * 3000; // 3000 per pcs
    }
    
    document.getElementById('price').value = price;
}
```

**Kaitannya dengan SQLite**:
- **INNER JOIN**: Menggabungkan data transactions dengan customers
- **Foreign Key**: customer_id → customers.id

---

#### 📄 `pages/check-order.php`
**Fungsi**: Portal untuk customer cek status pesanan mereka.

**Konsep PHP Native**:
- **Public Page**: Tidak perlu login
- **Search by Phone**: Customer input nomor telepon

**Fitur**:
- Input nomor telepon customer
- Display semua pesanan customer tersebut
- Show status progress dengan visual indicator

**Query**:
```php
if (isset($_POST['phone'])) {
    $phone = sanitize($_POST['phone']);
    
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as customer_name
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE c.phone = :phone
        ORDER BY t.created_at DESC
    ");
    $stmt->execute(['phone' => $phone]);
    $orders = $stmt->fetchAll();
}
```

**Visual Progress Indicator dengan Tailwind**:
```html
<div class="flex items-center space-x-2">
    <div class="w-8 h-8 rounded-full <?= $status_level >= 1 ? 'bg-blue-500' : 'bg-gray-300' ?> flex items-center justify-center">
        <span class="text-white text-xs">1</span>
    </div>
    <div class="flex-1 h-1 <?= $status_level >= 2 ? 'bg-blue-500' : 'bg-gray-300' ?>"></div>
    <div class="w-8 h-8 rounded-full <?= $status_level >= 2 ? 'bg-blue-500' : 'bg-gray-300' ?>">
        <span class="text-white text-xs">2</span>
    </div>
    <!-- dst -->
</div>
```

---

### 2.6 Folder `/api`

Folder ini berisi API endpoints untuk AJAX requests.

**Konsep**:
- **RESTful API Pattern**: Handle different HTTP methods
- **JSON Response**: Return data dalam format JSON
- **Separation**: Logic API terpisah dari views

---

#### 📄 `api/customers-api.php`
**Fungsi**: API endpoint untuk operasi CRUD customers.

**HTTP Methods**:
- **POST**: Create new customer
- **PUT**: Update existing customer
- **DELETE**: Delete customer
- **GET**: Get customer by ID

**Struktur**:
```php
<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$pdo = Database::connect();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            // Create customer
            $name = sanitize($_POST['name']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address'] ?? '');
            
            $stmt = $pdo->prepare("
                INSERT INTO customers (name, phone, address) 
                VALUES (:name, :phone, :address)
            ");
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'address' => $address
            ]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            // Update customer
            parse_str(file_get_contents('php://input'), $put_data);
            
            $id = $put_data['id'];
            $name = sanitize($put_data['name']);
            $phone = sanitize($put_data['phone']);
            $address = sanitize($put_data['address'] ?? '');
            
            $stmt = $pdo->prepare("
                UPDATE customers 
                SET name = :name, phone = :phone, address = :address
                WHERE id = :id
            ");
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'id' => $id
            ]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Pelanggan berhasil diupdate'
            ]);
            break;
            
        case 'DELETE':
            // Delete customer
            parse_str(file_get_contents('php://input'), $delete_data);
            $id = $delete_data['id'];
            
            $stmt = $pdo->prepare("DELETE FROM customers WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus'
            ]);
            break;
            
        case 'GET':
            // Get single customer
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $customer = $stmt->fetch();
            
            jsonResponse([
                'success' => true,
                'data' => $customer
            ]);
            break;
    }
} catch (PDOException $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ], 500);
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
```

**Penjelasan detail**:
- **$_SERVER['REQUEST_METHOD']**: Detect HTTP method (GET, POST, PUT, DELETE)
- **parse_str(file_get_contents('php://input'))**: Parse PUT/DELETE request body
- **lastInsertId()**: Get ID of last inserted row
- **try-catch**: Error handling untuk database errors
- **jsonResponse()**: Helper function untuk send JSON

**Kaitannya dengan PHP Native**:
- **RESTful principles**: Menggunakan HTTP methods untuk different operations
- **PDO Prepared Statements**: Prevent SQL Injection
- **Error Handling**: try-catch untuk graceful error handling

---

#### 📄 `api/transactions-api.php`
**Fungsi**: API endpoint untuk operasi CRUD transactions.

**Similar structure** dengan customers-api.php, dengan tambahan:
- **Status update endpoint**: Update status pesanan
- **Price calculation**: Validate dan calculate price
- **JOIN queries**: Include customer data

**Additional endpoint - Update Status**:
```php
case 'PATCH':
    // Update status only
    parse_str(file_get_contents('php://input'), $patch_data);
    
    $id = $patch_data['id'];
    $status = $patch_data['status'];
    
    $stmt = $pdo->prepare("
        UPDATE transactions 
        SET status = :status, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmt->execute([
        'status' => $status,
        'id' => $id
    ]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Status berhasil diupdate'
    ]);
    break;
```

---

### 2.7 Folder `/assets`

#### 📁 `/assets/css`

**📄 `assets/css/input.css`**
**Fungsi**: Source file Tailwind CSS dengan directives.

**Isi**:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom Components */
@layer components {
    .btn-primary {
        @apply bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 ease-in-out shadow-md hover:shadow-lg;
    }
    
    .btn-secondary {
        @apply bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200;
    }
    
    .btn-success {
        @apply bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200;
    }
    
    .btn-danger {
        @apply bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-md p-6;
    }
    
    .card-gradient {
        @apply bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-md;
    }
    
    .form-input {
        @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent;
    }
    
    .badge {
        @apply inline-block px-3 py-1 rounded-full text-xs font-semibold;
    }
    
    .modal-overlay {
        @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
    }
    
    .modal-content {
        @apply bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6;
    }
}

/* Custom Utilities */
@layer utilities {
    .text-shadow {
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
}
```

**Penjelasan Tailwind Directives**:
- **@tailwind base**: Reset CSS dan base styles
- **@tailwind components**: Component classes
- **@tailwind utilities**: Utility classes
- **@layer components**: Custom reusable components
- **@apply**: Apply Tailwind utilities ke custom class

**Mengapa pakai @layer**:
- Organize custom CSS dengan proper specificity
- Dapat di-purge saat production build
- Follow Tailwind best practices

---

**📄 `assets/css/style.css`**
**Fungsi**: Compiled/generated CSS dari input.css.

**Karakteristik**:
- **Auto-generated**: Jangan edit manual
- **Large file**: Berisi semua Tailwind utilities
- **Production**: Akan di-minify dan purge saat build

**Build process**:
```bash
# Development (watch mode)
npm run dev
# Compile saat ada perubahan di input.css atau file PHP

# Production (minified)
npm run build
# Compile dan minify untuk production
```

---

#### 📁 `/assets/js`

**📄 `assets/js/main.js`**
**Fungsi**: JavaScript utilities untuk interaktivitas.

**Fitur utama**:
```javascript
// Modal Management
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Format currency input
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    input.value = new Intl.NumberFormat('id-ID').format(value);
}

// Format phone number
function formatPhoneInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value.startsWith('0')) {
        input.value = value;
    } else if (value.startsWith('62')) {
        input.value = '0' + value.substring(2);
    }
}

// Show notification
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// AJAX Form Submission
async function submitForm(formId, apiUrl) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('success', result.message);
            location.reload(); // Reload page to show new data
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        showNotification('error', 'Terjadi kesalahan: ' + error.message);
    }
}

// Filter table
function filterTable(inputId, tableId, columnIndex) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cell = rows[i].getElementsByTagName('td')[columnIndex];
        if (cell) {
            const textValue = cell.textContent || cell.innerText;
            rows[i].style.display = textValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
}

// Auto-save form data to localStorage (draft)
function autoSaveForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            localStorage.setItem(`draft_${formId}`, JSON.stringify(data));
        });
    });
}

// Load draft from localStorage
function loadDraft(formId) {
    const draft = localStorage.getItem(`draft_${formId}`);
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) input.value = data[key];
        });
    }
}
```

**Penjelasan JavaScript Practices**:
- **async/await**: Modern asynchronous JavaScript
- **Fetch API**: Modern alternative to XMLHttpRequest
- **Template literals**: String interpolation dengan backticks
- **Arrow functions**: Concise function syntax
- **DOM Manipulation**: querySelector, classList, createElement
- **LocalStorage**: Client-side storage untuk draft

---

### 2.8 Folder `/node_modules`

**Fungsi**: Dependencies yang di-install via npm.

**Isi**:
- **tailwindcss**: Main Tailwind CSS package
- **Dependencies**: Dependencies dari Tailwind (postcss, dll)

**Notes**:
- **Jangan commit**: Harus di .gitignore
- **Besar**: Bisa ratusan MB
- **Reinstall**: `npm install` untuk re-download

---

## 🎓 BAGIAN 3: Konsep-Konsep Penting

### 3.1 Flow Aplikasi

**1. User Request**:
```
Browser → index.php atau pages/xxx.php
```

**2. Page Processing**:
```
Include header.php (session_start, functions, database)
↓
Query database
↓
Process data
↓
Render HTML dengan Tailwind CSS
↓
Include footer.php (close tags, load JS)
```

**3. AJAX Request**:
```
JavaScript (main.js)
↓
Fetch API → api/xxx-api.php
↓
Process (CRUD operation)
↓
JSON Response
↓
JavaScript handle response
↓
Update UI / Reload page
```

---

### 3.2 Database Flow (SQLite + PDO)

**1. Connection**:
```php
Database::connect() → PDO instance → Single connection untuk app
```

**2. Query Execution**:
```php
// Simple query (no parameters)
$stmt = $pdo->query("SELECT * FROM customers");

// Prepared statement (with parameters)
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
$stmt->execute(['id' => $id]);

// Fetch results
$single = $stmt->fetch();        // One row
$multiple = $stmt->fetchAll();   // All rows
```

**3. Transaction (untuk complex operations)**:
```php
try {
    $pdo->beginTransaction();
    
    // Multiple operations
    $pdo->exec("INSERT INTO ...");
    $pdo->exec("UPDATE ...");
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollback();
    throw $e;
}
```

---

### 3.3 Tailwind CSS Build Process

**1. Development**:
```bash
npm run dev
```
- Watch files: `*.php` (defined in tailwind.config.js)
- Detect classes: bg-blue-500, text-white, dll
- Compile: input.css → style.css
- Auto-reload: Saat ada perubahan

**2. Production**:
```bash
npm run build
```
- Scan all files
- Extract used classes only (tree-shaking)
- Minify CSS
- Result: Small, optimized CSS file

**3. Custom Components**:
```css
/* input.css */
@layer components {
    .btn-primary {
        @apply bg-blue-600 text-white px-4 py-2 rounded;
    }
}
```

**4. Usage di HTML**:
```html
<!-- Menggunakan custom component -->
<button class="btn-primary">Submit</button>

<!-- Atau direct utility classes -->
<button class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
```

---

### 3.4 Security Best Practices

**1. SQL Injection Prevention**:
```php
// ❌ JANGAN seperti ini
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ GUNAKAN prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);
```

**2. XSS Prevention**:
```php
// ❌ JANGAN echo langsung
echo $_POST['name'];

// ✅ GUNAKAN sanitize
echo sanitize($_POST['name']);
// atau
echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
```

**3. Input Validation**:
```php
// Validate phone number
if (!preg_match('/^[0-9]{10,13}$/', $phone)) {
    throw new Exception('Nomor telepon tidak valid');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Email tidak valid');
}
```

**4. File Upload (jika ada)**:
```php
// Validate file type
$allowed = ['jpg', 'jpeg', 'png', 'pdf'];
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (!in_array(strtolower($ext), $allowed)) {
    throw new Exception('File type tidak diizinkan');
}

// Validate file size
if ($_FILES['file']['size'] > 2 * 1024 * 1024) { // 2MB
    throw new Exception('File terlalu besar');
}
```

---

## 📚 BAGIAN 4: Tips Development

### 4.1 Debugging

**1. PHP Errors**:
```php
// Enable error reporting (development only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debugging variables
var_dump($variable);
print_r($array);
die('Stop here');
```

**2. Database Queries**:
```php
// Print query
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
echo $stmt->queryString; // Show query

// Show PDO errors
try {
    $stmt->execute(['id' => $id]);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "Query: " . $stmt->queryString;
}
```

**3. JavaScript Console**:
```javascript
console.log('Debug:', variable);
console.table(arrayData);
console.error('Error:', errorMessage);

// Network debugging
fetch(url)
    .then(response => {
        console.log('Response:', response);
        return response.json();
    })
    .then(data => console.log('Data:', data))
    .catch(error => console.error('Error:', error));
```

---

### 4.2 Development Workflow

**1. Start Development**:
```bash
# Terminal 1: Tailwind watch
npm run dev

# Terminal 2: PHP server
php -S localhost:8000
```

**2. Make Changes**:
- Edit PHP files: Refresh browser
- Edit CSS (input.css): Tailwind auto-compile
- Edit JS (main.js): Refresh browser

**3. Test**:
- Test CRUD operations
- Test responsive design (mobile, tablet, desktop)
- Test error handling
- Test edge cases

**4. Before Commit**:
```bash
# Build production CSS
npm run build

# Test application thoroughly
# Check for errors in console
```

---

### 4.3 Common Issues & Solutions

**1. Tailwind CSS not working**:
```bash
# Solution: Check if Tailwind is watching
npm run dev

# Check tailwind.config.js content paths
content: ["./pages/**/*.php", "./includes/**/*.php"]
```

**2. Database not found**:
```bash
# Solution: Initialize database
php database/init.php

# Check file permissions
chmod 644 database/laundry.db
chmod 755 database/
```

**3. AJAX not working**:
```javascript
// Solution: Check network tab in browser DevTools
// Check API response
// Check console for errors

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
```

**4. Modal not showing**:
```javascript
// Solution: Check if modal has 'hidden' class
// Check if openModal() function is called
// Check z-index value
```

---

## 🎯 BAGIAN 5: Latihan & Next Steps

### 5.1 Latihan untuk Pemula

**1. Modifikasi Dashboard**:
- Tambah card baru untuk "Pendapatan Bulan Ini"
- Ubah warna card menggunakan Tailwind utilities
- Tambah icon di setiap card

**2. Tambah Field di Customers**:
- Tambah field "email" di tabel customers
- Update form add/edit customer
- Update API untuk handle email

**3. Buat Report Page**:
- Halaman baru untuk laporan penjualan
- Filter berdasarkan tanggal
- Export ke PDF (challenge)

---

### 5.2 Advanced Features

**1. Authentication System**:
- Login/logout functionality
- Session-based authentication
- Role-based access control

**2. Real-time Updates**:
- WebSocket atau Server-Sent Events
- Auto-refresh dashboard tanpa reload
- Notification system

**3. Payment Integration**:
- Integrate payment gateway
- Payment history
- Invoice generation

**4. Reporting**:
- Daily/monthly/yearly reports
- Charts dengan Chart.js
- Export to Excel/PDF

---

## 📖 Resources untuk Belajar Lebih Lanjut

### PHP Native
- [PHP Manual Official](https://www.php.net/manual/en/)
- [PHP The Right Way](https://phptherightway.com/)
- [PDO Tutorial](https://www.php.net/manual/en/book.pdo.php)

### SQLite
- [SQLite Official Docs](https://www.sqlite.org/docs.html)
- [SQLite Tutorial](https://www.sqlitetutorial.net/)

### Tailwind CSS
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Tailwind UI Components](https://tailwindui.com/)
- [Tailwind Play (Online Editor)](https://play.tailwindcss.com/)

### JavaScript
- [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- [JavaScript.info](https://javascript.info/)
- [Fetch API Guide](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

---

## ✨ Penutup

Dengan memahami struktur projek ini dan konsep-konsep yang ada di dalamnya, Anda sekarang memiliki fondasi yang kuat untuk:

1. ✅ Melanjutkan development projek ini
2. ✅ Menambah fitur-fitur baru
3. ✅ Memperbaiki bug yang mungkin muncul
4. ✅ Mengoptimasi performa aplikasi
5. ✅ Membuat projek serupa dari nol

**Ingat**:
- Practice makes perfect - terus berlatih dan eksperimen
- Baca dokumentasi official untuk detail lebih lanjut
- Jangan takut membuat kesalahan - error adalah bagian dari proses belajar
- Join komunitas developer untuk bertanya dan sharing

**Selamat belajar dan happy coding! 🚀**

---

*Dokumen ini dibuat dengan ❤️ untuk membantu developer memahami D'four Laundry Management System*
