# 📋 D'four Laundry - Development TODO List

**Last Updated: 2026-01-07**

---

## ✅ COMPLETED FEATURES

### Customer-User Integration ✅
- [x] **Database Migration** - Tambah kolom `phone` di users, `user_id` di customers
- [x] **Registration dengan Phone** - Form register menyimpan nomor HP
- [x] **Auto-link Customer-User** - Otomatis link berdasarkan phone number
- [x] **Update Customer CRUD** - Status "Terdaftar/Belum Daftar" + JOIN users
- [x] **Invite Customer** - Tombol undang untuk customer belum terdaftar
- [x] **Google OAuth Integration** - Login dengan Google + cek phone

### Authentication System ✅
- [x] **Google OAuth 2.0 Login** - Setup \u0026 konfigurasi
- [x] **Login Page UI** - Modern design dengan Tailwind CSS
- [x] **Register Page UI** - Form registrasi dengan validasi
- [x] **Session Management** - Helper functions (isLoggedIn, requireLogin, getUserData)
- [x] **Logout Functionality** - Destroy session dengan benar
- [x] **Protected Pages** - Dashboard dilindungi dengan auth check
- [x] **Customer Dashboard** - Halaman khusus untuk pelanggan

### Landing Page \u0026 UI ✅
- [x] **Modern Landing Page** - Redesign dengan Tailwind CSS
- [x] **Bubble Decorations** - Background bubbles untuk estetika
- [x] **Responsive Design** - Mobile-friendly layout
- [x] **Custom Typography** - Google Fonts (Outfit)

---

## 🎨 DESIGN REDESIGN (NEW!)

> **Referensi Lengkap:** Lihat dokumentasi detail di `design_inspiration.md`

### 🌈 Design System - Color Palette

| Konteks | Primary | Secondary | Accent | Background |
|---------|---------|-----------|--------|------------|
| **User/Pelanggan** | Purple `#7C3AED` | Pink `#EC4899` | Green `#A3E635` | Gradient Pink-Purple |
| **Login/Register** | Royal Blue `#4F46E5` | Gray `#9CA3AF` | - | Soft Gray `#F8F9FB` |
| **Admin** | Purple `#8B5CF6` | Pink `#EC4899` | - | Light Pink `#FDF2F8` |

### ✨ Ciri Khas Design yang Harus Diterapkan

- [ ] **Glassmorphism Cards**
  - Background transparan dengan blur (`backdrop-filter: blur`)
  - Border semi-transparent putih
  - Soft shadow
  - Border radius besar (16px - 24px)

- [ ] **Gradient Background**
  - User dashboard: Pink ke Purple gradient
  - Admin sidebar: Pink ke Purple gradient vertikal
  - Login: Soft gray/white clean

- [ ] **Rounded UI Elements**
  - Semua card/button punya border-radius besar
  - Sidebar dengan rounded corners di sisi kanan
  - Input fields dengan underline style (Login page)

- [ ] **Large Typography untuk Stats**
  - Angka statistik: 36px - 48px, Bold
  - Heading: 24px - 32px, Semibold
  - Font: Outfit/Inter/Poppins

- [ ] **Decorative Elements**
  - Gradient blobs di background
  - Circular shapes dengan opacity rendah
  - Bubble decorations (laundry theme)

- [ ] **Modern Icons**
  - Line icons (outlined, bukan filled)
  - Consistent stroke width (1.5px - 2px)
  - Library: Lucide Icons atau Heroicons

### 📄 Pages Redesign Checklist

#### Login & Register (Gambar 2 Style)
- [ ] Split layout (Form kiri, CTA panel kanan)
- [ ] Clean white cards dengan soft shadow
- [ ] Underline-style input fields
- [ ] Rounded pill buttons (Royal Blue)
- [ ] Social login icons (Facebook, LinkedIn, Twitter)
- [ ] Sliding animation antara Login ↔ Register

#### User/Customer Dashboard (Gambar 1 Style)
- [ ] Pink-Purple gradient background
- [ ] Left sidebar dengan course/menu cards
- [ ] Welcome message dengan nama user
- [ ] Stats row (3-4 glassmorphism cards)
- [ ] Activity chart dengan gradient lines
- [ ] Donut chart untuk kategori
- [ ] Upcoming assignments/orders list

#### Admin Dashboard (Gambar 3 Style)
- [ ] Narrow gradient sidebar (icon-only, 80px)
- [ ] Stats cards row di top
- [ ] Traffic/analytics charts
- [ ] Inventory/Transaction list
- [ ] Customer panel di sebelah kanan
- [ ] Avatar+name untuk customer list items

### 🛠️ Technical Implementation

- [ ] **CSS Variables Setup**
  - Define semua warna sebagai CSS variables
  - Create utility classes untuk gradients
  
- [ ] **Component Library**
  - GlassCard component
  - GradientButton component
  - StatCard component
  - ChartWrapper component

- [ ] **Chart Styling**
  - Chart.js dengan custom colors
  - Gradient fills untuk area charts
  - Rounded bars untuk bar charts

---

## 🎨 FRONTEND TASKS

### 🔴 Priority: HIGH (Harus Segera)

#### UI/UX Improvements
- [ ] **Loading States**
  - Tambah loading spinner saat AJAX request
  - Show skeleton loading untuk table data

- [ ] **Form Validation (Client-Side)**
  - Validate required fields sebelum submit
  - Show error messages di bawah input field

- [ ] **Error Handling UI**
  - Toast notifications untuk success/error
  - Modal untuk confirmations (delete, etc)

#### Page Enhancements
- [ ] **Dashboard Page**
  - Add charts untuk visualisasi data (Chart.js/ApexCharts)
  - Add date range filter untuk reports
  - Add quick actions shortcuts

- [ ] **Customers Page**
  - Add pagination untuk large data sets
  - Show customer transaction history in detail view

- [ ] **Transactions Page**
  - Better filter UI (by date, status, customer)
  - Sort functionality (by date, price, etc)
  - Print receipt button dengan design yang bagus

#### New Features
- [ ] **Print System**
  - Print receipt/invoice dengan CSS print
  - Thermal printer friendly format

- [ ] **Dark Mode**
  - Toggle dark/light theme
  - Save preference to localStorage

### 🟡 Priority: MEDIUM

- [ ] **PWA Features**
  - Add manifest.json
  - Service worker untuk offline capability

- [ ] **Animation \u0026 Transitions**
  - Smooth page transitions
  - Micro-interactions pada buttons

---

## ⚙️ BACKEND TASKS

> **Branch Strategy:** Setiap grup fitur dikerjakan di branch terpisah untuk meminimalisir merge conflict.

### 🌿 Daftar Branch Backend

| No | Branch | Priority | Status |
|----|--------|----------|--------|
| 1 | `feature/backend-complete-profile` | 🔴 HIGH | ✅ Selesai |
| 2 | `feature/backend-monthly-reports` | 🟡 MEDIUM | ✅ Selesai |
| 3 | `feature/backend-export-reports` | 🟡 MEDIUM | ✅ Selesai |
| 4 | `feature/backend-email-notifications` | 🟡 MEDIUM | ✅ Selesai |
| 5 | `feature/backend-whatsapp-payment-integration` | 🟡 MEDIUM | ✅ Selesai |
| 6 | `feature/backend-security` | 🔴 HIGH | ⬜ Terakhir |
| 7 | `feature/backend-loyalty` | 🟢 LOW | ⬜ Belum |
| 8 | `feature/backend-multi-branch` | 🟢 LOW | ⬜ Belum |

---

### Branch 1: `feature/backend-complete-profile` ✅

- [x] **pages/auth/complete-profile.php** - Halaman untuk user Google melengkapi HP
- [x] **api/auth/update-phone.php** - API untuk menyimpan nomor HP dan link customer
- [x] **config/google-oauth.php.template** - Template konfigurasi Google OAuth
- [x] **docs/tutorial-complete-profile.md** - Tutorial lengkap
- [x] **docs/testing-complete-profile.md** - Panduan testing
- [x] **docs/setup-google-oauth.md** - Setup Google OAuth

---

### Branch 2: `feature/backend-monthly-reports` ✅

**Files:**
- [x] `pages/reports.php` - UI halaman laporan
- [x] `api/reports-api.php` - API endpoint laporan

**API Actions:**
- [x] `action=monthly_summary` - Ringkasan transaksi per bulan
- [x] `action=daily_breakdown` - Detail per hari dalam sebulan
- [x] `action=service_report` - Laporan per jenis layanan
- [x] `action=customer_report` - Laporan pelanggan
- [x] `action=status_report` - Laporan status transaksi

**Dashboard Widget:**
- [x] Quick stats di dashboard admin
- [x] Mini chart trend minggu ini
- [x] Comparison with last period

---

### Branch 3: `feature/backend-export-reports` ✅

> **Dependency:** Harus setelah `feature/backend-monthly-reports`

- [x] Export ke CSV (Simple Excel)
- [x] Export ke Print View (PDF via browser)
- [x] `api/export-api.php` - API endpoint export

---

### Branch 4: `feature/backend-email-notifications` ✅

- [x] `includes/email-helper.php` - PHPMailer integration
- [x] `config/email.php.template` - Template konfigurasi SMTP
- [x] Template email HTML (Order Created, Status Update, Ready Pickup)
- [x] `docs/tutorial-email-notifications.md` - Tutorial lengkap

---

### Branch 5: `feature/backend-whatsapp-payment-integration` ✅

> **Note:** Branch 5 & 6 (WhatsApp + Payment) digabung karena terintegrasi erat

**WhatsApp (Manual Click-to-Chat - GRATIS):**
- [x] `includes/whatsapp-helper.php` - Generate WA URLs & messages
- [x] `formatPhoneNumber()` - Konversi 08xxx ke 628xxx
- [x] `getBankListForWA()` - Ambil bank dari database (dinamis)
- [x] Tombol WA di `pages/transaction-detail.php`

**Payment Methods:**
- [x] `database/migrate_payments.php` - Migrasi tabel
- [x] `api/payment-methods-api.php` - CRUD rekening bank
- [x] `api/payments-api.php` - Konfirmasi pembayaran
- [x] `includes/payment-helper.php` - Helper functions
- [x] `pages/payment-methods.php` - Admin panel rekening
- [x] `pages/transaction-detail.php` - Detail + konfirmasi bayar

**Dokumentasi:**
- [x] `docs/tutorial-whatsapp-payment-integration.md`
- [x] `docs/tutorial-whatsapp-integration.md`
- [x] `docs/tutorial-payment-methods.md`

---

### Branch 6: `feature/backend-security` 🔴

> **⚠️ DIKERJAKAN TERAKHIR** - Karena update banyak file form dan API

**CSRF Protection:**
- [ ] Function `requireCsrf()` di `includes/auth.php`
- [ ] Update semua form dengan `csrfField()`
- [ ] Update semua API dengan `requireCsrf()`

**Rate Limiting:**
- [ ] Limit API requests per IP
- [ ] Prevent brute force attacks
- [ ] Logging failed attempts

**Dokumentasi:**
- [ ] `docs/tutorial-csrf-protection.md` ✅

---

### Branch 7: `feature/backend-loyalty` 🟢

> **Nice to Have** - Prioritas rendah

- [ ] Points system
- [ ] Rewards/discount
- [ ] Customer tier (Bronze, Silver, Gold)

---

### Branch 8: `feature/backend-multi-branch` 🟢

> **Nice to Have** - Major feature, paling akhir

- [ ] Branch management
- [ ] Consolidated reporting
- [ ] Per-branch dashboard

---

## 🧪 TESTING \u0026 QUALITY

- [ ] **Manual Testing**
  - Test semua CRUD operations
  - Test edge cases
  - Browser compatibility testing

- [ ] **Security Audit**
  - SQL injection testing
  - XSS testing
  - Session security check

---

## � GIT COLLABORATION WORKFLOW

> **Panduan untuk memulai kolaborasi tim Frontend & Backend**

### 🌿 Branch Strategy

```
main                    ← Branch production (protected)
├── develop             ← Branch development utama
├── feature/frontend-*  ← Branch untuk tim Frontend
└── feature/backend-*   ← Branch untuk tim Backend
```

### 📋 Setup Awal (Untuk Semua Anggota Tim)

- [ ] **Clone Repository**
  ```bash
  git clone https://github.com/qifor27/laundry-D-four-system.git
  cd laundry-D-four-system
  ```

- [ ] **Buat Branch develop** (Admin/Lead saja)
  ```bash
  git checkout -b develop
  git push -u origin develop
  ```

- [ ] **Setup Environment**
  - Copy `config/database.example.php` ke `config/database.php`
  - Sesuaikan kredensial database lokal
  - Import `database/schema.sql`

### 👨‍💻 Tim Frontend

**Folder yang dikerjakan:**
- `assets/css/` - Stylesheet
- `assets/js/` - JavaScript
- `pages/` - Views/Templates (bagian HTML/UI)
- `includes/` - Components (header, sidebar, footer)

**Cara Mulai:**
```bash
# 1. Ambil update terbaru
git checkout develop
git pull origin develop

# 2. Buat branch baru
git checkout -b feature/frontend-[nama-fitur]
# Contoh: feature/frontend-login-redesign

# 3. Kerjakan perubahan...

# 4. Commit & Push
git add .
git commit -m "style: redesign login page dengan glassmorphism"
git push -u origin feature/frontend-[nama-fitur]

# 5. Buat Pull Request ke develop di GitHub
```

**Naming Convention Commit (Frontend):**
- `style:` - Perubahan CSS/styling
- `feat:` - Fitur UI baru
- `fix:` - Bug fix UI
- `refactor:` - Restructure code tanpa mengubah fungsi

### ⚙️ Tim Backend

**Folder yang dikerjakan:**
- `api/` - API endpoints
- `config/` - Konfigurasi
- `database/` - SQL scripts, migrations
- `pages/` - Logic PHP (bagian server-side)

**Cara Mulai:**
```bash
# 1. Ambil update terbaru
git checkout develop
git pull origin develop

# 2. Buat branch baru
git checkout -b feature/backend-[nama-fitur]
# Contoh: feature/backend-monthly-reports

# 3. Kerjakan perubahan...

# 4. Commit & Push
git add .
git commit -m "feat(api): add monthly reports endpoint"
git push -u origin feature/backend-[nama-fitur]

# 5. Buat Pull Request ke develop di GitHub
```

**Naming Convention Commit (Backend):**
- `feat(api):` - Endpoint baru
- `feat(db):` - Perubahan database
- `fix:` - Bug fix
- `security:` - Perbaikan keamanan

### 🔄 Merge Workflow

```
feature/* → develop → main
```

1. **Feature ke Develop**: Pull Request + Code Review
2. **Develop ke Main**: Setelah testing selesai (Admin only)

### ⚠️ Aturan Penting

| ❌ Jangan | ✅ Lakukan |
|-----------|-----------|
| Push langsung ke `main` | Buat Pull Request |
| Commit file `config/database.php` | Pakai `.gitignore` |
| Merge tanpa review | Minta review minimal 1 orang |
| Commit besar sekaligus | Commit kecil dan sering |

### 📊 Progress Tracking

#### Frontend Tasks
- [ ] Login/Register Redesign
- [ ] User Dashboard Redesign
- [ ] Admin Dashboard Redesign
- [ ] Responsive Mobile View
- [ ] Dark Mode Toggle

#### Backend Tasks
- [ ] Complete Profile API
- [ ] Monthly Reports API
- [ ] Email Notification System
- [ ] CSRF Protection
- [ ] Rate Limiting

---

## �📚 DOCUMENTATION

- [x] **Registration Flow** - `docs/registration-flow.md`
- [ ] **User Manual** - Panduan penggunaan aplikasi
- [ ] **API Documentation** - Dokumentasi endpoint API
- [ ] **Deployment Guide** - Cara deploy ke production

---

## 🚀 DEPLOYMENT

- [ ] **Production Setup**
  - Optimize Tailwind CSS (purge, minify)
  - HTTPS setup (SSL certificate)
  - Environment configuration

---

## 📝 NEXT PRIORITY

Urutkan pengerjaan berikutnya:

1. ⏳ **Complete Profile Page** - Untuk user Google yang belum punya HP
2. 📊 **Laporan Bulanan** - Reports page + API
3. 🖨️ **Print Receipt** - Cetak struk/invoice
4. 📧 **Email Configuration** - Setup SMTP untuk notifikasi

---

*Prioritas bisa berubah sesuai kebutuhan bisnis*