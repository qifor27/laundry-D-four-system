# 📱 Panduan Cancel Google One & Pindah Akun

## 🎯 Tujuan
Cancel subscription Google One untuk menghemat biaya bulan ini.

---

## 📋 Langkah Cancel Google One

### Via Browser (Desktop/Laptop)

1. **Buka Google One Settings**
   ```
   https://one.google.com/settings
   ```

2. **Login** dengan akun Google yang berlangganan

3. **Klik "Manage Membership"** atau **"Kelola Keanggotaan"**

4. **Pilih "Cancel Membership"** atau **"Batalkan Keanggotaan"**

5. **Ikuti konfirmasi** pembatalan

6. **Selesai!** Langganan akan berakhir di akhir periode billing

---

### Via HP Android

1. Buka **Google Play Store**

2. Tap **Profile icon** (kanan atas)

3. Pilih **"Payments & subscriptions"** → **"Subscriptions"**

4. Cari **"Google One"**

5. Tap **"Cancel subscription"**

6. Konfirmasi pembatalan

---

### Via iPhone

1. Buka **Settings**

2. Tap nama Anda di atas → **"Subscriptions"**

3. Cari **"Google One"**

4. Tap **"Cancel Subscription"**

---

## ⚠️ Yang Perlu Diperhatikan

| Aspek | Keterangan |
|-------|------------|
| **Akses tetap aktif** | Sampai akhir periode billing |
| **Storage berkurang** | Kembali ke 15GB setelah expired |
| **Data tidak hilang** | Tapi tidak bisa upload baru jika over quota |
| **Refund** | Biasanya tidak ada refund partial |

---

## 🔄 Jika Ingin Pindah Akun

### Langkah 1: Backup Data Penting

1. **Google Drive**
   - Download file penting ke komputer
   - Atau gunakan Google Takeout: `takeout.google.com`

2. **Google Photos**
   - Download foto/video penting
   - Atau transfer ke akun baru via sharing

3. **Gmail**
   - Forward email penting
   - Atau export via Google Takeout

### Langkah 2: Setup Akun Baru

1. Buat akun Google baru (jika belum ada)
2. Login di perangkat
3. Transfer file yang diperlukan

### Langkah 3: Update di Proyek

Jika akun Google dipakai untuk OAuth di proyek:

1. **Google Cloud Console**
   - Login dengan akun baru
   - Buat project baru atau transfer ownership

2. **Update config di proyek**
   ```php
   // config/google.php
   return [
       'client_id' => 'NEW_CLIENT_ID',
       'client_secret' => 'NEW_CLIENT_SECRET',
       'redirect_uri' => 'YOUR_REDIRECT_URI'
   ];
   ```

---

## ✅ Checklist

- [ ] Cancel Google One subscription
- [ ] Backup data penting (jika pindah akun)
- [ ] Setup akun baru (jika diperlukan)
- [ ] Update OAuth credentials di proyek (jika pindah akun)

---

## 💡 Tips Hemat Storage

Jika kembali ke 15GB gratis:

1. **Hapus file besar** di Google Drive
2. **Kompres foto** di Google Photos (High Quality, bukan Original)
3. **Hapus email dengan attachment besar** di Gmail
4. **Kosongkan Trash** di Drive dan Gmail

---

*Panduan dibuat: 14 Januari 2026*
