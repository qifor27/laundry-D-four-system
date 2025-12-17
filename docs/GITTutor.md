# GITTutor - Panduan Git & GitHub untuk Laundry D'four

## 📚 Daftar Isi
1. [Pengenalan Git & GitHub](#pengenalan-git--github)
2. [Instalasi Git](#instalasi-git)
3. [Konfigurasi Awal Git](#konfigurasi-awal-git)
4. [Inisialisasi Project ke Git](#inisialisasi-project-ke-git)
5. [Upload ke GitHub](#upload-ke-github)
6. [Workflow Git Sehari-hari](#workflow-git-sehari-hari)
7. [Perintah Git yang Sering Digunakan](#perintah-git-yang-sering-digunakan)
8. [Tips & Best Practices](#tips--best-practices)
9. [Kolaborasi Frontend & Backend](#-kolaborasi-frontend--backend)

---

## 🎯 Pengenalan Git & GitHub

### Apa itu Git?
**Git** adalah sistem version control yang membantu Anda:
- 📝 Melacak perubahan kode
- ⏮️ Kembali ke versi sebelumnya jika ada kesalahan
- 👥 Berkolaborasi dengan developer lain
- 🔀 Mengelola berbagai versi/fitur secara paralel

### Apa itu GitHub?
**GitHub** adalah platform hosting untuk repository Git yang menyediakan:
- ☁️ Penyimpanan cloud untuk kode Anda
- 👥 Kolaborasi tim
- 📊 Manajemen project
- 🔒 Backup otomatis

---

## 💻 Instalasi Git

### Windows
1. Download Git dari [https://git-scm.com/download/win](https://git-scm.com/download/win)
2. Jalankan installer
3. Ikuti wizard instalasi (gunakan pengaturan default)
4. Verifikasi instalasi dengan membuka Command Prompt atau PowerShell:
   ```bash
   git --version
   ```
   Jika muncul versi Git, instalasi berhasil!

### Cek Apakah Git Sudah Terinstall
```bash
git --version
```

---

## ⚙️ Konfigurasi Awal Git

Sebelum menggunakan Git, Anda perlu mengatur identitas Anda:

```bash
# Set nama Anda
git config --global user.name "Nama Anda"

# Set email Anda (gunakan email yang sama dengan akun GitHub)
git config --global user.email "email@example.com"

# Verifikasi konfigurasi
git config --list
```

**Contoh:**
```bash
git config --global user.name "John Doe"
git config --global user.email "johndoe@gmail.com"
```

---

## 🚀 Inisialisasi Project ke Git

### Langkah 1: Buka Terminal di Folder Project
```bash
# Navigasi ke folder project
cd "e:\xampp2\htdocs\Laundry D'four"
```

### Langkah 2: Inisialisasi Git Repository
```bash
git init
```
Perintah ini akan membuat folder `.git` tersembunyi yang menyimpan semua history Git.

### Langkah 3: Tambahkan File ke Staging Area
```bash
# Tambahkan semua file
git add .

# ATAU tambahkan file tertentu
git add index.php
git add assets/css/style.css
```

**Penjelasan:**
- `git add .` → Menambahkan semua file yang belum di-track
- `.gitignore` akan mengecualikan file/folder yang tidak perlu (seperti `node_modules`, `database/laundry.db`)

### Langkah 4: Commit Perubahan
```bash
git commit -m "Initial commit: Laundry D'four Management System"
```

**Tips Pesan Commit yang Baik:**
- ✅ "Add customer registration feature"
- ✅ "Fix transaction calculation bug"
- ✅ "Update dashboard UI with Tailwind CSS"
- ❌ "update" (terlalu umum)
- ❌ "fix bug" (tidak spesifik)

---

## 🌐 Upload ke GitHub

### Langkah 1: Buat Repository di GitHub
1. Login ke [GitHub.com](https://github.com)
2. Klik tombol **"+"** di pojok kanan atas → **"New repository"**
3. Isi detail repository:
   - **Repository name:** `laundry-dfour` (atau nama lain)
   - **Description:** "Laundry Management System with PHP Native, Tailwind CSS, and SQLite"
   - **Visibility:** Public atau Private (pilih sesuai kebutuhan)
   - ⚠️ **JANGAN** centang "Initialize this repository with a README" (karena kita sudah punya project)
4. Klik **"Create repository"**

### Langkah 2: Hubungkan Local Repository ke GitHub
Setelah membuat repository, GitHub akan menampilkan instruksi. Gunakan yang ini:

```bash
# Tambahkan remote repository
git remote add origin https://github.com/username/laundry-dfour.git

# Ganti 'username' dengan username GitHub Anda
```

**Contoh:**
```bash
git remote add origin https://github.com/johndoe/laundry-dfour.git
```

### Langkah 3: Push ke GitHub
```bash
# Push ke branch main
git branch -M main
git push -u origin main
```

**Penjelasan:**
- `git branch -M main` → Rename branch default ke 'main'
- `git push -u origin main` → Upload kode ke GitHub
- `-u` → Set upstream, jadi next time cukup `git push`

### Langkah 4: Verifikasi
Buka repository Anda di GitHub, file project seharusnya sudah muncul!

---

## 🔄 Workflow Git Sehari-hari

### Scenario 1: Menambah/Mengubah File
```bash
# 1. Cek status file yang berubah
git status

# 2. Tambahkan file yang diubah
git add .
# ATAU tambahkan file spesifik
git add pages/transactions.php

# 3. Commit dengan pesan yang jelas
git commit -m "Add payment status filter to transactions page"

# 4. Push ke GitHub
git push
```

### Scenario 2: Melihat History Perubahan
```bash
# Lihat log commit
git log

# Lihat log dalam format singkat
git log --oneline

# Lihat perubahan pada file tertentu
git log -p pages/transactions.php
```

### Scenario 3: Membatalkan Perubahan
```bash
# Batalkan perubahan file yang belum di-add
git checkout -- namafile.php

# Batalkan semua perubahan yang belum di-add
git checkout -- .

# Unstage file yang sudah di-add
git reset HEAD namafile.php

# Kembali ke commit sebelumnya (HATI-HATI!)
git reset --hard HEAD~1
```

### Scenario 4: Bekerja dengan Branch
```bash
# Buat branch baru untuk fitur
git branch feature-payment

# Pindah ke branch tersebut
git checkout feature-payment
# ATAU buat dan langsung pindah
git checkout -b feature-payment

# Lihat semua branch
git branch

# Merge branch ke main
git checkout main
git merge feature-payment

# Hapus branch yang sudah tidak dipakai
git branch -d feature-payment
```

---

## 📖 Perintah Git yang Sering Digunakan

### Basic Commands
| Perintah | Fungsi |
|----------|--------|
| `git init` | Inisialisasi repository Git baru |
| `git status` | Cek status file (modified, staged, untracked) |
| `git add <file>` | Tambahkan file ke staging area |
| `git add .` | Tambahkan semua file ke staging area |
| `git commit -m "message"` | Commit perubahan dengan pesan |
| `git log` | Lihat history commit |
| `git diff` | Lihat perubahan yang belum di-stage |

### Remote Commands
| Perintah | Fungsi |
|----------|--------|
| `git remote add origin <url>` | Hubungkan ke remote repository |
| `git remote -v` | Lihat remote repository |
| `git push` | Upload commit ke GitHub |
| `git pull` | Download perubahan dari GitHub |
| `git clone <url>` | Clone repository dari GitHub |

### Branch Commands
| Perintah | Fungsi |
|----------|--------|
| `git branch` | Lihat semua branch |
| `git branch <name>` | Buat branch baru |
| `git checkout <branch>` | Pindah ke branch lain |
| `git checkout -b <name>` | Buat dan pindah ke branch baru |
| `git merge <branch>` | Merge branch ke branch aktif |
| `git branch -d <name>` | Hapus branch |

### Undo Commands
| Perintah | Fungsi |
|----------|--------|
| `git checkout -- <file>` | Batalkan perubahan file |
| `git reset HEAD <file>` | Unstage file |
| `git reset --soft HEAD~1` | Undo commit terakhir (keep changes) |
| `git reset --hard HEAD~1` | Undo commit terakhir (discard changes) |
| `git revert <commit>` | Buat commit baru yang membatalkan commit tertentu |

---

## 💡 Tips & Best Practices

### 1. Commit Sering, Push Teratur
- ✅ Commit setiap kali selesai fitur kecil
- ✅ Push minimal 1x sehari
- ❌ Jangan tunggu sampai banyak perubahan baru commit

### 2. Tulis Pesan Commit yang Jelas
```bash
# ✅ BAIK
git commit -m "Add customer search functionality"
git commit -m "Fix calculation bug in transaction total"
git commit -m "Update dashboard UI with responsive design"

# ❌ BURUK
git commit -m "update"
git commit -m "fix"
git commit -m "changes"
```

### 3. Gunakan .gitignore
File `.gitignore` sudah ada di project ini. Pastikan file-file ini tidak di-commit:
- `node_modules/`
- `database/laundry.db` (database lokal)
- File konfigurasi pribadi
- File temporary

### 4. Gunakan Branch untuk Fitur Baru
```bash
# Buat branch untuk fitur baru
git checkout -b feature-whatsapp-notification

# Kerjakan fitur di branch tersebut
# ... coding ...

# Commit perubahan
git add .
git commit -m "Add WhatsApp notification feature"

# Merge ke main setelah selesai
git checkout main
git merge feature-whatsapp-notification
```

### 5. Pull Sebelum Push
Jika bekerja dengan tim atau dari beberapa komputer:
```bash
git pull origin main
git push origin main
```

### 6. Backup Sebelum Operasi Berbahaya
Sebelum `git reset --hard` atau operasi yang menghapus perubahan:
```bash
# Buat branch backup
git branch backup-before-reset

# Atau commit dulu
git add .
git commit -m "WIP: backup before reset"
```

---

## 🆘 Troubleshooting

### Problem 1: "fatal: remote origin already exists"
```bash
# Hapus remote yang ada
git remote remove origin

# Tambahkan lagi
git remote add origin https://github.com/username/repo.git
```

### Problem 2: Conflict saat Merge
```bash
# 1. Lihat file yang conflict
git status

# 2. Buka file, cari marker conflict:
# <<<<<<< HEAD
# kode Anda
# =======
# kode dari branch lain
# >>>>>>> branch-name

# 3. Edit file, hapus marker, pilih kode yang benar

# 4. Add dan commit
git add .
git commit -m "Resolve merge conflict"
```

### Problem 3: Lupa Password GitHub
Gunakan **Personal Access Token** (PAT):
1. GitHub → Settings → Developer settings → Personal access tokens
2. Generate new token
3. Gunakan token sebagai password saat push

### Problem 4: File Besar Tidak Bisa di-Push
```bash
# Gunakan Git LFS untuk file besar
git lfs install
git lfs track "*.db"
git add .gitattributes
git commit -m "Add Git LFS"
```

---

## 👥 Kolaborasi Frontend & Backend

### 🎯 Workflow Kolaborasi Tim

Ketika bekerja dalam tim dengan pembagian Frontend dan Backend, berikut adalah workflow yang direkomendasikan:

### 📋 Setup Awal Tim

#### 1. Repository Owner (Team Lead)
```bash
# Sudah dilakukan - repository sudah ada di GitHub
# https://github.com/qifor27/laundry-D-four-system.git
```

#### 2. Frontend Developer - Clone Repository
```bash
# Clone repository
git clone https://github.com/qifor27/laundry-D-four-system.git
cd laundry-D-four-system

# Setup environment
npm install
npm run build

# Buat branch untuk kerja frontend
git checkout -b frontend-dev
```

#### 3. Backend Developer - Clone Repository
```bash
# Clone repository
git clone https://github.com/qifor27/laundry-D-four-system.git
cd laundry-D-four-system

# Setup database
php database/create_database.php
php database/init_mysql.php

# Buat branch untuk kerja backend
git checkout -b backend-dev
```

---

### 🔀 Strategi Branching untuk Tim

```
main (production-ready)
├── frontend-dev (branch utama frontend)
│   ├── feature-ui-dashboard
│   ├── feature-customer-form
│   └── feature-responsive-design
│
└── backend-dev (branch utama backend)
    ├── feature-api-customers
    ├── feature-api-transactions
    └── feature-database-optimization
```

---

### 💼 Workflow Frontend Developer

#### Scenario: Membuat UI Dashboard Baru

**Step 1: Buat Branch Fitur**
```bash
# Pastikan di branch frontend-dev
git checkout frontend-dev
git pull origin frontend-dev

# Buat branch untuk fitur spesifik
git checkout -b feature-ui-dashboard
```

**Step 2: Kerjakan Fitur**
```bash
# Edit file frontend
# - assets/css/input.css
# - pages/dashboard.php (bagian HTML/CSS)
# - assets/js/main.js

# Compile Tailwind
npm run dev
```

**Step 3: Commit Perubahan**
```bash
git add assets/css/input.css pages/dashboard.php
git commit -m "feat(frontend): Add new dashboard UI with statistics cards"
```

**Step 4: Push ke GitHub**
```bash
git push origin feature-ui-dashboard
```

**Step 5: Buat Pull Request**
1. Buka GitHub repository
2. Klik "Compare & pull request"
3. Base: `frontend-dev` ← Compare: `feature-ui-dashboard`
4. Tulis deskripsi perubahan
5. Request review dari team lead
6. Setelah approved, merge ke `frontend-dev`

**Step 6: Sync dengan Main**
```bash
# Setelah merge, update local
git checkout frontend-dev
git pull origin frontend-dev

# Hapus branch fitur yang sudah selesai
git branch -d feature-ui-dashboard
```

---

### ⚙️ Workflow Backend Developer

#### Scenario: Membuat API Endpoint Baru

**Step 1: Buat Branch Fitur**
```bash
# Pastikan di branch backend-dev
git checkout backend-dev
git pull origin backend-dev

# Buat branch untuk fitur spesifik
git checkout -b feature-api-reports
```

**Step 2: Kerjakan Fitur**
```bash
# Edit file backend
# - api/reports-api.php (buat file baru)
# - config/database_mysql.php (jika perlu)
# - database/migrations (jika ada perubahan schema)
```

**Step 3: Test API**
```bash
# Test dengan Postman atau curl
curl http://localhost/laundry-D-four/api/reports-api.php?action=daily
```

**Step 4: Commit Perubahan**
```bash
git add api/reports-api.php
git commit -m "feat(backend): Add daily reports API endpoint"
```

**Step 5: Push & Pull Request**
```bash
git push origin feature-api-reports

# Buat PR di GitHub
# Base: backend-dev ← Compare: feature-api-reports
```

---

### 🤝 Integrasi Frontend & Backend

#### Scenario: Frontend Butuh Data dari Backend API

**Backend Developer:**
```bash
# 1. Buat API endpoint
git checkout -b feature-api-customers
# Edit api/customers-api.php
git add api/customers-api.php
git commit -m "feat(api): Add GET /customers endpoint"
git push origin feature-api-customers

# 2. Dokumentasikan API
# Tambahkan ke docs/API.md atau README
```

**Frontend Developer:**
```bash
# 1. Tunggu API di-merge atau pull branch backend
git fetch origin
git checkout feature-api-customers
# Test API

# 2. Implementasi di frontend
git checkout frontend-dev
git checkout -b feature-customer-list
# Edit pages/customers.php dan assets/js/main.js
git add .
git commit -m "feat(frontend): Integrate customer list with API"
git push origin feature-customer-list
```

---

### 🔄 Daily Workflow Tim

#### Pagi Hari - Sync dengan Tim
```bash
# Frontend Developer
git checkout frontend-dev
git pull origin frontend-dev
git pull origin main  # Ambil update dari production

# Backend Developer
git checkout backend-dev
git pull origin backend-dev
git pull origin main  # Ambil update dari production
```

#### Saat Bekerja
```bash
# Buat branch fitur dari branch dev masing-masing
git checkout -b feature-nama-fitur

# Commit berkala (setiap 1-2 jam atau setelah fitur kecil selesai)
git add .
git commit -m "feat: deskripsi perubahan"

# Push ke remote (minimal 2x sehari)
git push origin feature-nama-fitur
```

#### Akhir Hari - Push Progress
```bash
# Push semua progress hari ini
git push origin feature-nama-fitur

# Buat PR jika fitur sudah selesai
# Atau tandai sebagai WIP (Work in Progress) di PR title
```

---

### 📝 Konvensi Commit Message untuk Tim

Gunakan format standar untuk memudahkan tracking:

```bash
# Format: <type>(<scope>): <subject>

# Frontend commits
git commit -m "feat(ui): Add responsive navbar"
git commit -m "style(dashboard): Update card colors"
git commit -m "fix(form): Fix validation on customer form"

# Backend commits
git commit -m "feat(api): Add transaction filtering endpoint"
git commit -m "fix(db): Fix foreign key constraint"
git commit -m "perf(query): Optimize customer search query"

# Database commits
git commit -m "db(migration): Add indexes to transactions table"
git commit -m "db(seed): Add default service types"

# Documentation
git commit -m "docs(api): Document customer endpoints"
git commit -m "docs(readme): Update installation steps"
```

**Types:**
- `feat` - Fitur baru
- `fix` - Bug fix
- `style` - Perubahan styling (CSS)
- `refactor` - Refactoring code
- `perf` - Performance improvement
- `test` - Menambah test
- `docs` - Dokumentasi
- `db` - Database changes

---

### 🚨 Mengatasi Konflik saat Merge

#### Scenario: Frontend & Backend Mengubah File yang Sama

**Contoh:** Kedua tim mengubah `pages/dashboard.php`

```bash
# Frontend developer mencoba merge
git checkout frontend-dev
git merge backend-dev

# CONFLICT! Git akan menampilkan:
# Auto-merging pages/dashboard.php
# CONFLICT (content): Merge conflict in pages/dashboard.php
```

**Cara Resolve:**

1. **Buka file yang conflict**
```php
<<<<<<< HEAD (frontend-dev)
<!-- Kode dari frontend -->
<div class="card-new-design">
=======
<!-- Kode dari backend -->
<div class="card-old-design">
>>>>>>> backend-dev
```

2. **Pilih atau gabungkan kode**
```php
<!-- Hasil setelah resolve -->
<div class="card-new-design">
    <!-- Gabungkan yang terbaik dari kedua versi -->
</div>
```

3. **Commit hasil resolve**
```bash
git add pages/dashboard.php
git commit -m "merge: Resolve conflict in dashboard.php"
git push origin frontend-dev
```

---

### 💡 Best Practices Kolaborasi

#### 1. Komunikasi Sebelum Coding
```bash
# Sebelum mulai fitur baru, diskusikan dengan tim:
# - File mana yang akan diubah?
# - Apakah ada yang sedang mengerjakan file yang sama?
# - API apa yang dibutuhkan?
```

#### 2. Pull Request Review
- **Frontend PR** → Review oleh: Frontend Lead + Backend Dev (untuk API integration)
- **Backend PR** → Review oleh: Backend Lead + Frontend Dev (untuk data structure)

#### 3. Jangan Langsung Push ke Main
```bash
# ❌ JANGAN
git checkout main
git commit -m "quick fix"
git push origin main

# ✅ LAKUKAN
git checkout -b hotfix-urgent-bug
git commit -m "fix: urgent bug in payment"
git push origin hotfix-urgent-bug
# Buat PR → Review → Merge
```

#### 4. Sync Rutin
```bash
# Setiap pagi dan sebelum mulai fitur baru
git checkout frontend-dev  # atau backend-dev
git pull origin frontend-dev
git pull origin main
```

#### 5. Dokumentasi API
Backend developer harus mendokumentasikan setiap API endpoint:

```markdown
## API: Get Customers
**Endpoint:** `/api/customers-api.php?action=list`
**Method:** GET
**Response:**
{
  "success": true,
  "data": [...]
}
```

---

### 📊 Workflow Merge ke Production (Main)

```bash
# 1. Frontend Lead merge frontend-dev ke main
git checkout main
git pull origin main
git merge frontend-dev
git push origin main

# 2. Backend Lead merge backend-dev ke main
git checkout main
git pull origin main
git merge backend-dev
# Resolve conflicts jika ada
git push origin main

# 3. Tag release
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

---

### 🎯 Checklist Kolaborasi

**Sebelum Mulai Kerja:**
- [ ] Pull latest dari branch dev (`git pull origin frontend-dev`)
- [ ] Pull latest dari main (`git pull origin main`)
- [ ] Buat branch fitur baru (`git checkout -b feature-xxx`)

**Saat Bekerja:**
- [ ] Commit berkala (setiap 1-2 jam)
- [ ] Gunakan commit message yang jelas
- [ ] Test perubahan sebelum commit

**Sebelum Push:**
- [ ] Pull latest lagi untuk avoid conflict
- [ ] Test sekali lagi
- [ ] Push ke branch fitur (`git push origin feature-xxx`)

**Setelah Fitur Selesai:**
- [ ] Buat Pull Request di GitHub
- [ ] Request review dari team lead
- [ ] Tunggu approval
- [ ] Merge ke branch dev
- [ ] Hapus branch fitur lokal

---

## 📚 Referensi & Belajar Lebih Lanjut

### Dokumentasi Resmi
- [Git Documentation](https://git-scm.com/doc)
- [GitHub Guides](https://guides.github.com/)

### Tutorial Interaktif
- [Learn Git Branching](https://learngitbranching.js.org/)
- [GitHub Learning Lab](https://lab.github.com/)

### Cheat Sheet
- [Git Cheat Sheet (PDF)](https://education.github.com/git-cheat-sheet-education.pdf)

---

## ✅ Quick Start Checklist

Untuk project **Laundry D'four** ini:

- [ ] Install Git
- [ ] Konfigurasi user.name dan user.email
- [ ] `git init` di folder project
- [ ] `git add .`
- [ ] `git commit -m "Initial commit: Laundry D'four Management System"`
- [ ] Buat repository di GitHub
- [ ] `git remote add origin <url>`
- [ ] `git branch -M main`
- [ ] `git push -u origin main`
- [ ] Verifikasi di GitHub

---

## 🎓 Latihan

### Latihan 1: First Commit
1. Inisialisasi Git di project ini
2. Commit semua file dengan pesan "Initial commit"
3. Push ke GitHub

### Latihan 2: Membuat Perubahan
1. Edit file `README.md`, tambahkan nama Anda
2. `git add README.md`
3. `git commit -m "Add author name to README"`
4. `git push`

### Latihan 3: Bekerja dengan Branch
1. Buat branch baru: `git checkout -b feature-test`
2. Buat file baru `test.txt`
3. Commit file tersebut
4. Kembali ke main: `git checkout main`
5. Merge branch: `git merge feature-test`

---

**Selamat belajar Git & GitHub! 🚀**

*Dibuat untuk project Laundry D'four Management System*
*Tanggal: 15 Desember 2025*
