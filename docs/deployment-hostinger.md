# 🚀 Panduan Deployment ke Hostinger

Panduan lengkap untuk meng-hosting D'four Smart Laundry System di Hostinger.

---

## 📋 Prasyarat

1. **Akun Hostinger** - Daftar di [hostinger.co.id](https://www.hostinger.co.id)
2. **Paket Hosting** - Minimal paket **Single Web Hosting** (Rp 9.000/bulan)
3. **Domain** - Bisa pakai subdomain gratis atau domain sendiri
4. **FTP Client** - [FileZilla](https://filezilla-project.org/) (opsional, bisa pakai File Manager)

---

## 📁 Langkah 1: Persiapan File Lokal

### 1.1 Install Dependencies (di komputer lokal)

```bash
cd e:\xampp2\htdocs\laundry-D-four
composer install --no-dev --optimize-autoloader
```

### 1.2 File yang Perlu Di-upload

```
laundry-D-four/
├── api/                    ✅ Upload
├── assets/                 ✅ Upload
├── config/                 ✅ Upload (sesuaikan konfigurasi)
├── database/               ✅ Upload (untuk referensi SQL)
├── docs/                   ❌ Tidak perlu
├── includes/               ✅ Upload
├── pages/                  ✅ Upload
├── vendor/                 ✅ Upload (hasil composer install)
├── index.php               ✅ Upload
├── login.php               ✅ Upload
├── register.php            ✅ Upload
├── admin-login.php         ✅ Upload
├── forgot-password.php     ✅ Upload
├── reset-password.php      ✅ Upload
├── verify-email.php        ✅ Upload
├── complete-profile.php    ✅ Upload
├── .htaccess               ✅ Upload (jika ada)
└── node_modules/           ❌ Tidak perlu
```

---

## 🔧 Langkah 2: Setup Hostinger

### 2.1 Login ke hPanel

1. Buka [hpanel.hostinger.com](https://hpanel.hostinger.com)
2. Login dengan akun Hostinger

### 2.2 Buat Database MySQL

1. Klik **Databases** → **MySQL Databases**
2. Isi form:
   - **Database name**: `laundry_dfour`
   - **Username**: `laundry_user`
   - **Password**: (buat password kuat)
3. Klik **Create**
4. **Catat informasi berikut**:
   ```
   Host: mysql.hostinger.com (atau lihat di panel)
   Database: u123456789_laundry_dfour
   Username: u123456789_laundry_user
   Password: [password yang dibuat]
   ```

### 2.3 Import Database Schema

1. Klik **Databases** → **phpMyAdmin**
2. Pilih database yang baru dibuat
3. Klik tab **Import**
4. Upload file `database/schema_full.sql`
5. Klik **Go**

---

## ⚙️ Langkah 3: Konfigurasi File

### 3.1 Buat file `config/database_mysql.php`

```php
<?php
/**
 * Database Configuration for Hostinger
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $this->config = [
            'host'     => 'localhost',  // Hostinger biasanya pakai localhost
            'database' => 'u123456789_laundry_dfour',  // Ganti dengan nama database Anda
            'username' => 'u123456789_laundry_user',   // Ganti dengan username Anda
            'password' => 'PASSWORD_ANDA',              // Ganti dengan password Anda
            'charset'  => 'utf8mb4'
        ];
        
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function getConfig() {
        return $this->config;
    }
}
?>
```

### 3.2 Buat file `config/email.php`

```php
<?php
/**
 * Email Configuration for Hostinger
 * Hostinger menyediakan email hosting, gunakan SMTP mereka
 */

return [
    'smtp_host'     => 'smtp.hostinger.com',
    'smtp_port'     => 465,
    'smtp_secure'   => 'ssl',
    'smtp_username' => 'noreply@domainanda.com',  // Email yang dibuat di Hostinger
    'smtp_password' => 'PASSWORD_EMAIL',
    'from_email'    => 'noreply@domainanda.com',
    'from_name'     => "D'four Laundry"
];
?>
```

### 3.3 Buat file `config/app.php`

```php
<?php
/**
 * Application Configuration for Production
 */

return [
    'name'      => "D'four Smart Laundry",
    'url'       => 'https://domainanda.com',  // Ganti dengan domain Anda
    'debug'     => false,  // Matikan debug di production
    'timezone'  => 'Asia/Jakarta'
];
?>
```

### 3.4 Update `config/google.php` (jika pakai Google OAuth)

```php
<?php
/**
 * Google OAuth Configuration
 */

return [
    'client_id'     => 'YOUR_GOOGLE_CLIENT_ID',
    'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
    'redirect_uri'  => 'https://domainanda.com/api/auth/google-callback.php'
];
?>
```

> ⚠️ **Penting**: Tambahkan domain production di [Google Cloud Console](https://console.cloud.google.com) → OAuth 2.0 → Authorized redirect URIs

---

## 📤 Langkah 4: Upload File

### Opsi A: Menggunakan File Manager (Mudah)

1. Di hPanel, klik **Files** → **File Manager**
2. Buka folder `public_html`
3. Hapus file default (jika ada)
4. Klik **Upload** → pilih semua file proyek
5. Atau upload sebagai ZIP, lalu **Extract**

### Opsi B: Menggunakan FTP (Lebih Cepat)

1. Di hPanel, klik **Files** → **FTP Accounts**
2. Catat kredensial FTP:
   ```
   Host: ftp.domainanda.com
   Username: [lihat di panel]
   Password: [password hosting]
   Port: 21
   ```
3. Buka FileZilla, connect ke server
4. Upload semua file ke folder `public_html`

---

## 🔐 Langkah 5: Setup SSL (HTTPS)

1. Di hPanel, klik **Security** → **SSL**
2. Klik **Install SSL** untuk domain Anda
3. Pilih **Free SSL** (Let's Encrypt)
4. Tunggu beberapa menit hingga aktif

---

## ✅ Langkah 6: Verifikasi Deployment

### 6.1 Tes Akses Website

Buka browser dan akses:
- `https://domainanda.com` - Halaman utama
- `https://domainanda.com/admin-login.php` - Login admin
- `https://domainanda.com/login.php` - Login user

### 6.2 Login Admin Default

```
Email: admin@dfour.com
Password: admin123
```

> ⚠️ **Penting**: Segera ganti password admin setelah login pertama kali!

### 6.3 Cek Koneksi Database

Jika ada error database, cek:
1. Kredensial di `config/database_mysql.php`
2. Database sudah di-import dengan benar
3. User database punya permission yang cukup

---

## 🔧 Troubleshooting

### Error 500 Internal Server Error

1. Cek file `.htaccess` - coba rename/hapus sementara
2. Cek permission file: `644` untuk file, `755` untuk folder
3. Aktifkan error logging di `php.ini` atau tambahkan di awal `index.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Database Connection Error

1. Pastikan host database benar (biasanya `localhost` di Hostinger)
2. Cek nama database, username, password sudah sesuai
3. Pastikan database sudah di-import

### Email Tidak Terkirim

1. Setup email account di hPanel → **Emails** → **Email Accounts**
2. Update kredensial SMTP di `config/email.php`
3. Alternatif: Gunakan layanan email seperti [Mailgun](https://mailgun.com) atau [SendGrid](https://sendgrid.com)

### Google OAuth Error

1. Tambahkan domain production di Google Cloud Console
2. Update `redirect_uri` di `config/google.php`
3. Pastikan HTTPS sudah aktif

---

## 📊 Monitoring & Maintenance

### Backup Database

1. hPanel → **Files** → **Backups**
2. Atau manual via phpMyAdmin → Export

### Cek Log Error

1. hPanel → **Advanced** → **Error Logs**
2. Atau file `error_log` di folder `public_html`

### Update Aplikasi

1. Backup database terlebih dahulu
2. Upload file baru via FTP/File Manager
3. Jalankan migrasi jika ada perubahan database

---

## 📝 Checklist Deployment

- [ ] Buat database MySQL
- [ ] Import `schema_full.sql`
- [ ] Konfigurasi `config/database_mysql.php`
- [ ] Konfigurasi `config/email.php`
- [ ] Konfigurasi `config/app.php`
- [ ] Upload semua file ke `public_html`
- [ ] Aktifkan SSL/HTTPS
- [ ] Tes halaman utama
- [ ] Tes login admin
- [ ] Tes registrasi user
- [ ] Tes email verification
- [ ] Ganti password admin default
- [ ] Setup Google OAuth (jika digunakan)

---

## 🆘 Butuh Bantuan?

- **Hostinger Support**: Live chat 24/7 di hPanel
- **Dokumentasi**: [support.hostinger.com](https://support.hostinger.com)
- **Tutorial Video**: [YouTube Hostinger Indonesia](https://youtube.com/hostingerindonesia)

---

*Dokumen ini dibuat: 2026-01-13*
