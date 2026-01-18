# 📊 Sequence Diagram - D'four Smart Laundry System

**Versi**: 1.3  
**Tanggal**: 2026-01-16  
**Status**: Berdasarkan fitur yang sudah ada dan sedang dikembangkan

---

## 📋 Daftar Sequence Diagram

| # | Diagram | Status | Gambar |
|---|---------|--------|--------|
| 1 | Login (Email) | ✅ Implemented | ✅ |
| 2 | Login (Google OAuth) | ✅ Implemented | ✅ |
| 3 | Register Pelanggan | ✅ Implemented | ✅ |
| 4 | **Tambah Data Pelanggan** | ✅ Implemented | ✅ |
| 5 | **Lihat Data Pelanggan** | ✅ Implemented | ✅ |
| 6 | **Edit Data Pelanggan** | ✅ Implemented | ✅ |
| 7 | **Hapus Data Pelanggan** | ✅ Implemented | ✅ |
| 8 | Mencatat Transaksi Baru | ✅ Implemented | ✅ |
| 9 | Update Status Transaksi | ✅ Implemented | ✅ |
| 10 | Cek Status Order (Pelanggan) | ✅ Implemented | ✅ |
| 11 | Mencetak Nota | ✅ Implemented | ✅ |
| 12 | Laporan Bulanan | 🚧 TODO | ✅ |
| 13 | **Pembayaran Online (Midtrans)** | ✅ Implemented | 🚧 |
| 14 | **Pembayaran Manual (QRIS/Transfer)** | ✅ Implemented | 🚧 |

---

## 📖 Keterangan Jenis Boundary (Stereotype)

| Stereotype | Simbol | Deskripsi | Contoh |
|------------|--------|-----------|--------|
| **<<boundary>>** | 🖥️ | Komponen antarmuka pengguna (UI) | Login Page, Dashboard |
| **<<control>>** | ⚙️ | Komponen logika bisnis / controller | auth.php, customers-api.php |
| **<<entity>>** | 📦 | Komponen penyimpanan data | Database, Session |
| **<<external>>** | 🌐 | Sistem eksternal | Google OAuth, Email Service |

---

## 1️⃣ Sequence Diagram: Login (Email)

![Sequence Diagram Login Email](images/sequence_login_email_1768093883970.png)

### Deskripsi Alur:
1. User mengakses halaman login
2. Input email dan password, klik Login
3. Sistem validasi dengan database
4. Jika valid: buat session, redirect ke dashboard
5. Jika invalid: tampilkan pesan error

---

## 2️⃣ Sequence Diagram: Login (Google OAuth)

![Sequence Diagram Login Google](images/sequence_login_google_1768093904760.png)

### Deskripsi Alur:
1. User klik "Login dengan Google"
2. Redirect ke Google OAuth
3. User pilih akun Google
4. Google callback dengan auth code
5. Sistem exchange code untuk token
6. Cek user di database, insert jika baru
7. Buat session, redirect ke dashboard

---

## 3️⃣ Sequence Diagram: Register Pelanggan

![Sequence Diagram Register](images/sequence_register_1768093929354.png)

### Deskripsi Alur:
1. Pelanggan akses halaman register
2. Isi form (nama, email, HP, password)
3. Sistem validasi dan cek email exists
4. Jika email baru: hash password, simpan user
5. Auto-link customer jika phone match
6. Redirect ke halaman login

---

## 4️⃣ Sequence Diagram: Tambah Data Pelanggan

![Sequence Diagram Tambah Pelanggan](images/sequence_tambah_pelanggan.png)

### Deskripsi Alur:
1. Karyawan klik tombol "Tambah Pelanggan"
2. Modal form ditampilkan
3. Karyawan mengisi data dan klik "Simpan"
4. API memvalidasi input
5. API mengecek duplikasi nomor HP
6. Jika valid dan unik, data disimpan ke database
7. UI menampilkan notifikasi dan refresh tabel

---

## 5️⃣ Sequence Diagram: Lihat Data Pelanggan

![Sequence Diagram Lihat Pelanggan](images/sequence_lihat_pelanggan.png)

### Deskripsi Alur:
1. Karyawan mengakses halaman pelanggan
2. Sistem memuat daftar pelanggan dari database
3. Karyawan dapat mencari pelanggan dengan keyword
4. Karyawan dapat melihat detail pelanggan + riwayat transaksi

---

## 6️⃣ Sequence Diagram: Edit Data Pelanggan

![Sequence Diagram Edit Pelanggan](images/sequence_edit_pelanggan.png)

### Deskripsi Alur:
1. Karyawan klik tombol "Edit" pada pelanggan
2. API mengambil data pelanggan
3. Form ditampilkan dengan data terisi
4. Karyawan mengubah data dan klik "Simpan"
5. API memvalidasi, cek keunikan HP jika berubah
6. Data di-update ke database
7. UI menampilkan notifikasi dan refresh tabel

---

## 7️⃣ Sequence Diagram: Hapus Data Pelanggan

![Sequence Diagram Hapus Pelanggan](images/sequence_hapus_pelanggan.png)

### Deskripsi Alur:
1. Karyawan klik tombol "Hapus" pada pelanggan
2. Dialog konfirmasi ditampilkan
3. Jika dikonfirmasi, API mengecek transaksi terkait
4. Jika ada transaksi, penghapusan ditolak
5. Jika tidak ada transaksi, data dihapus
6. UI menampilkan notifikasi dan refresh tabel

---

## 8️⃣ Sequence Diagram: Mencatat Transaksi Baru

![Sequence Diagram Transaksi Baru](images/sequence_transaksi_baru_1768094006272.png)

### Deskripsi Alur:
1. Karyawan klik "Transaksi Baru"
2. Sistem load daftar pelanggan dan layanan
3. Karyawan pilih pelanggan, layanan, input berat
4. Sistem hitung harga otomatis
5. Karyawan simpan transaksi
6. Sistem INSERT dengan status pending

---

## 9️⃣ Sequence Diagram: Update Status Transaksi

![Sequence Diagram Update Status](images/sequence_update_status_1768094028743.png)

### Deskripsi Alur:
1. Karyawan pilih status baru dari dropdown
2. Sistem validasi status
3. Jika valid: UPDATE database, tampilkan badge baru
4. Jika invalid: tampilkan error

### Alur Status Valid:
```
pending → washing → drying → ironing → done → picked_up
```

---

## 🔟 Sequence Diagram: Cek Status Order (Pelanggan)

![Sequence Diagram Cek Status](images/sequence_cek_status_1768094076592.png)

### Deskripsi Alur:
1. Pelanggan akses halaman cek status
2. Sistem cek login, redirect jika belum
3. Ambil phone dari session
4. Query transaksi berdasarkan phone
5. Tampilkan daftar dengan progress bar

---

## 1️⃣1️⃣ Sequence Diagram: Mencetak Nota (TODO)

![Sequence Diagram Cetak Nota](images/sequence_cetak_nota_1768094100382.png)

### Deskripsi Alur:
1. User klik "Cetak Nota" pada transaksi
2. Sistem ambil data transaksi dan pelanggan
3. Generate PDF dengan template nota
4. Karyawan: buka dialog print
5. Pelanggan: download file PDF

---

## 1️⃣2️⃣ Sequence Diagram: Laporan Bulanan (TODO)

![Sequence Diagram Laporan Bulanan](images/sequence_laporan_1768094120850.png)

### Deskripsi Alur:
1. Karyawan akses halaman laporan
2. Pilih periode (bulan, tahun)
3. Sistem query data summary, daily, per service
4. Render charts (Line, Pie, Bar)
5. Tampilkan dashboard laporan
6. Optional: export ke Excel/PDF

---

## 1️⃣3️⃣ Sequence Diagram: Pembayaran Online (Midtrans)

### Deskripsi Alur:

```mermaid
sequenceDiagram
    participant P as Pelanggan
    participant UI as <<boundary>><br/>Payment Page
    participant API as <<control>><br/>create-snap.php
    participant MT as <<external>><br/>Midtrans API
    participant DB as <<entity>><br/>Database
    participant WH as <<control>><br/>notification.php

    P->>UI: Buka halaman pembayaran
    UI->>DB: Query transaksi unpaid
    DB-->>UI: Daftar transaksi
    UI-->>P: Tampilkan transaksi
    
    P->>UI: Klik "Bayar Sekarang"
    UI->>API: POST /api/payment/create-snap.php
    API->>DB: Get transaction details
    DB-->>API: Transaction data
    API->>MT: POST /snap/v1/transactions
    MT-->>API: snap_token
    API-->>UI: { success: true, snap_token }
    
    UI->>P: Tampilkan popup Midtrans
    P->>MT: Pilih metode & bayar
    MT-->>P: Status pembayaran
    
    Note over MT,WH: Webhook Callback (Async)
    MT->>WH: POST notification
    WH->>WH: Verify signature
    WH->>DB: UPDATE payment_status
    DB-->>WH: Success
    WH-->>MT: HTTP 200 OK
```

### Komponen:
| Stereotype | Nama | File |
|------------|------|------|
| <<boundary>> | Payment Page | `pages/payment.php` |
| <<control>> | create-snap.php | `api/payment/create-snap.php` |
| <<control>> | notification.php | `api/payment/notification.php` |
| <<external>> | Midtrans API | `https://api.sandbox.midtrans.com` |
| <<entity>> | Database | `transactions`, `payments` table |

---

## 1️⃣4️⃣ Sequence Diagram: Pembayaran Manual (QRIS/Transfer)

### Deskripsi Alur:

```mermaid
sequenceDiagram
    participant P as Pelanggan
    participant UI as <<boundary>><br/>Payment Page
    participant WA as <<external>><br/>WhatsApp
    participant AD as Admin
    participant DB as <<entity>><br/>Database

    P->>UI: Buka halaman pembayaran
    UI-->>P: Tampilkan QRIS & info rekening bank
    
    alt Bayar via QRIS
        P->>P: Scan QRIS dengan aplikasi e-wallet
        P->>P: Input nominal & bayar
    else Bayar via Transfer Bank
        P->>P: Transfer ke rekening BNI
    end
    
    P->>UI: Klik "Konfirmasi via WhatsApp"
    UI->>WA: Open WhatsApp link dengan pesan template
    P->>AD: Kirim bukti transfer
    
    AD->>DB: UPDATE payment_status = 'paid'
    DB-->>AD: Success
    AD-->>P: Konfirmasi pembayaran diterima
```

### Info Pembayaran Manual:
| Metode | Detail |
|--------|--------|
| QRIS | Gambar QRIS statis di `assets/images/qris.jpg` |
| Bank Transfer | BNI - 2019082060 a.n. MUHAMMAD ROFIQUL ISLAM |
| WhatsApp | 62895337252897 |
| Konfigurasi | `config/payment-info.php` |

---

## 📝 Catatan

1. **Implemented (✅)**: Fitur sudah ada di codebase
2. **TODO (🚧)**: Fitur dalam tahap pengembangan
3. Gambar menggunakan format standar **Sparx Enterprise Architect**

### Referensi File:

| Sequence | File Terkait |
|----------|--------------|
| Login | `pages/auth/login.php`, `includes/auth.php` |
| Register | `pages/auth/register.php` |
| Kelola Pelanggan | `pages/customers.php`, `api/customers-api.php` |
| Transaksi | `pages/transactions.php`, `api/transactions-api.php` |
| Cek Status | `pages/check-order.php` |
| Cetak Nota | `pages/print-receipt.php` (TODO) |
| Laporan | `pages/reports.php` (TODO) |

---

*Dokumen ini berdasarkan analisis codebase D'four Smart Laundry System*
