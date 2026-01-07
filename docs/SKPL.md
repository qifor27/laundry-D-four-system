# SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK 
## (Software Requirement Specification)

# D'four Smart Laundry System
**Aplikasi Berbasis Web untuk Pengelolaan Sistem di Laundry D'four dan memudahkan pelanggan dalam mengakses informasi tentang laundry D'four**

---

## 1. Pendahuluan (Introduction)

### 1.1 Tujuan SKPL (SRS Purpose)

Dokumen Spesifikasi Kebutuhan Perangkat Lunak (SKPL) ini disusun untuk menjelaskan kebutuhan dari sistem **D'four Smart Laundry System** yang akan diterapkan pada Laundry D'four. Kebutuhan yang dijelaskan meliputi kebutuhan tingkat tinggi (high-level) yang berfokus pada tujuan bisnis, serta kebutuhan tingkat rendah (low-level) yang berkaitan dengan fungsi sistem.

Dokumen ini digunakan sebagai pedoman bagi pihak manajemen, analis sistem, pengembang, serta penguji dalam memahami kebutuhan sistem, sehingga perangkat lunak yang dikembangkan dapat sesuai dengan kebutuhan operasional dan tujuan bisnis Laundry D'four.

---

### 1.2 Ruang Lingkup Produk (Product Scope)

**Permasalahan Utama yang Diatasi:**
1. Sistem digital yang dapat mencatat cucian masuk
2. Mencatat berat pakaian
3. Menampilkan status cucian (pending, washing, drying, ironing, done, picked_up)
4. Dapat memberikan informasi kepada pelanggan
5. Membuat laporan harian/bulanan

**Solusi:** Sebuah sistem informasi berbasis web yang mengintegrasikan seluruh pemrosesan pesanan pada Laundry D'four.

**Ruang Lingkup Sistem (Scope):**
- ✅ Pemrosesan pesanan pelanggan
- ✅ Pencatatan berat pakaian pelanggan  
- ✅ Informasi kepada pelanggan (cek status via nomor telepon)
- ✅ Dashboard statistik (penjualan harian, aktif, total pelanggan, pendapatan bulanan)

---

### 1.3 Audiens yang Ditargetkan (Intended Audience)

- Pemilik bisnis dan manajemen (Business owners and management)
- Mahasiswa dan dosen pembimbing (untuk konteks akademik)
- Dosen Penguji

---

### 1.4 Definisi, Akronim, dan Singkatan

| Istilah | Definisi |
|---------|----------|
| Cuci Setrika | Jenis layanan cuci dan langsung setrika |
| Cuci Kering | Jenis layanan cuci tanpa setrika |
| CRUD | Create, Read, Update, Delete |
| API | Application Programming Interface |
| REST | Representational State Transfer |

---

### 1.5 Konvensi Dokumen (Document Conventions)

- **"Harus" (Shall)** → kebutuhan wajib (mandatory requirement)
- **"Sebaiknya" (Should)** → kebutuhan yang direkomendasikan
- **"Dapat" (May)** → kebutuhan opsional

---

### 1.6 Referensi dan Ucapan Terima Kasih

- IEEE 830 Software Requirements Specification
- IEEE 29148-2018
- Schneider & Winters – Applying Use Cases

---

## 2. Deskripsi Umum (Overall Description)

### 2.1 Perspektif Produk (Product Perspective)

D'four Smart Laundry System merupakan **aplikasi berbasis web** yang dirancang untuk mengelola operasional bisnis laundry secara digital.

#### Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                    BROWSER / CLIENT                              │
│         HTML5 + Tailwind CSS + JavaScript                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────────┐
│                      PHP SERVER                                  │
├─────────────────────────────────────────────────────────────────┤
│  📄 Landing Page (index.php)                                    │
│  📊 Dashboard (pages/dashboard.php)                             │
│  👥 Customers (pages/customers.php)                             │
│  📝 Transactions (pages/transactions.php)                       │
│  🔍 Check Order (pages/check-order.php)                         │
├─────────────────────────────────────────────────────────────────┤
│  🔌 REST API Endpoints                                          │
│     - /api/customers-api.php                                    │
│     - /api/transactions-api.php                                 │
├─────────────────────────────────────────────────────────────────┤
│  📦 Includes (header, sidebar, functions)                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────────┐
│                      DATABASE                                    │
│                MySQL / SQLite                                    │
│  Tables: customers, transactions, service_types                  │
└─────────────────────────────────────────────────────────────────┘
```

#### Karakteristik Sistem

| Karakteristik | Deskripsi |
|---------------|-----------|
| **Aplikasi Web Mandiri** | Berjalan pada server lokal (XAMPP) atau cloud hosting |
| **Portal Publik** | Halaman cek status pesanan via nomor telepon |
| **Panel Administrasi** | Dashboard lengkap dengan navigasi sidebar |
| **REST API** | Endpoint untuk CRUD pelanggan dan transaksi |
| **Teknologi** | PHP Native + MySQL + Tailwind CSS |

---

### 2.2 Fungsionalitas Produk (Product Functionality)

#### ✅ Fitur yang Sudah Diimplementasi

| No | Fitur | Status | File |
|----|-------|--------|------|
| 1 | Dashboard statistik real-time | ✅ Aktif | `pages/dashboard.php` |
| 2 | Manajemen data pelanggan (CRUD) | ✅ Aktif | `pages/customers.php` |
| 3 | Manajemen transaksi (CRUD) | ✅ Aktif | `pages/transactions.php` |
| 4 | Cek status pesanan (publik) | ✅ Aktif | `pages/check-order.php` |
| 5 | Master jenis layanan | ✅ Aktif | Database `service_types` |
| 6 | REST API | ✅ Aktif | `api/*.php` |
| 7 | Landing page | ✅ Aktif | `index.php` |

#### 🚀 Fitur yang Direkomendasikan untuk Pengembangan

| No | Fitur | Prioritas | Deskripsi |
|----|-------|-----------|-----------|
| 1 | **Login Multi-Role** | Tinggi | Sistem autentikasi untuk Owner/Admin/Staff |
| 2 | **Notifikasi WhatsApp** | Tinggi | Kirim notifikasi otomatis ke pelanggan |
| 3 | **Laporan PDF/Excel** | Tinggi | Export laporan transaksi per periode |
| 4 | **Manajemen User** | Sedang | CRUD user dengan role management |
| 5 | **Cetak Struk** | Sedang | Print thermal/PDF untuk struk pesanan |
| 6 | **Riwayat Pelanggan** | Sedang | Melihat semua transaksi per pelanggan |
| 7 | **Estimasi Waktu** | Rendah | Perkiraan waktu selesai pesanan |
| 8 | **Promo/Diskon** | Rendah | Sistem kupon dan diskon |
| 9 | **Integrasi Timbangan** | Rendah | Input berat otomatis dari hardware |

---

### 2.3 Pengguna dan Karakteristik (Users and Characteristics)

| Pengguna | Karakteristik | Tanggung Jawab |
|----------|---------------|----------------|
| **Pemilik (Owner)** | Pengambil keputusan | Memantau laporan transaksi dan kinerja |
| **Karyawan (Staff)** | Fokus teknis | Memperbarui status cucian via sistem |
| **Pelanggan (Customer)** | Menginginkan kepastian | Cek status pesanan via halaman publik |

---

### 2.4 Lingkungan Operasi (Operating Environment)

| Komponen | Spesifikasi |
|----------|-------------|
| **Browser** | Chrome, Firefox, Edge (versi terbaru) |
| **Server** | Windows/Linux dengan PHP 7.4+ |
| **Database** | MySQL 5.7+ atau SQLite 3 |
| **Web Server** | Apache (XAMPP) atau Nginx |

---

### 2.5 Batasan Desain dan Implementasi

**Yang Termasuk:**
- ✅ Pemrosesan pesanan
- ✅ Pencatatan berat/jumlah pakaian
- ✅ Informasi pelanggan
- ✅ Dashboard statistik

**Yang Tidak Termasuk (Saat Ini):**
- ❌ Inventaris bahan
- ❌ Keuangan kompleks
- ❌ Manajemen SDM
- ❌ Pembayaran online

---

### 2.6 Dokumentasi Pengguna (User Documentation)

- 📖 `README.md` - Panduan instalasi dan penggunaan
- 📖 `QUICKSTART.md` - Panduan cepat
- 📖 `docs/learning_by_doing.md` - Dokumentasi pembelajaran
- 📖 `docs/architecture_diagram.png` - Diagram arsitektur

---

### 2.7 Asumsi dan Ketergantungan

- Pengguna memiliki kemampuan dasar komputer
- Jaringan stabil saat mengakses sistem
- Karyawan konsisten mengupdate status cucian
- Pelanggan memiliki smartphone dan nomor telepon aktif

---

## 3. Kebutuhan Spesifik (Specific Requirements)

### 3.1 Kebutuhan Antarmuka Eksternal

#### Antarmuka Pengguna (User Interface)

| Komponen | Implementasi |
|----------|--------------|
| Framework CSS | Tailwind CSS v3.x |
| Font | Outfit (Google Fonts) |
| Design | Gradient purple, glassmorphism, responsive |
| Komponen | Cards, Tables, Modals, Forms, Badges |

#### Antarmuka Sistem (System Interface)

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/customers-api.php` | GET, POST, PUT, DELETE | CRUD Pelanggan |
| `/api/transactions-api.php` | GET, POST, PUT, DELETE | CRUD Transaksi |

---

### 3.2 Kebutuhan Fungsional (Functional Requirements)

#### FR-01: Pengelolaan Data Pelanggan

| ID | Requirement | Status |
|----|-------------|--------|
| FR-01a | Sistem harus memungkinkan CRUD data pelanggan | ✅ |
| FR-01b | Data: id, name, phone (unik), address, created_at | ✅ |
| FR-01c | Validasi nomor telepon unik | ✅ |
| FR-01d | Fitur pencarian pelanggan | ✅ |

#### FR-02: Pengelolaan Jenis Layanan

| ID | Requirement | Status |
|----|-------------|--------|
| FR-02a | Data jenis layanan tersimpan di database | ✅ |
| FR-02b | Atribut: name, unit (kg/pcs), price_per_unit, description | ✅ |
| FR-02c | Status aktif/non-aktif | ✅ |

**Jenis Layanan Default:**
- Cuci Kering (Rp 5.000/kg)
- Cuci Setrika (Rp 7.000/kg)
- Setrika Saja (Rp 4.000/kg)
- Bed Cover Single (Rp 15.000/pcs)
- Bed Cover Double (Rp 25.000/pcs)
- Selimut (Rp 20.000/pcs)
- Boneka Kecil/Besar (Rp 10.000-25.000/pcs)

#### FR-03: Pemrosesan Transaksi

| ID | Requirement | Status |
|----|-------------|--------|
| FR-03a | Membuat transaksi dengan pilih pelanggan dari database | ✅ |
| FR-03b | Input berat (kg) atau jumlah (pcs) sesuai unit layanan | ✅ |
| FR-03c | Hitung total harga otomatis | ✅ |
| FR-03d | Status default: pending | ✅ |

#### FR-04: Status Pengerjaan Cucian

| Status | Kode | Warna Badge |
|--------|------|-------------|
| Menunggu | `pending` | Kuning |
| Dicuci | `washing` | Biru |
| Dikeringkan | `drying` | Cyan |
| Disetrika | `ironing` | Ungu |
| Selesai | `done` | Hijau |
| Diambil | `picked_up` | Abu-abu |

#### FR-05: Pelanggan Cek Status Pesanan

| ID | Requirement | Status |
|----|-------------|--------|
| FR-05a | Halaman publik (tanpa login) | ✅ |
| FR-05b | Pencarian via nomor telepon | ✅ |
| FR-05c | Tampilkan: ID, nama, layanan, berat, harga, status, tanggal | ✅ |

#### FR-06: Dashboard Statistik

| ID | Requirement | Status |
|----|-------------|--------|
| FR-06a | Total penjualan hari ini | ✅ |
| FR-06b | Pesanan aktif (belum selesai) | ✅ |
| FR-06c | Total pelanggan | ✅ |
| FR-06d | Pendapatan bulan ini | ✅ |
| FR-06e | Tabel transaksi terbaru | ✅ |

---

### 3.3 Kebutuhan Perilaku (Behavior Requirements)

#### Use Case 1: Staff Membuat Transaksi Baru

```
1. Staff membuka halaman "Transaksi"
2. Staff klik tombol "Transaksi Baru"
3. Staff memilih pelanggan dari dropdown
4. Staff memilih jenis layanan
5. Sistem menampilkan field sesuai unit (kg/pcs)
6. Staff memasukkan berat/jumlah
7. Sistem menghitung total harga otomatis
8. Staff menambahkan catatan (opsional)
9. Staff klik "Simpan"
10. Sistem menyimpan transaksi dengan status "pending"
```

#### Use Case 2: Staff Memperbarui Status Cucian

```
1. Staff membuka halaman "Transaksi"
2. Staff mencari transaksi yang akan diupdate
3. Staff klik dropdown status pada baris transaksi
4. Staff pilih status baru
5. Sistem memperbarui status dan updated_at
6. Halaman refresh menampilkan status terbaru
```

#### Use Case 3: Pelanggan Mengecek Status Pesanan

```
1. Pelanggan membuka halaman utama (landing page)
2. Pelanggan klik "Cek Status Order"
3. Pelanggan memasukkan nomor telepon
4. Pelanggan klik "Cari"
5. Sistem menampilkan daftar pesanan pelanggan
6. Sistem menampilkan detail: status, layanan, harga
```

---

## 4. Kebutuhan Nonfungsional

### 4.1 Kebutuhan Kinerja (Performance)

| Requirement | Target |
|-------------|--------|
| Proses transaksi baru | ≤ 3 detik |
| Pencarian pelanggan/transaksi | ≤ 2 detik |
| Update status | ≤ 2 detik |

### 4.2 Kebutuhan Keamanan (Security)

| Requirement | Status |
|-------------|--------|
| Halaman admin dilindungi login | 🚀 Rekomendasi |
| Password terenkripsi | 🚀 Rekomendasi |
| Halaman cek status publik | ✅ Aktif |

### 4.3 Atribut Kualitas

| Atribut | Target |
|---------|--------|
| **Keandalan** | Uptime ≥ 99% |
| **Ketersediaan** | Akses selama jam operasional |
| **Maintainability** | Kode modular |
| **Adaptabilitas** | Siap dikembangkan |

---

## 5. Kebutuhan Data (Data Dictionary)

### 5.1 Customer Entity (Tabel pelanggan)

| Field | Tipe Data | Mandatory | Validasi |
|-------|-----------|-----------|----------|
| id | INTEGER | Yes | Auto-increment, Primary Key |
| name | TEXT | Yes | Minimal 3 karakter |
| phone | TEXT | Yes | Unik, 10-15 digit |
| address | TEXT | No | - |
| created_at | DATETIME | Yes | Auto-timestamp |

### 5.2 Service Type Entity (Tabel Jenis Layanan)

| Field | Tipe Data | Mandatory | Validasi |
|-------|-----------|-----------|----------|
| id | INTEGER | Yes | Auto-increment |
| name | TEXT | Yes | Unik |
| unit | TEXT | Yes | 'kg' atau 'pcs' |
| price_per_unit | DECIMAL(10,2) | Yes | ≥ 0 |
| description | TEXT | No | - |
| is_active | INTEGER | Yes | Default: 1 |
| created_at | DATETIME | Yes | Auto-timestamp |

### 5.3 Transaction Entity (Tabel Transaksi)

| Field | Tipe Data | Mandatory | Validasi |
|-------|-----------|-----------|----------|
| id | INTEGER | Yes | Auto-increment |
| customer_id | INTEGER | Yes | FK → customers.id |
| service_type | TEXT | Yes | Nama layanan |
| weight | REAL | Conditional | Jika unit='kg' |
| quantity | INTEGER | Conditional | Jika unit='pcs' |
| price | DECIMAL(10,2) | Yes | Auto-calculated |
| status | TEXT | Yes | Enum status |
| notes | TEXT | No | Catatan opsional |
| created_at | DATETIME | Yes | Auto-timestamp |
| updated_at | DATETIME | Yes | Auto-update |

### 5.4 User Entity (🚀 Rekomendasi untuk Pengembangan)

| Field | Tipe Data | Mandatory | Validasi |
|-------|-----------|-----------|----------|
| id | INTEGER | Yes | Auto-increment |
| username | VARCHAR(50) | Yes | Unik |
| password_hash | VARCHAR(255) | Yes | Hashed |
| role | ENUM | Yes | owner/admin/staff |
| is_active | BOOLEAN | Yes | Default: true |
| last_login | DATETIME | No | Auto-updated |

---

## 6. Rencana Pengembangan Masa Depan

### Phase 1: Keamanan & Autentikasi (Prioritas Tinggi)
- [ ] Sistem login multi-role (Owner, Admin, Staff)
- [ ] Proteksi halaman admin
- [ ] Session management
- [ ] Logout functionality

### Phase 2: Notifikasi (Prioritas Tinggi)  
- [ ] Integrasi WhatsApp API (wa.me atau Fonnte)
- [ ] Template pesan status
- [ ] Notifikasi otomatis saat status berubah

### Phase 3: Laporan (Prioritas Tinggi)
- [ ] Export laporan PDF
- [ ] Export laporan Excel/CSV
- [ ] Filter berdasarkan periode
- [ ] Grafik statistik

### Phase 4: Fitur Tambahan (Prioritas Sedang)
- [ ] Cetak struk/nota
- [ ] Riwayat transaksi per pelanggan
- [ ] Manajemen jenis layanan via UI
- [ ] Photo upload untuk bukti selesai

### Phase 5: Optimasi (Prioritas Rendah)
- [ ] Estimasi waktu pengerjaan
- [ ] Sistem promo/diskon
- [ ] Integrasi timbangan digital
- [ ] PWA (Progressive Web App)

---

## 7. Diagram Entity Relationship

```
┌────────────────┐         ┌────────────────┐
│   customers    │         │  service_types │
├────────────────┤         ├────────────────┤
│ id (PK)        │         │ id (PK)        │
│ name           │         │ name           │
│ phone (UNIQUE) │         │ unit           │
│ address        │         │ price_per_unit │
│ created_at     │         │ description    │
└───────┬────────┘         │ is_active      │
        │                  │ created_at     │
        │ 1                └────────────────┘
        │
        │ M
┌───────▼────────┐
│  transactions  │
├────────────────┤
│ id (PK)        │
│ customer_id (FK)│
│ service_type   │
│ weight         │
│ quantity       │
│ price          │
│ status         │
│ notes          │
│ created_at     │
│ updated_at     │
└────────────────┘
```

---

**Dokumen ini terakhir diperbarui:** 27 Desember 2024

**Versi:** 1.0

**Disusun oleh:** Tim Pengembangan D'four Smart Laundry System
