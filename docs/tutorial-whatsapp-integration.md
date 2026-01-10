# 📚 Tutorial: WhatsApp Integration

**Fitur:** Kirim notifikasi WhatsApp ke pelanggan  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 2-3 jam  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Opsi WhatsApp API](#2-opsi-whatsapp-api)
3. [Setup Fonnte API](#3-setup-fonnte-api)
4. [Langkah Implementasi](#langkah-implementasi)
5. [Testing](#testing)
6. [Troubleshooting](#troubleshooting)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa WhatsApp?

- ✅ **Open rate tinggi** - 98% pesan dibaca
- ✅ **Lebih personal** - Langsung ke HP pelanggan
- ✅ **Mudah diakses** - Hampir semua orang pakai WhatsApp
- ✅ **Hemat pulsa** - Pakai internet, bukan SMS

### 📱 Jenis Notifikasi

| Jenis | Trigger | Contoh Pesan |
|-------|---------|--------------|
| **Order Created** | Transaksi baru | "Pesanan #123 diterima" |
| **Status Update** | Status berubah | "Pesanan #123 sedang dicuci" |
| **Ready for Pickup** | Status = done | "Pesanan #123 siap diambil!" |
| **Payment Reminder** | Belum bayar | "Konfirmasi pembayaran..." |

---

## 2. Opsi WhatsApp API

### Perbandingan Provider

| Provider | Gratis | Harga | Kemudahan | Rekomendasi |
|----------|--------|-------|-----------|-------------|
| **Fonnte** | 500 pesan | Rp 100rb/bulan | ⭐⭐⭐⭐⭐ | ✅ Tutorial ini |
| **Wablas** | 1000 pesan | Rp 150rb/bulan | ⭐⭐⭐⭐ | Alternatif |
| **WhatsApp Business API** | ❌ | $$$$ | ⭐⭐ | Enterprise |
| **Twilio** | Trial | $0.005/pesan | ⭐⭐⭐ | Internasional |

### ✅ Kenapa Fonnte?

1. **Gratis 500 pesan** untuk testing
2. **Mudah setup** - 5 menit selesai
3. **Stabil** - Server Indonesia
4. **Dokumentasi lengkap** - Bahasa Indonesia
5. **Murah** - Mulai Rp 100rb/bulan

---

## 3. Setup Fonnte API

### A. Daftar Akun Fonnte

1. Buka: https://fonnte.com
2. Klik **"Daftar"**
3. Isi data dan verifikasi email
4. Login ke dashboard

### B. Hubungkan WhatsApp

1. Di dashboard, klik **"Tambah Device"**
2. Scan QR Code dengan WhatsApp Anda
3. Tunggu sampai status **"Connected"**

### C. Dapatkan API Token

1. Di dashboard, klik **"API Token"**
2. Copy token yang muncul (contoh: `abc123xyz...`)
3. Simpan token ini untuk konfigurasi

---

## 4. File yang Akan Dibuat

| No | File | Deskripsi |
|----|------|-----------|
| 1 | `config/whatsapp.php.template` | Template konfigurasi API |
| 2 | `includes/whatsapp-helper.php` | Helper functions |

---

## Langkah 1: Konfigurasi WhatsApp

### 📄 File: `config/whatsapp.php.template`

```php
<?php
/**
 * WhatsApp Configuration (Fonnte API)
 * 
 * Copy file ini ke whatsapp.php dan isi dengan API Token Anda.
 * File whatsapp.php sudah di-ignore oleh git untuk keamanan.
 * 
 * PENTING: Jangan commit file whatsapp.php ke repository!
 */

// ================================================
// FONNTE API CONFIGURATION
// ================================================

// API Token dari dashboard Fonnte
define('FONNTE_TOKEN', 'your-api-token-here');

// API Endpoint
define('FONNTE_API_URL', 'https://api.fonnte.com/send');

// ================================================
// SENDER CONFIGURATION
// ================================================

// Nama bisnis (muncul di pesan)
define('WA_BUSINESS_NAME', "D'four Laundry");

// ================================================
// FEATURE FLAGS
// ================================================

// Aktifkan WhatsApp notifications
define('WA_ENABLED', true);

// Debug mode
define('WA_DEBUG', false);

// ================================================
// MESSAGE TEMPLATES
// ================================================

// Template pesan (gunakan {variable} untuk placeholder)
define('WA_TEMPLATE_ORDER_CREATED', "
*{business_name}*

Halo {customer_name}! 👋

Pesanan Anda telah kami terima:
📦 No. Pesanan: #{order_id}
🧺 Layanan: {service_type}
💰 Total: Rp {price}

Kami akan segera memproses pesanan Anda.

Terima kasih! 🙏
");

define('WA_TEMPLATE_STATUS_UPDATE', "
*{business_name}*

Halo {customer_name}!

Status pesanan Anda telah diupdate:
📦 No. Pesanan: #{order_id}
📊 Status: *{status}*

Terima kasih telah menunggu! 🙏
");

define('WA_TEMPLATE_READY_PICKUP', "
*{business_name}*

Halo {customer_name}! 🎉

Kabar baik! Pesanan Anda sudah selesai:
📦 No. Pesanan: #{order_id}
💰 Total: Rp {price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat (Cash)
2️⃣ Transfer Bank:
   🏦 {bank_name} - {bank_account}
   👤 A/N: {bank_holder}

📍 Lokasi: D'four Laundry
🕐 Jam: 08.00 - 21.00 WIB

Ditunggu kedatangannya! 😊
");

define('WA_TEMPLATE_PAYMENT_REMINDER', "
*{business_name}*

Halo {customer_name}! 👋

Kami ingin mengingatkan bahwa pesanan Anda belum dibayar:
📦 No. Pesanan: #{order_id}
💰 Total: Rp {price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat saat ambil
2️⃣ Transfer Bank:
   🏦 {bank_name} - {bank_account}
   👤 A/N: {bank_holder}

Jika sudah transfer, mohon konfirmasi ke admin.

Terima kasih! 🙏
");

// ================================================
// BANK ACCOUNT CONFIGURATION
// ================================================

// Info Rekening Bank (bisa lebih dari satu)
define('WA_BANK_NAME', 'BCA');
define('WA_BANK_ACCOUNT', '1234567890');
define('WA_BANK_HOLDER', "D'four Laundry");

// ================================================
// HELPER FUNCTION
// ================================================

/**
 * Check if WhatsApp is configured properly
 */
function isWhatsAppConfigured() {
    if (!defined('FONNTE_TOKEN') || FONNTE_TOKEN === 'your-api-token-here') {
        return false;
    }
    return defined('WA_ENABLED') && WA_ENABLED === true;
}
?>
```

### 📝 Langkah Setup:

1. Copy `whatsapp.php.template` ke `whatsapp.php`
2. Edit `whatsapp.php` dengan API Token dari Fonnte
3. Pastikan `whatsapp.php` ada di `.gitignore`

---

## Langkah 2: WhatsApp Helper

### 📄 File: `includes/whatsapp-helper.php`

```php
<?php
/**
 * WhatsApp Helper Functions (Fonnte API)
 * 
 * Fungsi-fungsi untuk mengirim pesan WhatsApp via Fonnte API.
 */

// Load WhatsApp config
$waConfigPath = __DIR__ . '/../config/whatsapp.php';
if (file_exists($waConfigPath)) {
    require_once $waConfigPath;
}

/**
 * Send WhatsApp Message via Fonnte API
 * 
 * @param string $phone Phone number (format: 628xxx)
 * @param string $message Message content
 * @return array ['success' => bool, 'message' => string]
 */
function sendWhatsApp($phone, $message) {
    // Check if WhatsApp is enabled
    if (!defined('WA_ENABLED') || !WA_ENABLED) {
        return ['success' => false, 'message' => 'WhatsApp notifications are disabled'];
    }
    
    // Check configuration
    if (!isWhatsAppConfigured()) {
        return ['success' => false, 'message' => 'WhatsApp not configured properly'];
    }
    
    // Format phone number
    $phone = formatPhoneNumber($phone);
    
    // Prepare request
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => FONNTE_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62', // Indonesia
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . FONNTE_TOKEN
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        logWhatsApp($phone, 'failed: ' . $err);
        return ['success' => false, 'message' => 'CURL Error: ' . $err];
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['status']) && $result['status'] === true) {
        logWhatsApp($phone, 'sent');
        return ['success' => true, 'message' => 'WhatsApp sent successfully'];
    } else {
        $errorMsg = $result['reason'] ?? 'Unknown error';
        logWhatsApp($phone, 'failed: ' . $errorMsg);
        return ['success' => false, 'message' => 'Failed: ' . $errorMsg];
    }
}

/**
 * Format phone number to international format
 */
function formatPhoneNumber($phone) {
    // Remove non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert 08xxx to 628xxx
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    // Add 62 if not present
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }
    
    return $phone;
}

/**
 * Send Order Created WhatsApp
 */
function sendOrderCreatedWA($transaction, $customer) {
    if (empty($customer['phone'])) {
        return ['success' => false, 'message' => 'Customer has no phone'];
    }
    
    $message = WA_TEMPLATE_ORDER_CREATED;
    $message = str_replace('{business_name}', WA_BUSINESS_NAME, $message);
    $message = str_replace('{customer_name}', $customer['name'], $message);
    $message = str_replace('{order_id}', $transaction['id'], $message);
    $message = str_replace('{service_type}', $transaction['service_type'], $message);
    $message = str_replace('{price}', number_format($transaction['price'], 0, ',', '.'), $message);
    $message = str_replace('{bank_name}', WA_BANK_NAME, $message);
    $message = str_replace('{bank_account}', WA_BANK_ACCOUNT, $message);
    $message = str_replace('{bank_holder}', WA_BANK_HOLDER, $message);
    
    return sendWhatsApp($customer['phone'], $message);
}

/**
 * Send Status Update WhatsApp
 */
function sendStatusUpdateWA($transaction, $customer, $newStatus) {
    if (empty($customer['phone'])) {
        return ['success' => false, 'message' => 'Customer has no phone'];
    }
    
    $statusLabels = [
        'pending' => 'Menunggu Proses',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai',
        'picked_up' => 'Sudah Diambil'
    ];
    
    $message = WA_TEMPLATE_STATUS_UPDATE;
    $message = str_replace('{business_name}', WA_BUSINESS_NAME, $message);
    $message = str_replace('{customer_name}', $customer['name'], $message);
    $message = str_replace('{order_id}', $transaction['id'], $message);
    $message = str_replace('{status}', $statusLabels[$newStatus] ?? $newStatus, $message);
    
    return sendWhatsApp($customer['phone'], $message);
}

/**
 * Send Ready for Pickup WhatsApp
 */
function sendReadyForPickupWA($transaction, $customer) {
    if (empty($customer['phone'])) {
        return ['success' => false, 'message' => 'Customer has no phone'];
    }
    
    $message = WA_TEMPLATE_READY_PICKUP;
    $message = str_replace('{business_name}', WA_BUSINESS_NAME, $message);
    $message = str_replace('{customer_name}', $customer['name'], $message);
    $message = str_replace('{order_id}', $transaction['id'], $message);
    $message = str_replace('{price}', number_format($transaction['price'], 0, ',', '.'), $message);
    $message = str_replace('{bank_name}', WA_BANK_NAME, $message);
    $message = str_replace('{bank_account}', WA_BANK_ACCOUNT, $message);
    $message = str_replace('{bank_holder}', WA_BANK_HOLDER, $message);
    
    return sendWhatsApp($customer['phone'], $message);
}

/**
 * Send Payment Reminder WhatsApp
 */
function sendPaymentReminderWA($transaction, $customer) {
    if (empty($customer['phone'])) {
        return ['success' => false, 'message' => 'Customer has no phone'];
    }
    
    $message = WA_TEMPLATE_PAYMENT_REMINDER;
    $message = str_replace('{business_name}', WA_BUSINESS_NAME, $message);
    $message = str_replace('{customer_name}', $customer['name'], $message);
    $message = str_replace('{order_id}', $transaction['id'], $message);
    $message = str_replace('{price}', number_format($transaction['price'], 0, ',', '.'), $message);
    $message = str_replace('{bank_name}', WA_BANK_NAME, $message);
    $message = str_replace('{bank_account}', WA_BANK_ACCOUNT, $message);
    $message = str_replace('{bank_holder}', WA_BANK_HOLDER, $message);
    
    return sendWhatsApp($customer['phone'], $message);
}

/**
 * Log WhatsApp activity
 */
function logWhatsApp($phone, $status) {
    $logFile = __DIR__ . '/../logs/whatsapp.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | To: $phone | Status: $status\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>
```

---

## Langkah 3: Update .gitignore

Tambahkan ke `.gitignore`:

```
# WhatsApp config (contains API token)
config/whatsapp.php
```

---

## Langkah 4: Integrasi ke API

### Update `api/transactions-api.php`

Tambahkan di bagian `case 'update_status'`:

```php
// Setelah update status berhasil, kirim WhatsApp
require_once __DIR__ . '/../includes/whatsapp-helper.php';

// Get customer data
$customerStmt = $db->prepare("
    SELECT c.*, u.phone 
    FROM customers c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.id = ?
");
$customerStmt->execute([$transaction['customer_id']]);
$customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

if ($customer && !empty($customer['phone'])) {
    // Send WhatsApp based on new status
    if ($status === 'done') {
        sendReadyForPickupWA($transaction, $customer);
    } else {
        sendStatusUpdateWA($transaction, $customer, $status);
    }
}
```

---

## Testing

### A. Test Konfigurasi

Buat file test sementara:

```php
<?php
// test_whatsapp.php (HAPUS SETELAH TESTING)
require_once 'config/whatsapp.php';
require_once 'includes/whatsapp-helper.php';

echo "WhatsApp Configured: " . (isWhatsAppConfigured() ? 'Yes' : 'No') . "\n";

// Ganti dengan nomor HP Anda
$result = sendWhatsApp(
    '08123456789',
    "Test dari D'four Laundry! 🧺\n\nJika Anda menerima pesan ini, konfigurasi berhasil!"
);

print_r($result);
?>
```

### B. Test via Browser

```
http://localhost/laundry-D-four-system/test_whatsapp.php
```

### C. Cek Log

Log tersimpan di: `logs/whatsapp.log`

---

## Troubleshooting

### ❌ "WhatsApp not configured"

**Solusi:**
1. Pastikan `config/whatsapp.php` sudah dibuat
2. Pastikan `FONNTE_TOKEN` sudah diisi dengan token yang benar
3. Pastikan `WA_ENABLED` = `true`

### ❌ "Device not connected"

**Solusi:**
1. Buka dashboard Fonnte
2. Cek status device, scan ulang QR jika perlu
3. Pastikan WhatsApp tidak logout

### ❌ "Invalid phone number"

**Solusi:**
1. Format nomor: `08xxx` atau `628xxx`
2. Pastikan nomor aktif dan terdaftar WhatsApp

### ❌ "Quota exceeded"

**Solusi:**
1. Cek sisa kuota di dashboard Fonnte
2. Upgrade paket jika perlu
3. Untuk testing, gunakan akun trial

---

## ✅ Checklist Implementasi

- [ ] Daftar akun Fonnte
- [ ] Hubungkan WhatsApp ke Fonnte
- [ ] Dapatkan API Token
- [ ] Buat `config/whatsapp.php.template`
- [ ] Copy ke `config/whatsapp.php` dan isi token
- [ ] Buat `includes/whatsapp-helper.php`
- [ ] Tambahkan `config/whatsapp.php` ke `.gitignore`
- [ ] Update `api/transactions-api.php`
- [ ] Test kirim WhatsApp
- [ ] Cek log di `logs/whatsapp.log`

---

## 💰 Harga Fonnte

| Paket | Kuota/Bulan | Harga |
|-------|-------------|-------|
| Trial | 500 pesan | GRATIS |
| Basic | 3.000 pesan | Rp 99.000 |
| Pro | 10.000 pesan | Rp 199.000 |
| Business | 50.000 pesan | Rp 499.000 |

**Estimasi kebutuhan:**
- 50 transaksi/hari × 2 pesan = 100 pesan/hari
- 100 × 30 = 3.000 pesan/bulan → **Paket Basic cukup!**

---

**Selamat Coding! 📱**
