# 📚 Tutorial: Email Notifications

**Fitur:** Kirim email notifikasi ke pelanggan  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 2-3 jam  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Setup SMTP](#2-setup-smtp)
3. [File yang Akan Dibuat](#3-file-yang-akan-dibuat)
4. [Langkah 1: Konfigurasi Email](#langkah-1-konfigurasi-email)
5. [Langkah 2: Email Helper](#langkah-2-email-helper)
6. [Langkah 3: Template Email](#langkah-3-template-email)
7. [Langkah 4: Integrasi ke API](#langkah-4-integrasi-ke-api)
8. [Testing](#testing)
9. [Troubleshooting](#troubleshooting)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa Fitur Ini Diperlukan?

Pelanggan perlu diberi tahu tentang:
1. **Status order berubah** - Dari pending → washing → done
2. **Order siap diambil** - Notifikasi pickup ready
3. **Promo/Diskon** - Marketing emails (opsional)

### 📧 Jenis Email

| Jenis | Trigger | Penerima |
|-------|---------|----------|
| **Order Created** | Transaksi baru dibuat | Customer |
| **Status Update** | Status berubah | Customer |
| **Ready for Pickup** | Status = done | Customer |
| **Monthly Report** | Awal bulan (opsional) | Admin |

---

## 2. Setup Gmail SMTP

Tutorial ini menggunakan **Gmail SMTP** karena:
- ✅ **Gratis** - 500 email/hari
- ✅ **Reliable** - Server Google stabil
- ✅ **Tidak masuk spam** - Reputasi tinggi
- ✅ **Mudah setup** - 10 menit selesai

### 📊 Spesifikasi Gmail SMTP

| Setting | Value |
|---------|-------|
| **Host** | `smtp.gmail.com` |
| **Port** | `587` |
| **Encryption** | `TLS` |
| **Auth** | App Password (bukan password biasa) |
| **Limit** | 500 email/hari |

---

### 🔧 Langkah Setup Gmail SMTP

#### Langkah 1: Aktifkan 2-Step Verification

1. Buka: https://myaccount.google.com/security
2. Scroll ke **"How you sign in to Google"**
3. Klik **"2-Step Verification"**
4. Ikuti wizard untuk mengaktifkan (pakai HP untuk verifikasi)

> ⚠️ **PENTING:** 2-Step Verification HARUS aktif untuk bisa membuat App Password!

#### Langkah 2: Buat App Password

1. Buka: https://myaccount.google.com/apppasswords
2. Di dropdown **"Select app"**, pilih **"Mail"**
3. Di dropdown **"Select device"**, pilih **"Windows Computer"**
4. Klik **"Generate"**
5. **COPY password 16 karakter** yang muncul (contoh: `abcd efgh ijkl mnop`)


> 📝 **Catatan:** Hilangkan spasi saat menggunakan password. 
> Contoh: `abcdefghijklmnop`

#### Langkah 3: Simpan Kredensial

Setelah mendapat App Password, catat:

```
Gmail Email: your-email@gmail.com
App Password: abcdefghijklmnop (16 karakter tanpa spasi)
```

---


## 3. File yang Akan Dibuat

| No | File | Deskripsi |
|----|------|-----------|
| 1 | `config/email.php.template` | Template konfigurasi SMTP |
| 2 | `includes/email-helper.php` | Helper functions untuk kirim email |
| 3 | `templates/email/order-status.php` | Template HTML email |

---

## Langkah 1: Konfigurasi Email

### 📄 File: `config/email.php.template`

Buat file baru di: `c:\xampp\htdocs\laundry-D-four-system\config\email.php.template`

### Kode:

```php
<?php
/**
 * Email Configuration
 * 
 * Copy file ini ke email.php dan isi dengan kredensial SMTP Anda.
 * File email.php sudah di-ignore oleh git untuk keamanan.
 * 
 * PENTING: Jangan commit file email.php ke repository!
 */

// ================================================
// SMTP CONFIGURATION
// ================================================

// SMTP Host (Gmail: smtp.gmail.com, Mailtrap: smtp.mailtrap.io)
define('SMTP_HOST', 'smtp.gmail.com');

// SMTP Port (Gmail: 587, SSL: 465)
define('SMTP_PORT', 587);

// SMTP Username (email address)
define('SMTP_USERNAME', 'your-email@gmail.com');

// SMTP Password (App Password for Gmail)
define('SMTP_PASSWORD', 'your-app-password');

// SMTP Encryption (tls atau ssl)
define('SMTP_ENCRYPTION', 'tls');

// ================================================
// SENDER CONFIGURATION
// ================================================

// From Email (email pengirim)
define('EMAIL_FROM', 'noreply@dfourlaundry.com');

// From Name (nama pengirim)
define('EMAIL_FROM_NAME', "D'four Laundry");

// Reply-To Email (email untuk reply)
define('EMAIL_REPLY_TO', 'cs@dfourlaundry.com');

// ================================================
// FEATURE FLAGS
// ================================================

// Aktifkan email notifications
define('EMAIL_ENABLED', true);

// Debug mode (tampilkan error detail)
define('EMAIL_DEBUG', false);

// ================================================
// HELPER FUNCTION
// ================================================

/**
 * Check if email is configured properly
 */
function isEmailConfigured() {
    if (!defined('SMTP_HOST') || SMTP_HOST === 'smtp.gmail.com') {
        // Cek apakah password sudah diganti dari default
        if (!defined('SMTP_PASSWORD') || SMTP_PASSWORD === 'your-app-password') {
            return false;
        }
    }
    return defined('EMAIL_ENABLED') && EMAIL_ENABLED === true;
}
?>
```

### 📝 Langkah Setup:

1. Copy `email.php.template` ke `email.php`
2. Edit `email.php` dengan kredensial SMTP Anda
3. Pastikan `email.php` ada di `.gitignore`

---

## Langkah 2: Email Helper

### 📄 File: `includes/email-helper.php`

Buat file baru di: `c:\xampp\htdocs\laundry-D-four-system\includes\email-helper.php`

### Kode (Tanpa Library Eksternal):

```php
<?php
/**
 * Email Helper Functions
 * 
 * Fungsi-fungsi untuk mengirim email menggunakan PHP mail() atau SMTP socket.
 * Tidak memerlukan library eksternal seperti PHPMailer.
 */

// Load email config
$emailConfigPath = __DIR__ . '/../config/email.php';
if (file_exists($emailConfigPath)) {
    require_once $emailConfigPath;
}

/**
 * Send Email via SMTP
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param array $options Optional settings
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $options = []) {
    // Check if email is enabled
    if (!defined('EMAIL_ENABLED') || !EMAIL_ENABLED) {
        return ['success' => false, 'message' => 'Email notifications are disabled'];
    }
    
    // Check configuration
    if (!isEmailConfigured()) {
        return ['success' => false, 'message' => 'Email not configured properly'];
    }
    
    $fromEmail = $options['from'] ?? EMAIL_FROM;
    $fromName = $options['from_name'] ?? EMAIL_FROM_NAME;
    $replyTo = $options['reply_to'] ?? EMAIL_REPLY_TO;
    
    // Build headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $replyTo,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    // Try to send via PHP mail()
    // Note: For production, use PHPMailer or similar library
    try {
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if ($result) {
            logEmail($to, $subject, 'sent');
            return ['success' => true, 'message' => 'Email sent successfully'];
        } else {
            logEmail($to, $subject, 'failed');
            return ['success' => false, 'message' => 'Failed to send email. Check server mail configuration.'];
        }
    } catch (Exception $e) {
        logEmail($to, $subject, 'error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Send Order Status Email
 * 
 * @param array $transaction Transaction data
 * @param array $customer Customer data
 * @param string $newStatus New status
 * @return array
 */
function sendOrderStatusEmail($transaction, $customer, $newStatus) {
    // Check if customer has email
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Update Status Pesanan #" . $transaction['id'] . " - D'four Laundry";
    
    // Load template
    $body = getOrderStatusEmailTemplate($transaction, $customer, $newStatus);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Send Order Created Email
 * 
 * @param array $transaction Transaction data
 * @param array $customer Customer data
 * @return array
 */
function sendOrderCreatedEmail($transaction, $customer) {
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Pesanan Baru #" . $transaction['id'] . " - D'four Laundry";
    
    $body = getOrderCreatedEmailTemplate($transaction, $customer);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Send Ready for Pickup Email
 * 
 * @param array $transaction Transaction data
 * @param array $customer Customer data
 * @return array
 */
function sendReadyForPickupEmail($transaction, $customer) {
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Pesanan #" . $transaction['id'] . " Siap Diambil! - D'four Laundry";
    
    $body = getReadyForPickupEmailTemplate($transaction, $customer);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Log email activity
 */
function logEmail($to, $subject, $status) {
    $logFile = __DIR__ . '/../logs/email.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject | Status: $status\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Get status label in Indonesian
 */
function getEmailStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu Proses',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai - Siap Diambil',
        'picked_up' => 'Sudah Diambil',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? $status;
}

// ================================================
// EMAIL TEMPLATES
// ================================================

/**
 * Order Status Update Template
 */
function getOrderStatusEmailTemplate($transaction, $customer, $newStatus) {
    $statusLabel = getEmailStatusLabel($newStatus);
    $statusColor = $newStatus === 'done' ? '#22c55e' : '#9333ea';
    
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#9333ea,#7c3aed);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">D'four Laundry</h1>
                <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;">Update Status Pesanan</p>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;margin:0 0 20px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                
                <p style="color:#374151;font-size:16px;margin:0 0 20px;">
                    Status pesanan Anda telah diperbarui:
                </p>
                
                <!-- Status Badge -->
                <div style="text-align:center;margin:30px 0;">
                    <span style="background:{$statusColor};color:#fff;padding:12px 24px;border-radius:50px;font-weight:bold;font-size:18px;">
                        {$statusLabel}
                    </span>
                </div>
                
                <!-- Order Details -->
                <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:20px 0;">
                    <h3 style="color:#374151;margin:0 0 15px;font-size:14px;text-transform:uppercase;">Detail Pesanan</h3>
                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">No. Pesanan</td>
                            <td style="padding:8px 0;color:#111827;font-weight:bold;text-align:right;">#{$transaction['id']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">Layanan</td>
                            <td style="padding:8px 0;color:#111827;text-align:right;">{$transaction['service_type']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">Total</td>
                            <td style="padding:8px 0;color:#111827;font-weight:bold;text-align:right;">Rp " . number_format($transaction['price'], 0, ',', '.') . "</td>
                        </tr>
                    </table>
                </div>
                
                <!-- CTA Button -->
                <div style="text-align:center;margin:30px 0;">
                    <a href="#" style="display:inline-block;background:#9333ea;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;">
                        Cek Status Pesanan
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="color:#6b7280;font-size:12px;margin:0;">
                    © " . date('Y') . " D'four Laundry. All rights reserved.
                </p>
                <p style="color:#9ca3af;font-size:11px;margin:10px 0 0;">
                    Email ini dikirim otomatis, mohon tidak membalas email ini.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Order Created Template
 */
function getOrderCreatedEmailTemplate($transaction, $customer) {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">✓ Pesanan Diterima!</h1>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                <p style="color:#374151;font-size:16px;">
                    Terima kasih telah menggunakan layanan D'four Laundry. Pesanan Anda telah kami terima.
                </p>
                
                <div style="background:#f0fdf4;border-left:4px solid #22c55e;padding:15px;margin:20px 0;">
                    <strong style="color:#166534;">No. Pesanan: #{$transaction['id']}</strong>
                </div>
                
                <p style="color:#6b7280;font-size:14px;">
                    Kami akan segera memproses pesanan Anda. Anda akan menerima notifikasi email saat status berubah.
                </p>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;">
                <p style="color:#6b7280;font-size:12px;margin:0;">D'four Laundry</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Ready for Pickup Template
 */
function getReadyForPickupEmailTemplate($transaction, $customer) {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">🎉 Pesanan Siap Diambil!</h1>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                <p style="color:#374151;font-size:16px;">
                    Kabar baik! Pesanan laundry Anda sudah selesai dan siap untuk diambil.
                </p>
                
                <div style="background:#fffbeb;border:2px dashed #f59e0b;border-radius:12px;padding:20px;margin:20px 0;text-align:center;">
                    <p style="color:#92400e;font-size:14px;margin:0 0 10px;">No. Pesanan</p>
                    <p style="color:#78350f;font-size:28px;font-weight:bold;margin:0;">#{$transaction['id']}</p>
                </div>
                
                <p style="color:#374151;font-size:16px;">
                    <strong>Total Pembayaran:</strong> Rp " . number_format($transaction['price'], 0, ',', '.') . "
                </p>
                
                <div style="background:#f9fafb;border-radius:8px;padding:15px;margin:20px 0;">
                    <p style="color:#374151;font-size:14px;margin:0;">
                        📍 <strong>Alamat Pickup:</strong><br>
                        D'four Laundry<br>
                        Jl. Contoh No. 123
                    </p>
                </div>
                
                <p style="color:#6b7280;font-size:14px;">
                    Jam Operasional: 08.00 - 21.00 WIB
                </p>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;">
                <p style="color:#6b7280;font-size:12px;margin:0;">D'four Laundry - Bersih dan Wangi!</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}
?>
```

---

## Langkah 3: Update .gitignore

Tambahkan ke `.gitignore`:

```
# Email config (contains credentials)
config/email.php
```

---

## Langkah 4: Integrasi ke API

### Update `api/transactions-api.php`

Tambahkan di bagian `case 'update_status'`:

```php
// Setelah update status berhasil, kirim email
require_once __DIR__ . '/../includes/email-helper.php';

// Get customer data
$customerStmt = $db->prepare("
    SELECT c.*, u.email 
    FROM customers c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.id = ?
");
$customerStmt->execute([$transaction['customer_id']]);
$customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

if ($customer) {
    // Send email based on new status
    if ($status === 'done') {
        sendReadyForPickupEmail($transaction, $customer);
    } else {
        sendOrderStatusEmail($transaction, $customer, $status);
    }
}
```

---

## Testing

### A. Test Konfigurasi

Buat file test sementara:

```php
<?php
// test_email.php (HAPUS SETELAH TESTING)
require_once 'config/email.php';
require_once 'includes/email-helper.php';

echo "Email Configured: " . (isEmailConfigured() ? 'Yes' : 'No') . "\n";

$result = sendEmail(
    'your-test-email@gmail.com',
    'Test Email dari D\'four Laundry',
    '<h1>Ini adalah test email</h1><p>Jika Anda menerima email ini, konfigurasi berhasil!</p>'
);

print_r($result);
?>
```

### B. Test via Browser

```
http://localhost/laundry-D-four-system/test_email.php
```

### C. Cek Log

Log email tersimpan di: `logs/email.log`

---

## Troubleshooting

### ❌ Email tidak terkirim

**Gejala:** `mail()` return false

**Solusi:**
1. Pastikan XAMPP sendmail sudah dikonfigurasi
2. Atau gunakan PHPMailer untuk SMTP langsung

### ❌ Gmail blocked

**Gejala:** Email gagal dengan Gmail SMTP

**Solusi:**
1. Gunakan App Password, bukan password biasa
2. Aktifkan "Less secure apps" (tidak recommended)
3. Atau gunakan Mailtrap untuk testing

### ❌ Template tidak tampil dengan benar

**Gejala:** HTML email tidak ter-render

**Solusi:** Pastikan header `Content-Type: text/html` sudah ditambahkan

---

## 🚀 Upgrade ke PHPMailer (Recommended for Production)

Untuk production, install PHPMailer:

```bash
composer require phpmailer/phpmailer
```

Lalu update `sendEmail()` function:

```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $options = []) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(EMAIL_REPLY_TO);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

---

## ✅ Checklist Implementasi

- [ ] Buat `config/email.php.template`
- [ ] Copy ke `config/email.php` dan isi kredensial
- [ ] Buat `includes/email-helper.php`
- [ ] Tambahkan `config/email.php` ke `.gitignore`
- [ ] Update `api/transactions-api.php` untuk kirim email
- [ ] Test kirim email
- [ ] Cek email diterima di inbox
- [ ] Cek log di `logs/email.log`

---

**Selamat Coding! 📧**
