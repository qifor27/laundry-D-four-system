# 📚 Tutorial: WhatsApp Integration (Manual - Gratis 100%)

**Fitur:** Kirim notifikasi WhatsApp ke pelanggan (Manual Copy-Paste)  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 1-2 jam  
**Last Updated:** 2026-01-10  
**Biaya:** GRATIS 100%  
**Integrasi:** Payment Methods

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Cara Kerja](#2-cara-kerja)
3. [Langkah Implementasi](#3-langkah-implementasi)
4. [Testing](#4-testing)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa Manual Copy-Paste?

| Aspek | Manual | API (Fonnte) |
|-------|--------|--------------|
| **Biaya** | Rp 0 (GRATIS) | Rp 99.000/bulan |
| **Setup** | Tidak perlu daftar | Perlu daftar + token |
| **Kecepatan** | 30 detik/pesan | Otomatis |
| **Risiko Banned** | Tidak ada | Tidak ada |

### 📱 Alur Kerja

```
1. Admin buka halaman transaksi
2. Klik tombol "Kirim WA"
3. Sistem generate pesan + buka WhatsApp Web
4. Admin klik Send di WhatsApp Web
5. Selesai ✅
```

### 📋 Jenis Notifikasi

| Jenis | Trigger | Tombol di Dashboard |
|-------|---------|---------------------|
| **Order Created** | Transaksi baru | "WA: Pesanan Diterima" |
| **Status Update** | Status berubah | "WA: Update Status" |
| **Ready for Pickup** | Status = done | "WA: Siap Diambil" |
| **Payment Reminder** | Belum bayar | "WA: Reminder Bayar" |

---

## 2. Cara Kerja

### A. WhatsApp Web Click-to-Chat

WhatsApp menyediakan URL resmi untuk membuka chat dengan pesan terisi:

```
https://wa.me/628123456789?text=Pesan%20Anda%20Disini
```

**Komponen:**
- `628123456789` = Nomor tujuan (format internasional)
- `text=...` = Pesan yang sudah di-encode URL

### B. Flow di Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                      ADMIN DASHBOARD                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Transaksi #123 - Budi                                          │
│  Status: Done | Bayar: Belum                                    │
│                                                                 │
│  [📱 WA: Siap Diambil]  [📱 WA: Reminder Bayar]                │
│         ↓                        ↓                              │
│  Buka tab baru:            Buka tab baru:                       │
│  WhatsApp Web              WhatsApp Web                         │
│  + Pesan terisi            + Pesan terisi                       │
│         ↓                        ↓                              │
│  Admin klik SEND           Admin klik SEND                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. Langkah Implementasi

### File yang Akan Dibuat

| No | File | Deskripsi |
|----|------|-----------|
| 1 | `includes/whatsapp-helper.php` | Helper functions generate pesan |
| 2 | Update UI transaksi | Tambah tombol WA |

---

### Langkah 1: WhatsApp Helper

### 📄 File: `includes/whatsapp-helper.php`

```php
<?php
/**
 * WhatsApp Helper Functions (Manual - Click to Chat)
 * 
 * Gratis 100% - Menggunakan WhatsApp Web Click-to-Chat
 */

// ================================================
// CONFIGURATION
// ================================================

define('WA_BUSINESS_NAME', "D'four Laundry");
define('WA_BUSINESS_ADDRESS', "Jl. Contoh No. 123");
define('WA_BUSINESS_HOURS', "08.00 - 21.00 WIB");

// Info Rekening Bank
define('WA_BANK_NAME', 'BCA');
define('WA_BANK_ACCOUNT', '1234567890');
define('WA_BANK_HOLDER', "D'four Laundry");

// ================================================
// URL GENERATOR
// ================================================

/**
 * Generate WhatsApp Click-to-Chat URL
 * 
 * @param string $phone Nomor HP (08xxx atau 628xxx)
 * @param string $message Pesan
 * @return string WhatsApp Web URL
 */
function generateWhatsAppURL($phone, $message) {
    $phone = formatPhoneNumber($phone);
    $encodedMessage = urlencode($message);
    return "https://wa.me/{$phone}?text={$encodedMessage}";
}

/**
 * Format phone number to international format (tanpa +)
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

// ================================================
// MESSAGE GENERATORS
// ================================================

/**
 * Generate Order Created Message
 */
function getOrderCreatedMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 👋

Pesanan Anda telah kami terima:
📦 No. Pesanan: #{$transaction['id']}
🧺 Layanan: {$transaction['service_type']}
💰 Total: Rp {$price}

Kami akan segera memproses pesanan Anda.

Terima kasih! 🙏";
}

/**
 * Generate Status Update Message
 */
function getStatusUpdateMessage($transaction, $customer, $status) {
    $statusLabels = [
        'pending' => 'Menunggu Proses',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai',
        'picked_up' => 'Sudah Diambil'
    ];
    
    $statusLabel = $statusLabels[$status] ?? $status;
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}!

Status pesanan Anda telah diupdate:
📦 No. Pesanan: #{$transaction['id']}
📊 Status: *{$statusLabel}*

Terima kasih telah menunggu! 🙏";
}

/**
 * Generate Ready for Pickup Message (dengan opsi pembayaran)
 */
function getReadyForPickupMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 🎉

Kabar baik! Pesanan Anda sudah selesai:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat (Cash)
2️⃣ Transfer Bank:
   🏦 " . WA_BANK_NAME . " - " . WA_BANK_ACCOUNT . "
   👤 A/N: " . WA_BANK_HOLDER . "

📍 Lokasi: " . WA_BUSINESS_ADDRESS . "
🕐 Jam: " . WA_BUSINESS_HOURS . "

Ditunggu kedatangannya! 😊";
}

/**
 * Generate Payment Reminder Message
 */
function getPaymentReminderMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 👋

Kami ingin mengingatkan bahwa pesanan Anda belum dibayar:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat saat ambil
2️⃣ Transfer Bank:
   🏦 " . WA_BANK_NAME . " - " . WA_BANK_ACCOUNT . "
   👤 A/N: " . WA_BANK_HOLDER . "

Jika sudah transfer, mohon konfirmasi ke admin.

Terima kasih! 🙏";
}

// ================================================
// COMPLETE URL GENERATORS (untuk tombol)
// ================================================

/**
 * Get WhatsApp URL for Order Created
 */
function getOrderCreatedWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getOrderCreatedMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Status Update
 */
function getStatusUpdateWAUrl($transaction, $customer, $status) {
    if (empty($customer['phone'])) return null;
    $message = getStatusUpdateMessage($transaction, $customer, $status);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Ready Pickup
 */
function getReadyForPickupWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getReadyForPickupMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Payment Reminder
 */
function getPaymentReminderWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getPaymentReminderMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}
?>
```

---

### Langkah 2: Update UI Transaksi

Tambahkan tombol WhatsApp di halaman transaksi admin.

### Contoh Penggunaan di PHP:

```php
<?php
require_once 'includes/whatsapp-helper.php';

// Data transaksi dan customer
$transaction = [
    'id' => 123,
    'service_type' => 'Cuci Setrika',
    'price' => 50000,
    'status' => 'done',
    'payment_status' => 'unpaid'
];

$customer = [
    'name' => 'Budi',
    'phone' => '08123456789'
];

// Generate URL
$waReadyUrl = getReadyForPickupWAUrl($transaction, $customer);
$waReminderUrl = getPaymentReminderWAUrl($transaction, $customer);
?>

<!-- Tombol WhatsApp -->
<div class="flex gap-2">
    <?php if ($waReadyUrl): ?>
    <a href="<?= $waReadyUrl ?>" 
       target="_blank"
       class="btn btn-success">
        📱 WA: Siap Diambil
    </a>
    <?php endif; ?>
    
    <?php if ($transaction['payment_status'] === 'unpaid' && $waReminderUrl): ?>
    <a href="<?= $waReminderUrl ?>" 
       target="_blank"
       class="btn btn-warning">
        📱 WA: Reminder Bayar
    </a>
    <?php endif; ?>
</div>
```

### Contoh Penggunaan di JavaScript:

```javascript
// Generate WA URL di frontend
function generateWAUrl(phone, message) {
    // Format phone
    phone = phone.replace(/\D/g, '');
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }
    
    // Encode message
    const encodedMessage = encodeURIComponent(message);
    
    return `https://wa.me/${phone}?text=${encodedMessage}`;
}

// Contoh: Tombol Kirim WA
document.getElementById('btnWA').addEventListener('click', function() {
    const phone = '08123456789';
    const message = `*D'four Laundry*\n\nHalo Budi!\nPesanan #123 siap diambil.`;
    
    const waUrl = generateWAUrl(phone, message);
    window.open(waUrl, '_blank');
});
```

---

### Langkah 3: Integrasi dengan Payment Methods

File `whatsapp-helper.php` sudah terintegrasi dengan info pembayaran:

| Konfigurasi | Lokasi | Digunakan di |
|-------------|--------|--------------|
| `WA_BANK_NAME` | whatsapp-helper.php | Ready Pickup, Payment Reminder |
| `WA_BANK_ACCOUNT` | whatsapp-helper.php | Ready Pickup, Payment Reminder |
| `WA_BANK_HOLDER` | whatsapp-helper.php | Ready Pickup, Payment Reminder |

**Pastikan update nilai ini sesuai rekening Anda!**

---

## 4. Testing

### A. Test Generate URL

```php
<?php
// test_whatsapp.php
require_once 'includes/whatsapp-helper.php';

$transaction = [
    'id' => 123,
    'service_type' => 'Cuci Setrika',
    'price' => 50000
];

$customer = [
    'name' => 'Test',
    'phone' => '08123456789' // Ganti dengan nomor Anda
];

$url = getReadyForPickupWAUrl($transaction, $customer);

echo "URL: " . $url . "\n\n";
echo "Klik link di bawah untuk test:\n";
echo $url;
?>
```

### B. Test di Browser

1. Buka `http://localhost/laundry-D-four-system/test_whatsapp.php`
2. Klik link yang muncul
3. WhatsApp Web akan terbuka dengan pesan terisi
4. Klik Send!

---

## ✅ Checklist Implementasi

- [ ] Buat `includes/whatsapp-helper.php`
- [ ] Update config (nama bank, no rekening)
- [ ] Tambah tombol WA di halaman transaksi
- [ ] Test generate URL
- [ ] Test buka WhatsApp Web
- [ ] Test kirim pesan

---

## 💡 Tips

### Keyboard Shortcut

Setelah WhatsApp Web terbuka:
- **Enter** = Kirim pesan
- **Ctrl + Enter** = New line

### Multiple Messages

Untuk kirim ke banyak customer:
1. Buka semua URL di tab baru
2. Klik Send satu per satu
3. Atau gunakan keyboard shortcut

---

## 📊 Perbandingan dengan API

| Aspek | Manual (Tutorial Ini) | API (Fonnte) |
|-------|----------------------|--------------|
| Biaya | **Rp 0** | Rp 99.000/bulan |
| Setup | 30 menit | 1-2 jam |
| Effort per pesan | 1 klik + Enter | Otomatis |
| Cocok untuk | < 50 pesan/hari | > 50 pesan/hari |
| Risiko banned | Tidak ada | Tidak ada |

---

**Selamat Coding! 📱 GRATIS 100%!**
