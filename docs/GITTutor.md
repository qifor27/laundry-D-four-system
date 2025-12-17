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
