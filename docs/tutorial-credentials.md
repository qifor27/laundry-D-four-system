# 🔑 Tutorial Mendapatkan Credential Keys

Panduan lengkap untuk mendapatkan credential keys yang diperlukan untuk fitur OAuth dan Email di D'four Laundry System.

---

## 📋 Daftar Credentials yang Diperlukan

| Credential | Kegunaan | Wajib? |
|------------|----------|--------|
| Google Client ID | Login dengan Google | Opsional |
| Google Client Secret | Login dengan Google | Opsional |
| Gmail App Password | Kirim email notifikasi | Opsional |

---

## 🔐 1. Google OAuth Credentials

### Langkah 1: Buka Google Cloud Console

1. Buka browser dan akses: **https://console.cloud.google.com**
2. Login dengan akun Google Anda

### Langkah 2: Buat Project Baru

1. Klik **dropdown project** di pojok kiri atas (sebelah logo "Google Cloud")
2. Klik **"New Project"**
3. Isi detail:
   - **Project name**: `DFour Laundry` (atau nama lain)
   - **Location**: Biarkan default
4. Klik **"Create"**
5. Tunggu beberapa saat, lalu pastikan project sudah terpilih

### Langkah 3: Konfigurasi OAuth Consent Screen

1. Di menu kiri, klik **"APIs & Services"** → **"OAuth consent screen"**
2. Pilih **"External"** → Klik **"Create"**
3. Isi form:
   - **App name**: `D'four Laundry`
   - **User support email**: Pilih email Anda
   - **Developer contact email**: Email Anda
4. Klik **"Save and Continue"**
5. Di bagian **Scopes**, klik **"Add or Remove Scopes"**
   - Centang: `openid`, `email`, `profile`
   - Klik **"Update"** → **"Save and Continue"**
6. Di bagian **Test users**:
   - Klik **"Add Users"**
   - Masukkan email yang akan digunakan untuk testing
   - Klik **"Add"** → **"Save and Continue"**
7. Klik **"Back to Dashboard"**

### Langkah 4: Buat OAuth 2.0 Credentials

1. Di menu kiri, klik **"APIs & Services"** → **"Credentials"**
2. Klik **"+ Create Credentials"** → **"OAuth client ID"**
3. Pilih:
   - **Application type**: `Web application`
   - **Name**: `DFour Laundry Web`
4. Di bagian **Authorized JavaScript origins**, klik **"+ Add URI"**:
   ```
   http://localhost
   ```
5. Di bagian **Authorized redirect URIs**, klik **"+ Add URI"**:
   ```
   http://localhost/laundry-D-four/api/google-auth.php
   ```
   > ⚠️ **Penting:** Sesuaikan path dengan folder project Anda!
   
6. Klik **"Create"**

### Langkah 5: Salin Credentials

Setelah berhasil, akan muncul popup dengan:
- **Client ID**: `xxxxx.apps.googleusercontent.com`
- **Client Secret**: `GOCSPX-xxxxx`

**Salin keduanya!**

### Langkah 6: Konfigurasi di Project

Buat file `config/google-oauth.php`:

```php
<?php
/**
 * Google OAuth Configuration
 */

// Google OAuth Credentials
define('GOOGLE_CLIENT_ID', 'PASTE_CLIENT_ID_DISINI.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'PASTE_CLIENT_SECRET_DISINI');

// Redirect URI (harus sama dengan yang di Google Console)
define('GOOGLE_REDIRECT_URI', 'http://localhost/laundry-D-four/api/google-auth.php');

// OAuth Scopes
define('GOOGLE_SCOPES', [
    'openid',
    'email', 
    'profile'
]);
?>
```

---

## 📧 2. Gmail App Password (untuk Email SMTP)

### Prasyarat
- Akun Gmail
- **2-Step Verification** sudah aktif di akun Google

### Langkah 1: Aktifkan 2-Step Verification

1. Buka: **https://myaccount.google.com/security**
2. Scroll ke bagian **"How you sign in to Google"**
3. Klik **"2-Step Verification"**
4. Ikuti langkah untuk mengaktifkan (nomor HP, dll)

### Langkah 2: Buat App Password

1. Buka: **https://myaccount.google.com/apppasswords**
   
   Atau dari Google Account:
   - **Security** → **2-Step Verification** → scroll ke bawah → **App passwords**

2. Di dropdown **"Select app"**, pilih: **Mail**
3. Di dropdown **"Select device"**, pilih: **Windows Computer** (atau sesuaikan)
4. Klik **"Generate"**

5. **Salin password 16 karakter** yang muncul (contoh: `abcd efgh ijkl mnop`)
   > ⚠️ Password ini hanya ditampilkan sekali! Simpan baik-baik.

### Langkah 3: Konfigurasi di Project

Buat file `config/email.php`:

```php
<?php
/**
 * Email SMTP Configuration
 */

// Gmail SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');

// Gmail Credentials
define('SMTP_USERNAME', 'email-anda@gmail.com');      // Email Gmail Anda
define('SMTP_PASSWORD', 'xxxx xxxx xxxx xxxx');       // App Password (16 karakter)

// From Email
define('MAIL_FROM_EMAIL', 'email-anda@gmail.com');
define('MAIL_FROM_NAME', "D'four Laundry");
?>
```

---

## ✅ Checklist Setelah Setup

### Google OAuth
- [ ] Project dibuat di Google Cloud Console
- [ ] OAuth Consent Screen dikonfigurasi
- [ ] OAuth 2.0 Client ID dibuat
- [ ] Redirect URI sudah benar
- [ ] Client ID & Secret disimpan di `config/google-oauth.php`

### Email SMTP
- [ ] 2-Step Verification aktif di Google Account
- [ ] App Password dibuat
- [ ] Credentials disimpan di `config/email.php`

---

## 🔧 Testing

### Test Google Login
1. Buka: `http://localhost/laundry-D-four/pages/auth/login.php`
2. Klik tombol **"Login dengan Google"**
3. Pilih akun Google (gunakan email yang ada di Test Users)
4. Jika berhasil, akan redirect ke dashboard

### Test Email
```php
// Test kirim email (buat file test_email.php)
<?php
require_once 'config/email.php';
require_once 'vendor/autoload.php'; // Jika pakai PHPMailer

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->SMTPAuth = true;
$mail->Username = SMTP_USERNAME;
$mail->Password = SMTP_PASSWORD;
$mail->SMTPSecure = SMTP_ENCRYPTION;
$mail->Port = SMTP_PORT;

$mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
$mail->addAddress('test@example.com');
$mail->Subject = 'Test Email';
$mail->Body = 'Ini email test dari D\'four Laundry';

$mail->send();
echo 'Email berhasil dikirim!';
?>
```

---

## ⚠️ Troubleshooting

### Error: "redirect_uri_mismatch"
- Pastikan Redirect URI di Google Console **PERSIS SAMA** dengan di code
- Perhatikan: `http` vs `https`, trailing slash, nama folder

### Error: "Access blocked: This app's request is invalid"
- OAuth Consent Screen belum dikonfigurasi dengan benar
- Pastikan email test user sudah ditambahkan

### Error: "Less secure app access"
- Gmail tidak lagi support "Less secure apps"
- **Gunakan App Password** (seperti tutorial di atas)

### Error: "Application-specific password required"
- 2-Step Verification sudah aktif tapi belum buat App Password
- Ikuti langkah membuat App Password

---

## 📁 Struktur File Config

```
config/
├── database_mysql.php    # Database credentials
├── google-oauth.php      # Google OAuth credentials ← BARU
└── email.php             # Email SMTP credentials ← BARU
```

---

## 🔒 Security Tips

1. **JANGAN commit file credentials** ke Git!
   
   Tambahkan ke `.gitignore`:
   ```
   config/google-oauth.php
   config/email.php
   ```

2. **Untuk production**, simpan credentials di environment variables:
   ```php
   define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
   ```

3. **Rotasi credentials** secara berkala untuk keamanan

---

**Selesai! 🎉** Credentials sudah siap digunakan.
