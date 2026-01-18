# 📖 Panduan Implementasi Payment Gateway Midtrans

## ✅ FASE 1: Konfigurasi (SELESAI)

File yang sudah dibuat:
- `config/midtrans.php` - Konfigurasi Midtrans API
- `config/payment-info.php` - Info QRIS dan Rekening
- `assets/images/qris.jpg` - Gambar QRIS

### Yang Perlu Anda Edit:

#### 1. config/midtrans.php
```php
// Ganti dengan key dari dashboard Midtrans Anda
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-XXXXXXX'); // dari dashboard
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-XXXXXXX'); // dari dashboard
```

**Cara Dapat Key:**
1. Buka https://dashboard.sandbox.midtrans.com/
2. Daftar/Login
3. Pergi ke **Settings > Access Keys**
4. Copy Server Key dan Client Key

#### 2. config/payment-info.php
```php
// Ganti dengan data rekening bank Anda
$PAYMENT_BANK_ACCOUNTS = [
    [
        'bank' => 'BCA',
        'account_number' => 'NOMOR_REKENING_ANDA',
        'account_name' => 'NAMA_PEMILIK_REKENING',
    ],
];

// Ganti dengan nomor WhatsApp Anda
define('PAYMENT_WHATSAPP_NUMBER', '62895337252897');
```

---

## ⏳ FASE 2: API Payment

### File yang perlu dibuat:

#### 1. api/payment/create-snap.php
Fungsi: Membuat token pembayaran Midtrans

#### 2. api/payment/notification.php
Fungsi: Menerima notifikasi dari Midtrans (webhook)

#### 3. api/payment/check-status.php
Fungsi: Cek status pembayaran

**Ketik "lanjut fase 2" untuk melanjutkan**

---

## ⏳ FASE 3: Halaman Pembayaran User

### File yang perlu dibuat:
- `pages/payment.php` - Halaman pembayaran

### File yang perlu dimodifikasi:
- `pages/customer-dashboard.php` - Tambah tombol bayar

---

## ⏳ FASE 4: Nota/Invoice

### File yang perlu dibuat:
- `pages/invoice.php` - Halaman nota
- `includes/invoice-template.php` - Template nota

---

## ⏳ FASE 5: Pengingat WhatsApp

### File yang perlu dimodifikasi:
- `api/transactions-api.php` - Tambah info pembayaran di reminder

---

## 📞 Catatan Penting

1. **Sandbox vs Production**
   - Gunakan Sandbox untuk testing (gratis)
   - Ganti ke Production setelah live

2. **Testing Pembayaran**
   - Di Sandbox, gunakan kartu test: 4811 1111 1111 1114
   - CVV: 123, Exp: bulan/tahun apapun di masa depan

3. **Webhook URL**
   - Setelah hosting, set URL notifikasi di dashboard Midtrans
   - URL: `https://yourdomain.com/api/payment/notification.php`
