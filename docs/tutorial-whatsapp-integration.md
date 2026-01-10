# 📚 Tutorial: WhatsApp Integration (Manual - Gratis 100%)

**Fitur:** Kirim notifikasi WhatsApp ke pelanggan  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 1-2 jam  
**Biaya:** GRATIS (Manual Click-to-Chat)  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Cara Kerja](#2-cara-kerja)
3. [Implementasi](#3-implementasi)
4. [Testing](#4-testing)

---

## 1. Penjelasan Fitur

### Mengapa Manual (Gratis)?

| Aspek | Manual | API (Fonnte) |
|-------|--------|--------------|
| **Biaya** | Rp 0 | Rp 99.000/bulan |
| **Setup** | Tidak perlu | Perlu daftar |
| **Effort** | 1 klik + Enter | Otomatis |

### Jenis Notifikasi

| Jenis | Trigger |
|-------|---------|
| Order Created | Transaksi baru |
| Status Update | Status berubah |
| Ready for Pickup | Status = done |
| Payment Reminder | Belum bayar |

---

## 2. Cara Kerja

### WhatsApp Web Click-to-Chat

```
https://wa.me/628123456789?text=Pesan%20Anda
```

### Alur:

```
1. Admin klik tombol WA
2. Browser buka WhatsApp Web
3. Pesan sudah terisi otomatis
4. Admin klik Send
5. Selesai!
```

---

## 3. Implementasi

### File: `includes/whatsapp-helper.php`

```php
<?php
// Configuration
define('WA_BUSINESS_NAME', "D'four Laundry");
define('WA_BUSINESS_ADDRESS', "Jl. Contoh No. 123");
define('WA_BUSINESS_HOURS', "08.00 - 21.00 WIB");

// Generate URL
function generateWhatsAppURL($phone, $message) {
    $phone = formatPhoneNumber($phone);
    return "https://wa.me/{$phone}?text=" . urlencode($message);
}

// Format phone: 08xxx → 628xxx
function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    return $phone;
}

// Get bank list from database (dinamis!)
function getBankListForWA() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM payment_methods WHERE type='bank_transfer' AND is_active=1");
    $banks = $stmt->fetchAll();
    
    $text = "";
    foreach ($banks as $bank) {
        $text .= "\n   🏦 {$bank['bank_name']} - {$bank['account_number']}";
        $text .= "\n      A/N: {$bank['account_holder']}";
    }
    return $text;
}

// URL Generators
function getOrderCreatedWAUrl($transaction, $customer) { ... }
function getStatusUpdateWAUrl($transaction, $customer, $status) { ... }
function getReadyForPickupWAUrl($transaction, $customer) { ... }
function getPaymentReminderWAUrl($transaction, $customer) { ... }
?>
```

### Penggunaan di UI:

```php
<?php
require_once 'includes/whatsapp-helper.php';

$waUrl = getReadyForPickupWAUrl($transaction, $customer);
?>

<a href="<?= $waUrl ?>" target="_blank" class="btn">
    📱 WA: Siap Diambil
</a>
```

---

## 4. Testing

1. Buka: `http://localhost/laundry-D-four-system/pages/transaction-detail.php?id=1`
2. Klik tombol WhatsApp
3. WhatsApp Web terbuka dengan pesan terisi
4. Klik Send

---

## ✅ Checklist

- [ ] Buat `includes/whatsapp-helper.php`
- [ ] Update `pages/transaction-detail.php`
- [ ] Test kirim WhatsApp

---

**Selamat Coding! 📱**
