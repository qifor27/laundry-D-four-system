# 🎨 Design System Implementation Guide

**Branch:** `feature/frontend-design-system`  
**Author:** Team Lead Frontend  
**Date:** 2026-01-07

> Panduan lengkap untuk mengimplementasikan design system baru berdasarkan inspirasi design. Ketik sendiri kode di bawah ini untuk memahami setiap bagian.

---

## 📋 Daftar File yang Perlu Diubah/Ditambah

| No | File | Aksi | Keterangan |
|----|------|------|------------|
| 1 | `tailwind.config.js` | MODIFY | Update color palette |
| 2 | `assets/css/input.css` | MODIFY | Tambah komponen baru |
| 3 | `includes/header.php` | MODIFY | Tambah link font Google |

---

## 1️⃣ Update `tailwind.config.js`

### Lokasi: `e:\xampp2\htdocs\laundry-D-four\tailwind.config.js`

### Penjelasan:
Kita perlu menambahkan warna baru sesuai dengan design inspiration:
- **Purple Pink Gradient** untuk User Dashboard
- **Royal Blue** untuk Login/Register
- **Pink Purple** untuk Admin Dashboard

### Kode Lengkap (Ganti Seluruh Isi File):

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      // ========================================
      // FONT FAMILY
      // ========================================
      fontFamily: {
        'outfit': ['Outfit', 'sans-serif'],
        'inter': ['Inter', 'sans-serif'],
      },

      // ========================================
      // COLOR PALETTE
      // Berdasarkan Design Inspiration
      // ========================================
      colors: {
        // PRIMARY - Purple (untuk accent utama)
        primary: {
          50: '#faf5ff',
          100: '#f3e8ff',
          200: '#e9d5ff',
          300: '#d8b4fe',
          400: '#c084fc',
          500: '#a855f7',
          600: '#9333ea',   // Default primary
          700: '#7e22ce',
          800: '#6b21a8',
          900: '#581c87',
        },

        // SECONDARY - Green (untuk success states)
        secondary: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',   // Default secondary
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
        },


        // ACCENT - Pink (untuk gradients & highlights)
        accent: {
          50: '#fdf2f8',
          100: '#fce7f3',
          200: '#fbcfe8',
          300: '#f9a8d4',
          400: '#f472b6',
          500: '#ec4899',   // Default accent
          600: '#db2777',
          700: '#be185d',
          800: '#9d174d',
          900: '#831843',
        },

        // ROYAL BLUE - Untuk Login/Register
        royal: {
          50: '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',   // Default royal (Login button)
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
        },

        // DARK - Untuk text
        dark: {
          50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937',
          900: '#111827',
        },

        // BUBBLE COLORS - Untuk decorations
        bubble: {
          pink: '#fce7f3',
          purple: '#e9d5ff',
          blue: '#dbeafe',
          cyan: '#cffafe',
        },

        // GLASS - Untuk glassmorphism
        glass: {
          white: 'rgba(255, 255, 255, 0.7)',
          border: 'rgba(255, 255, 255, 0.3)',
          dark: 'rgba(0, 0, 0, 0.1)',
        }
      },

      // ========================================
      // BORDER RADIUS
      // Design menggunakan rounded besar
      // ========================================
      borderRadius: {
        'xl': '12px',
        '2xl': '16px',
        '3xl': '24px',
        '4xl': '32px',
      },

      // ========================================
      // BOX SHADOW
      // Soft shadows untuk cards
      // ========================================
      boxShadow: {
        'glass': '0 8px 32px rgba(0, 0, 0, 0.1)',
        'glass-lg': '0 12px 48px rgba(0, 0, 0, 0.15)',
        'soft': '0 4px 20px rgba(0, 0, 0, 0.08)',
        'soft-lg': '0 8px 30px rgba(0, 0, 0, 0.12)',
        'glow-purple': '0 10px 40px rgba(124, 58, 237, 0.3)',
        'glow-pink': '0 10px 40px rgba(236, 72, 153, 0.3)',
      },

      // ========================================
      // BACKDROP BLUR
      // Untuk glassmorphism effect
      // ========================================
      backdropBlur: {
        'xs': '2px',
        'glass': '10px',
      },

      // ========================================
      // ANIMATIONS
      // ========================================
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'float': 'float 20s ease-in-out infinite',
        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0) scale(1)' },
          '50%': { transform: 'translateY(-20px) scale(1.05)' },
        },
        pulseSoft: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.7' },
        }
      },

      // ========================================
      // GRADIENT BACKGROUNDS
      // Custom gradients untuk backgrounds
      // ========================================
      backgroundImage: {
        // User Dashboard background
        'gradient-user': 'linear-gradient(135deg, #FFB6F3 0%, #C8B6FF 50%, #A5B4FC 100%)',
        // Admin sidebar gradient
        'gradient-admin': 'linear-gradient(180deg, #EC4899 0%, #8B5CF6 100%)',
        // Primary button gradient
        'gradient-primary': 'linear-gradient(135deg, #7C3AED 0%, #EC4899 100%)',
        // Stats card decorative blob
        'gradient-blob': 'linear-gradient(135deg, #C8B6FF 0%, #FFB6F3 100%)',
      },
    },
  },
  plugins: [],
}
```

---

## 2️⃣ Update `assets/css/input.css`

### Lokasi: `e:\xampp2\htdocs\laundry-D-four\assets\css\input.css`

### Penjelasan:
Menambahkan komponen baru berdasarkan design inspiration:
- Glassmorphism cards
- Gradient buttons
- Underline input fields (untuk login)
- Stats cards
- Admin sidebar

### Kode Lengkap (Ganti Seluruh Isi File):

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* =========================================
   BASE STYLES
   ========================================= */
@layer base {
  body {
    @apply antialiased font-outfit;
  }

  html {
    scroll-behavior: smooth;
  }

  /* Default heading styles with large typography */
  h1 {
    @apply text-3xl md:text-4xl font-bold text-dark-900;
  }

  h2 {
    @apply text-xl md:text-2xl font-semibold text-dark-800;
  }

  h3 {
    @apply text-lg font-medium text-dark-700;
  }
}

/* =========================================
   COMPONENT STYLES
   ========================================= */
@layer components {

  /* -----------------------------------------
     GLASSMORPHISM CARD
     Style utama dari design inspiration
     ----------------------------------------- */
  .glass-card {
    @apply bg-white/70 backdrop-blur-[10px] border border-white/30 rounded-3xl shadow-glass p-6;
    transition: all 0.3s ease;
  }

  .glass-card:hover {
    @apply shadow-glass-lg;
    transform: translateY(-4px);
  }

  /* Glass card - dark variant */
  .glass-card-dark {
    @apply bg-dark-900/70 backdrop-blur-[10px] border border-white/10 rounded-3xl shadow-glass p-6 text-white;
  }

  /* -----------------------------------------
     CARDS - Standard
     ----------------------------------------- */
  .card {
    @apply bg-white rounded-3xl shadow-soft p-6 transition-all duration-200;
  }

  .card:hover {
    @apply shadow-soft-lg;
  }

  .card-gradient {
    @apply bg-gradient-primary rounded-3xl p-6 text-white shadow-glow-purple;
  }

  /* -----------------------------------------
     STATS CARD
     Untuk dashboard statistics
     ----------------------------------------- */
  .stat-card {
    @apply relative bg-white rounded-3xl p-6 shadow-soft overflow-hidden;
  }

  .stat-card::after {
    content: '';
    @apply absolute -top-5 -right-5 w-24 h-24 bg-gradient-blob rounded-full opacity-50;
  }

  .stat-number {
    @apply text-4xl md:text-5xl font-bold text-dark-900;
  }

  .stat-label {
    @apply text-sm text-dark-500 mt-2;
  }

  /* -----------------------------------------
     BUTTONS - Gradient Style
     ----------------------------------------- */
  .btn-gradient {
    @apply bg-gradient-primary text-white font-semibold py-3 px-6 rounded-xl;
    @apply transition-all duration-200;
    @apply hover:shadow-glow-purple hover:-translate-y-0.5;
  }

  /* Primary button - standard */
  .btn-primary {
    @apply bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-xl;
    @apply transition-all duration-200 shadow-soft hover:shadow-soft-lg;
    @apply hover:-translate-y-0.5;
  }

  /* Secondary button */
  .btn-secondary {
    @apply bg-dark-200 hover:bg-dark-300 text-dark-800 font-medium py-2.5 px-6 rounded-xl;
    @apply transition-all duration-200;
  }

  /* Royal Blue button - untuk login/register */
  .btn-royal {
    @apply bg-royal-600 hover:bg-royal-700 text-white font-medium py-3.5 px-8;
    @apply rounded-full transition-all duration-200;
    @apply hover:shadow-lg hover:-translate-y-0.5;
  }

  /* Success button */
  .btn-success {
    @apply bg-secondary-600 hover:bg-secondary-700 text-white font-medium py-2.5 px-6 rounded-xl;
    @apply transition-all duration-200;
  }

  /* Danger button */
  .btn-danger {
    @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-6 rounded-xl;
    @apply transition-all duration-200;
  }

  /* -----------------------------------------
     FORM ELEMENTS - Standard
     ----------------------------------------- */
  .form-input {
    @apply w-full px-4 py-3 border border-dark-200 rounded-xl;
    @apply focus:ring-2 focus:ring-primary-500 focus:border-transparent;
    @apply transition-all duration-200 bg-white;
  }

  .form-label {
    @apply block text-sm font-medium text-dark-700 mb-2;
  }

  /* -----------------------------------------
     FORM ELEMENTS - Underline Style
     Untuk halaman Login/Register
     ----------------------------------------- */
  .input-underline {
    @apply w-full bg-transparent border-0 border-b-2 border-dark-200 py-3 px-0;
    @apply text-dark-800 placeholder-dark-400;
    @apply focus:border-royal-600 focus:ring-0 focus:outline-none;
    @apply transition-all duration-300;
  }

  .input-underline:focus {
    border-bottom-color: #4f46e5;
  }

  /* Floating label effect */
  .input-group {
    @apply relative;
  }

  .input-group .floating-label {
    @apply absolute left-0 top-3 text-dark-400 transition-all duration-300 pointer-events-none;
  }

  .input-group .input-underline:focus ~ .floating-label,
  .input-group .input-underline:not(:placeholder-shown) ~ .floating-label {
    @apply -top-4 text-xs text-royal-600;
  }

  /* -----------------------------------------
     ADMIN SIDEBAR
     Gradient sidebar untuk admin
     ----------------------------------------- */
  .admin-sidebar {
    @apply bg-gradient-admin min-h-screen w-20 py-6 px-3;
    @apply flex flex-col items-center rounded-r-4xl;
  }

  .sidebar-icon {
    @apply w-12 h-12 flex items-center justify-center;
    @apply bg-white/20 rounded-2xl text-white mb-4;
    @apply transition-all duration-200 cursor-pointer;
  }

  .sidebar-icon:hover,
  .sidebar-icon.active {
    @apply bg-white/40;
  }

  /* -----------------------------------------
     STATUS BADGES
     ----------------------------------------- */
  .badge {
    @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
  }

  .badge-success {
    @apply bg-secondary-100 text-secondary-700;
  }

  .badge-warning {
    @apply bg-amber-100 text-amber-700;
  }

  .badge-danger {
    @apply bg-red-100 text-red-700;
  }

  .badge-info {
    @apply bg-royal-100 text-royal-700;
  }

  .badge-purple {
    @apply bg-primary-100 text-primary-700;
  }

  /* -----------------------------------------
     MODAL
     ----------------------------------------- */
  .modal-overlay {
    @apply fixed inset-0 bg-black/50 z-40 backdrop-blur-sm;
  }

  .modal-content {
    @apply fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2;
    @apply bg-white rounded-3xl shadow-glass-lg z-50 w-full max-w-md p-6;
  }

  /* -----------------------------------------
     TABLE
     ----------------------------------------- */
  .table-wrapper {
    @apply overflow-x-auto rounded-2xl border border-dark-100 bg-white;
  }

  .table {
    @apply w-full text-sm text-left;
  }

  .table thead {
    @apply bg-dark-50 text-dark-600 uppercase text-xs font-semibold;
  }

  .table th {
    @apply px-6 py-4;
  }

  .table td {
    @apply px-6 py-4 border-b border-dark-100;
  }

  .table tbody tr {
    @apply hover:bg-dark-50 transition-colors duration-150;
  }

  /* -----------------------------------------
     NAVIGATION TABS
     Untuk dashboard navigation
     ----------------------------------------- */
  .nav-tabs {
    @apply flex items-center bg-white/50 backdrop-blur-sm rounded-full p-1;
  }

  .nav-tab {
    @apply px-5 py-2 rounded-full text-sm font-medium text-dark-500;
    @apply transition-all duration-200 cursor-pointer;
  }

  .nav-tab:hover {
    @apply text-dark-700;
  }

  .nav-tab.active {
    @apply bg-white text-dark-900 shadow-soft;
  }

  /* -----------------------------------------
     AVATAR
     ----------------------------------------- */
  .avatar {
    @apply w-10 h-10 rounded-full object-cover border-2 border-white shadow-soft;
  }

  .avatar-lg {
    @apply w-14 h-14 rounded-full object-cover border-2 border-white shadow-soft;
  }

  /* -----------------------------------------
     CUSTOMER LIST ITEM
     Untuk admin panel kanan
     ----------------------------------------- */
  .customer-item {
    @apply flex items-center gap-3 p-3 rounded-xl hover:bg-dark-50;
    @apply transition-all duration-200 cursor-pointer;
  }

  .customer-item .customer-name {
    @apply font-medium text-dark-800;
  }

  .customer-item .customer-location {
    @apply text-sm text-dark-400;
  }
}

/* =========================================
   UTILITY CLASSES
   ========================================= */
@layer utilities {
  /* Gradient text */
  .gradient-text {
    @apply bg-gradient-to-r from-primary-600 to-accent-500 bg-clip-text text-transparent;
  }

  /* Glass effect utility */
  .glass-effect {
    @apply bg-white/70 backdrop-blur-[10px] border border-white/30;
  }

  /* Center absolutely */
  .center-absolute {
    @apply absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2;
  }
}

/* =========================================
   BACKGROUND DECORATIONS
   Bubble & Gradient Blobs
   ========================================= */
.bubble-decoration {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
  overflow: hidden;
}

.bubble {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.6;
  animation: float 20s ease-in-out infinite;
}

.bubble-pink {
  background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
}

.bubble-purple {
  background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
}

.bubble-blue {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
}

.bubble-cyan {
  background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
}

/* Gradient blob decoration */
.gradient-blob {
  @apply absolute rounded-full opacity-30;
  background: linear-gradient(135deg, #C8B6FF 0%, #FFB6F3 100%);
  filter: blur(40px);
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) scale(1);
  }
  50% {
    transform: translateY(-20px) scale(1.05);
  }
}

/* =========================================
   PAGE BACKGROUNDS
   ========================================= */

/* User Dashboard Background */
.bg-user-dashboard {
  @apply min-h-screen;
  background: linear-gradient(135deg, #FFB6F3 0%, #C8B6FF 50%, #A5B4FC 100%);
}

/* Admin Dashboard Background */
.bg-admin-dashboard {
  @apply min-h-screen bg-accent-50;
}

/* Login/Register Background */
.bg-auth {
  @apply min-h-screen bg-dark-50;
}

/* =========================================
   PRINT STYLES
   ========================================= */
@media print {
  .no-print {
    display: none !important;
  }

  .print-full-width {
    width: 100% !important;
  }

  body {
    @apply text-black bg-white;
  }

  .card,
  .glass-card,
  .modal-content {
    @apply shadow-none border border-dark-200;
  }
}

/* =========================================
   MOBILE RESPONSIVE
   ========================================= */
@media (max-width: 640px) {
  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .card,
  .glass-card {
    @apply p-4 rounded-2xl;
  }

  .modal-content {
    @apply w-[95vw] max-h-[90vh] overflow-y-auto;
  }

  .stat-number {
    @apply text-3xl;
  }

  .admin-sidebar {
    @apply w-16 px-2;
  }

  .sidebar-icon {
    @apply w-10 h-10;
  }
}
```

---

## 3️⃣ Update `includes/header.php`

### Lokasi: `e:\xampp2\htdocs\laundry-D-four\includes\header.php`

### Penjelasan:
Menambahkan Google Fonts (Inter sebagai secondary font) dan Lucide Icons.

### Cari Bagian `<head>` dan Tambahkan:

```html
<!-- Google Fonts - Outfit & Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Lucide Icons (Modern line icons) -->
<script src="https://unpkg.com/lucide@latest"></script>
```

### Inisialisasi Icons di Akhir `<body>`:

```html
<script>
  // Initialize Lucide icons
  lucide.createIcons();
</script>
```

---

## 4️⃣ Build Tailwind CSS

### Setelah selesai mengetik, jalankan perintah ini:

```bash
npm run build
```

Atau untuk development mode (auto-watch):

```bash
npm run dev
```

---

## 📝 Contoh Penggunaan Komponen

### Glassmorphism Card

```html
<div class="glass-card">
  <h3 class="text-lg font-semibold">Card Title</h3>
  <p class="text-dark-500">Card content here...</p>
</div>
```

### Stats Card

```html
<div class="stat-card">
  <p class="stat-number">1,250</p>
  <p class="stat-label">Total Orders</p>
</div>
```

### Gradient Button

```html
<button class="btn-gradient">
  Get Started
</button>
```

### Royal Blue Button (Login)

```html
<button class="btn-royal">
  SIGN IN
</button>
```

### Underline Input

```html
<div class="input-group">
  <input type="email" class="input-underline" placeholder=" " id="email">
  <label class="floating-label" for="email">Email</label>
</div>
```

### Admin Sidebar

```html
<nav class="admin-sidebar">
  <div class="sidebar-icon active">
    <i data-lucide="home"></i>
  </div>
  <div class="sidebar-icon">
    <i data-lucide="bar-chart-2"></i>
  </div>
  <div class="sidebar-icon">
    <i data-lucide="users"></i>
  </div>
</nav>
```

### Page Background

```html
<!-- User Dashboard -->
<body class="bg-user-dashboard">
  ...
</body>

<!-- Admin Dashboard -->
<body class="bg-admin-dashboard">
  ...
</body>

<!-- Login/Register -->
<body class="bg-auth">
  ...
</body>
```

---

## ✅ Checklist Selesai

Setelah selesai mengetik semua:

- [ ] Update `tailwind.config.js`
- [ ] Update `assets/css/input.css`
- [ ] Update `includes/header.php`
- [ ] Run `npm run build`
- [ ] Test di browser
- [ ] Commit changes

### Git Commands:

```bash
# Lihat perubahan
git status

# Add semua file
git add .

# Commit dengan pesan
git commit -m "feat: implement new design system based on inspiration

- Add new color palette (purple, pink, royal blue)
- Add glassmorphism card components
- Add gradient buttons and backgrounds
- Add underline input style for auth pages
- Add admin sidebar component
- Add stats card component
- Add Lucide icons integration"

# Push ke remote
git push -u origin feature/frontend-design-system
```

---

## 📚 Referensi

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Lucide Icons](https://lucide.dev/icons/)
- [Google Fonts - Outfit](https://fonts.google.com/specimen/Outfit)
- Design Inspiration: `docs/design_inspiration.md`

---

*Happy Coding! 🚀*
