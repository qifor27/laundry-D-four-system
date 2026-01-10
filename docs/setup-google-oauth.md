# 🔐 Panduan Setup Google OAuth Login

**Panduan lengkap untuk mengaktifkan fitur Login dengan Google**

---

## 📋 Langkah-langkah

### 1. Buat Project di Google Cloud Console

1. Buka https://console.cloud.google.com/
2. Login dengan akun Google Anda
3. Klik **"Select a project"** → **"New Project"**
4. Isi nama project (contoh: `D'four Laundry`)
5. Klik **Create**

---

### 2. Aktifkan Google+ API

1. Di sidebar, klik **"APIs & Services"** → **"Library"**
2. Cari **"Google+ API"** atau **"Google Identity"**
3. Klik dan tekan **"Enable"**

---

### 3. Buat OAuth Credentials

1. Klik **"APIs & Services"** → **"Credentials"**
2. Klik **"+ CREATE CREDENTIALS"** → **"OAuth client ID"**
3. Jika diminta, konfigurasi **OAuth consent screen** dulu:
   - User Type: **External**
   - App name: `D'four Laundry`
   - User support email: (email Anda)
   - Developer contact: (email Anda)
   - Klik **Save and Continue** sampai selesai

4. Kembali ke Credentials, buat OAuth client ID:
   - Application type: **Web application**
   - Name: `D'four Laundry Web`
   - **Authorized JavaScript origins:**
     ```
     http://localhost
     ```
   - **Authorized redirect URIs:**
     ```
     http://localhost/laundry-D-four-system/api/google-callback.php
     ```
   - Klik **Create**

5. **Catat/Copy:**
   - **Client ID** (format: `xxx.apps.googleusercontent.com`)
   - **Client Secret**

---

### 4. Update File Konfigurasi

Buka file `config/google-oauth.php` dan ganti:

```php
// Ganti dengan Client ID Anda
define('GOOGLE_CLIENT_ID', 'xxx.apps.googleusercontent.com');

// Ganti dengan Client Secret Anda  
define('GOOGLE_CLIENT_SECRET', 'your-client-secret-here');
```

---

### 5. Test Login

1. Buka: http://localhost/laundry-D-four-system/pages/auth/login.php
2. Tombol **"Sign in with Google"** akan muncul
3. Klik dan pilih akun Google
4. Jika berhasil, akan redirect ke complete-profile atau dashboard

---

## ⚠️ Troubleshooting

| Error | Solusi |
|-------|--------|
| Tombol Google tidak muncul | Pastikan `GOOGLE_CLIENT_ID` sudah benar |
| "redirect_uri_mismatch" | Pastikan URI di Google Console sama persis dengan project |
| "Access blocked" | Tambahkan email test user di OAuth consent screen |

---

## 🔗 Links Penting

- [Google Cloud Console](https://console.cloud.google.com/)
- [OAuth Consent Screen](https://console.cloud.google.com/apis/credentials/consent)
- [Credentials](https://console.cloud.google.com/apis/credentials)
