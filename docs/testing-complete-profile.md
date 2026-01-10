# 🧪 Panduan Testing: Complete Profile Feature

**Fitur:** Testing lengkap untuk Complete Profile (Google Users)  
**Last Updated:** 2026-01-08

---

## 📋 Daftar Isi

1. [Persiapan Sebelum Testing](#1-persiapan-sebelum-testing)
2. [Test Scenario 1: Akses Halaman (Belum Login)](#2-test-scenario-1-akses-halaman-belum-login)
3. [Test Scenario 2: Login via Google](#3-test-scenario-2-login-via-google)
4. [Test Scenario 3: Halaman Complete Profile](#4-test-scenario-3-halaman-complete-profile)
5. [Test Scenario 4: Validasi Input Phone](#5-test-scenario-4-validasi-input-phone)
6. [Test Scenario 5: Submit Phone (Sukses)](#6-test-scenario-5-submit-phone-sukses)
7. [Test Scenario 6: Submit Phone (Error Cases)](#7-test-scenario-6-submit-phone-error-cases)
8. [Test Database Verification](#8-test-database-verification)
9. [Checklist Testing](#9-checklist-testing)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Persiapan Sebelum Testing

### 🔧 Pastikan Server Berjalan

1. **Buka XAMPP Control Panel**
2. **Start Apache dan MySQL**
3. **Cek status:** Pastikan keduanya berwarna hijau

### 🗄️ Persiapan Database

1. **Buka phpMyAdmin:** http://localhost/phpmyadmin
2. **Pilih database:** `laundry_d_four_system` (atau nama database project)
3. **Persiapkan akun test:**
   - Buat/gunakan akun Google yang belum punya phone di database
   - Atau reset phone di database: 
   ```sql
   UPDATE users SET phone = NULL WHERE email = 'email_test@gmail.com';
   ```

### 🌐 URL yang Digunakan

| Halaman | URL |
|---------|-----|
| Login | http://localhost/laundry-D-four-system/pages/auth/login.php |
| Complete Profile | http://localhost/laundry-D-four-system/pages/auth/complete-profile.php |
| Customer Dashboard | http://localhost/laundry-D-four-system/pages/customer-dashboard.php |

---

## 2. Test Scenario 1: Akses Halaman (Belum Login)

### 📝 Langkah-langkah

1. **Buka browser** (mode incognito/private disarankan)
2. **Paste URL:** 
   ```
   http://localhost/laundry-D-four-system/pages/auth/complete-profile.php
   ```
3. **Tekan Enter**

### ✅ Expected Result

- 🔄 **Redirect** ke halaman login (`/pages/auth/login.php`)
- ❌ **Tidak boleh** bisa akses halaman complete-profile tanpa login

### 📸 Cara Verifikasi

- Lihat URL di address bar, harus berubah ke `/pages/auth/login.php`

---

## 3. Test Scenario 2: Login via Google

### 📝 Langkah-langkah

1. **Buka halaman login:**
   ```
   http://localhost/laundry-D-four-system/pages/auth/login.php
   ```

2. **Klik tombol "Login dengan Google"**

3. **Pilih/masukkan akun Google test**

4. **Izinkan akses** jika diminta

### ✅ Expected Result

**Jika akun Google BELUM punya phone:**
- 🔄 Redirect ke `/pages/auth/complete-profile.php`
- Tampil halaman form input nomor HP
- Tampil nama dan foto profil dari Google

**Jika akun Google SUDAH punya phone:**
- 🔄 Redirect langsung ke dashboard (`/pages/customer-dashboard.php`)

### 📸 Cara Verifikasi

- Lihat URL address bar
- Cek apakah nama dan foto muncul di halaman complete-profile

---

## 4. Test Scenario 3: Halaman Complete Profile

### 📝 Langkah-langkah

1. **Setelah login Google (tanpa phone)**, halaman complete-profile akan muncul

2. **Verifikasi tampilan halaman:**

### ✅ Expected Result - Tampilan

| Elemen | Status |
|--------|--------|
| Logo D'four Laundry | ✅ Tampil |
| Foto profil Google | ✅ Tampil |
| Nama user dari Google | ✅ Tampil (e.g., "Halo, John Doe! 👋") |
| Info box "Mengapa perlu nomor HP?" | ✅ Tampil |
| Input field untuk nomor HP | ✅ Tampil |
| Tombol "Simpan & Lanjutkan" | ✅ Tampil |
| Link "Logout dari akun ini" | ✅ Tampil |

### 📸 Screenshot untuk Dokumentasi

- Capture halaman complete-profile yang sudah tampil dengan benar

---

## 5. Test Scenario 4: Validasi Input Phone

### 📝 A. Test Input Kosong

1. **Kosongkan field nomor HP**
2. **Klik tombol "Simpan & Lanjutkan"**

**Expected Result:**
- ❌ Alert error: "Nomor HP wajib diisi"

---

### 📝 B. Test Format Salah

Coba masing-masing input berikut, klik submit, dan catat hasilnya:

| No | Input | Expected Result |
|----|-------|-----------------|
| 1 | `021234567` | ❌ Error: "Format tidak valid" |
| 2 | `+62812345678` | ❌ Error: "Format tidak valid" |
| 3 | `62812345678` | ❌ Error: "Format tidak valid" |
| 4 | `0812345` | ❌ Error: "Format tidak valid" (terlalu pendek) |
| 5 | `08abc12345678` | ❌ Input hanya menerima angka |
| 6 | `081 234 567 890` | Input akan auto-hapus spasi |

---

### 📝 C. Test Format Benar

| No | Input | Expected Result |
|----|-------|-----------------|
| 1 | `0812345678` | ✅ Valid (10 digit) |
| 2 | `081234567890` | ✅ Valid (12 digit) |
| 3 | `08123456789012` | ✅ Valid (14 digit) |

---

### 📝 D. Test Auto-Format Input

1. **Ketik:** `8123456789` (tanpa 0 di depan)
2. **Expected:** Input otomatis menjadi `08123456789`

---

## 6. Test Scenario 5: Submit Phone (Sukses)

### 📝 Langkah-langkah

1. **Masukkan nomor HP valid:**
   ```
   081234567890
   ```

2. **Klik tombol "Simpan & Lanjutkan"**

3. **Amati proses:**

### ✅ Expected Result

| Step | Status |
|------|--------|
| 1. Tombol berubah jadi loading | ✅ Tampil animasi spin |
| 2. Alert hijau muncul | ✅ "Profil berhasil dilengkapi!" |
| 3. Redirect otomatis | ✅ Ke `/pages/customer-dashboard.php` |

### ⏱️ Timing

- Redirect terjadi **sekitar 1.5 detik** setelah alert muncul

---

## 7. Test Scenario 6: Submit Phone (Error Cases)

### 📝 A. Phone Sudah Terdaftar

1. **Gunakan nomor yang sudah dipakai user lain**
2. **Klik submit**

**Expected Result:**
- ❌ Alert error: "Nomor HP sudah terdaftar oleh akun lain"

---

### 📝 B. Session Expired

1. **Hapus cookie browser (clear session)**
2. **Akses langsung API:**
   ```
   POST http://localhost/laundry-D-four-system/api/auth/update-phone.php
   ```

**Expected Result:**
- ❌ HTTP 401
- Error: "Anda harus login terlebih dahulu"

---

## 8. Test Database Verification

### 📝 Setelah Submit Phone Sukses, Verifikasi Database:

1. **Buka phpMyAdmin**

2. **Cek tabel `users`:**
   ```sql
   SELECT id, name, email, phone, google_id 
   FROM users 
   WHERE email = 'email_test@gmail.com';
   ```
   **Expected:** Kolom `phone` terisi dengan nomor yang diinput

3. **Cek tabel `customers`:**
   ```sql
   SELECT * FROM customers 
   WHERE phone = '081234567890';
   ```
   **Expected:** 
   - Ada record dengan phone tersebut
   - Kolom `user_id` ter-link dengan id user

---

## 9. Checklist Testing

### ✅ Test yang Harus Dilakukan

| No | Test Case | Status | Notes |
|----|-----------|--------|-------|
| 1 | Akses halaman tanpa login → Redirect ke login | ⬜ | |
| 2 | Login Google → Redirect ke complete-profile | ⬜ | |
| 3 | Tampilan halaman complete-profile | ⬜ | |
| 4 | Input kosong → Error | ⬜ | |
| 5 | Format phone salah → Error | ⬜ | |
| 6 | Auto-format input (8... → 08...) | ⬜ | |
| 7 | Phone valid → Submit sukses | ⬜ | |
| 8 | Alert sukses muncul | ⬜ | |
| 9 | Redirect ke dashboard | ⬜ | |
| 10 | Database users ter-update | ⬜ | |
| 11 | Database customers ter-link | ⬜ | |
| 12 | Phone sudah ada → Error | ⬜ | |
| 13 | Sudah punya phone → Redirect ke dashboard | ⬜ | |

### 📊 Hasil Testing

- **Total Test:** 13
- **Passed:** ___
- **Failed:** ___
- **Date:** ___
- **Tester:** ___

---

## 10. Troubleshooting

### ❌ Error: Halaman Blank/Putih

**Kemungkinan Penyebab:**
- PHP error (syntax error)
- Path file salah

**Solusi:**
1. Cek Apache error log: `C:\xampp\apache\logs\error.log`
2. Tambahkan di awal file PHP:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

---

### ❌ Error: "Method Not Allowed"

**Penyebab:** Mengakses API dengan method GET

**Solusi:** API haru diakses via POST (dari form submit)

---

### ❌ Error: CORS / Network Error

**Penyebab:** Request diblokir browser

**Solusi:**
1. Pastikan mengakses dari domain yang sama (localhost)
2. Cek DevTools → Console untuk error detail

---

### ❌ Redirect Loop

**Penyebab:** Logic redirect bermasalah

**Solusi:**
1. Clear browser cookies
2. Reset phone di database ke NULL
3. Coba login ulang

---

### ❌ Foto Google Tidak Tampil

**Penyebab:** URL foto blocked atau expired

**Solusi:** Normal jika foto tidak tersimpan di session, akan tampil avatar default

---

## 📌 Tips Testing

1. **Gunakan Incognito/Private Mode** - Memastikan session bersih
2. **Buka DevTools (F12)** - Monitor Network dan Console untuk debug
3. **Screenshot setiap step** - Untuk dokumentasi
4. **Test di browser berbeda** - Chrome, Firefox, Edge
5. **Test responsive** - Cek di mobile view (DevTools → Toggle device)

---

**Selamat Testing! 🧪**

Jika menemukan bug atau issue, catat dengan detail:
- Langkah untuk reproduce
- Expected vs Actual result
- Screenshot/video jika perlu
