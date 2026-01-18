# MATERI SLIDE PPT - D'FOUR SMART LAUNDRY SYSTEM
## Sidang Project Based Learning

---

## SLIDE 1: COVER

**D'FOUR SMART LAUNDRY SYSTEM**

*Sistem Manajemen Laundry Modern Berbasis Web*

**Anggota Kelompok:**
1. Rofiq
2. Reyhan
3. Fikri
4. Reza
5. Ica

Tahun 2026

---

## SLIDE 2: LATAR BELAKANG

### Masalah yang Dihadapi:
- Pengelolaan laundry masih manual (buku catatan)
- Sulit melacak status cucian pelanggan
- Tidak ada laporan penjualan yang terstruktur
- Pelanggan tidak bisa cek status pesanan secara mandiri
- Kesalahan pencatatan dan kehilangan nota sering terjadi

### Solusi:
Membangun **Sistem Informasi Manajemen Laundry** berbasis web yang dapat diakses kapan saja dan dimana saja.

---

## SLIDE 3: TUJUAN PROJECT

1. **Mempermudah** pengelolaan data pelanggan dan transaksi
2. **Menyediakan** fitur tracking status cucian secara real-time
3. **Menghasilkan** laporan penjualan otomatis
4. **Mengintegrasikan** pembayaran online (QRIS, Transfer Bank)
5. **Memberikan** akses dashboard untuk pelanggan terdaftar

---

## SLIDE 4: DESKRIPSI SINGKAT

**D'Four Smart Laundry System** adalah aplikasi web untuk mengelola bisnis laundry secara digital.

### Fitur Utama:
| Fitur | Keterangan |
|-------|------------|
| Dashboard Admin | Statistik penjualan & monitoring |
| Manajemen Pelanggan | CRUD data pelanggan |
| Manajemen Transaksi | Input order & update status |
| Customer Portal | Login & tracking pesanan |
| Pembayaran Online | Midtrans (QRIS, VA Bank) |
| Laporan Bulanan | Export CSV & Print PDF |

---

## SLIDE 5: TECH STACK

### Backend
| Teknologi | Fungsi |
|-----------|--------|
| **PHP 7.4+** | Server-side scripting |
| **MySQL 5.7+** | Database management |
| **PDO** | Database abstraction layer |

### Frontend
| Teknologi | Fungsi |
|-----------|--------|
| **HTML5** | Struktur halaman |
| **Tailwind CSS** | Styling & responsive design |
| **JavaScript** | Interaktivitas & AJAX |

### Tools & Services
| Teknologi | Fungsi |
|-----------|--------|
| **Midtrans** | Payment gateway |
| **Google OAuth** | Login dengan Google |
| **XAMPP** | Local development server |

---

## SLIDE 6: ARSITEKTUR SISTEM

```
┌──────────────────────────────────────────────────────────────┐
│                        BROWSER (Client)                       │
│         HTML + Tailwind CSS + JavaScript                      │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                      WEB SERVER (Apache)                      │
│                    PHP Native + PDO                           │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                      DATABASE (MySQL)                         │
│      users | customers | transactions | payments              │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                  EXTERNAL SERVICES                            │
│        Midtrans API | Google OAuth | SMTP Email               │
└──────────────────────────────────────────────────────────────┘
```

---

## SLIDE 7: DATABASE DESIGN (ERD)

### Tabel Utama:
| Tabel | Keterangan | Relasi |
|-------|------------|--------|
| **users** | Data akun pengguna | 1:1 dengan customers |
| **customers** | Data pelanggan laundry | 1:N dengan transactions |
| **transactions** | Data transaksi | 1:N dengan payments |
| **payments** | Data pembayaran | N:1 dengan payment_methods |
| **payment_methods** | Metode pembayaran | - |
| **service_types** | Jenis layanan | - |

### Relasi:
- 1 User bisa punya 1 Customer
- 1 Customer bisa punya banyak Transactions
- 1 Transaction bisa punya banyak Payments

---

## SLIDE 8: DEMO & KESIMPULAN

### Demo Aplikasi:
1. Landing Page → Login → Dashboard
2. Tambah Pelanggan → Buat Transaksi
3. Update Status → Pembayaran Online
4. Customer Portal → Cek Status Pesanan
5. Laporan Bulanan → Export

### Kesimpulan:
✅ Sistem berhasil dikembangkan dengan PHP Native & Tailwind CSS  
✅ Fitur CRUD berjalan dengan baik  
✅ Integrasi payment gateway (Midtrans) berhasil  
✅ Responsive design untuk mobile & desktop  

### Saran Pengembangan:
- Notifikasi WhatsApp otomatis
- Fitur delivery tracking
- Mobile app (Android/iOS)

---

## CATATAN TAMBAHAN UNTUK PPT

### Warna Tema yang Disarankan:
- **Primary**: Purple (#8B5CF6)
- **Secondary**: Indigo (#4F46E5)
- **Accent**: Fuchsia (#D946EF)

### Font yang Disarankan:
- **Heading**: Outfit atau Poppins
- **Body**: Inter atau Plus Jakarta Sans

### Screenshot yang Perlu Disiapkan:
1. Landing Page
2. Halaman Login
3. Dashboard Admin
4. Halaman Transaksi
5. Popup Pembayaran Midtrans
6. Customer Dashboard
7. Halaman Laporan

---

*Dokumen ini untuk referensi pembuatan slide PowerPoint*
