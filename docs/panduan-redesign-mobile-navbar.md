# 📱 Panduan Redesign Landing Page - Mobile Bottom Navigation

## Referensi Screenshot
![Current Design](C:/Users/TOSHIBA/.gemini/antigravity/brain/e39cb891-a9c4-4e95-bb94-1383fc452960/uploaded_image_1768712334484.png)

---

## 🎯 Tujuan Perubahan

1. **Mobile Bottom Navbar** - Navbar menjadi floating bar di bawah saat mobile
2. **Icon-Based Navigation** - Item navbar diwakilkan oleh ikon
3. **Load Animation** - Animasi saat halaman pertama kali dimuat
4. **Enhanced Card Shadow** - Bayangan lebih tegas pada card layanan

---

## 1️⃣ MOBILE BOTTOM NAVIGATION

### A. Struktur HTML Baru

Tambahkan komponen bottom navbar setelah navbar utama (sekitar line 88 di `index.php`):

```html
<!-- Mobile Bottom Navigation - Hanya tampil di mobile -->
<nav class="md:hidden fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 bg-white/95 backdrop-blur-lg shadow-2xl rounded-full px-6 py-3">
    <div class="flex items-center space-x-6">
        <!-- Home -->
        <a href="#home" class="flex flex-col items-center text-primary-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-xs mt-1">Home</span>
        </a>
        
        <!-- Layanan -->
        <a href="#services" class="flex flex-col items-center text-gray-500 hover:text-primary-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            <span class="text-xs mt-1">Layanan</span>
        </a>
        
        <!-- Lokasi -->
        <a href="#location" class="flex flex-col items-center text-gray-500 hover:text-primary-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-xs mt-1">Lokasi</span>
        </a>
        
        <!-- Login -->
        <a href="<?= baseUrl('pages/auth/login.php') ?>" class="flex flex-col items-center text-gray-500 hover:text-primary-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-xs mt-1">Login</span>
        </a>
    </div>
</nav>
```

### B. Sembunyikan Navbar Atas di Mobile

Ubah navbar utama (line 62-87) untuk hanya tampil di desktop:

**SEBELUM:**
```html
<nav class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-white/95 backdrop-blur-lg shadow-xl rounded-full">
```

**SESUDAH:**
```html
<nav class="hidden md:block fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-white/95 backdrop-blur-lg shadow-xl rounded-full">
```

### C. Class Tailwind yang Digunakan

| Class | Fungsi |
|-------|--------|
| `md:hidden` | Sembunyikan di layar >= 768px |
| `hidden md:block` | Sembunyikan di mobile, tampil di desktop |
| `fixed bottom-4` | Posisi fixed, 1rem dari bawah |
| `left-1/2 -translate-x-1/2` | Center horizontal |
| `backdrop-blur-lg` | Efek blur background |
| `shadow-2xl` | Shadow paling besar |
| `rounded-full` | Border radius penuh (pill shape) |

---

## 2️⃣ ANIMASI LOAD HALAMAN

### A. Tambahkan Custom CSS Animation

Tambahkan di dalam tag `<style>` atau di file CSS:

```html
<style>
    /* Fade In Up Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Fade In Animation */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Scale In Animation */
    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Animation Classes */
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    .animate-fade-in {
        animation: fadeIn 1s ease-out forwards;
    }
    
    .animate-scale-in {
        animation: scaleIn 0.6s ease-out forwards;
    }
    
    /* Delay Classes */
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }
    
    /* Initial state for animated elements */
    .animate-on-load {
        opacity: 0;
    }
</style>
```

### B. Terapkan Animasi ke Elemen Hero Section

**Tagline (LAYANAN LAUNDRY PROFESIONAL):**
```html
<p class="text-primary-600 font-medium mb-4 uppercase tracking-wider animate-on-load animate-fade-in-up">
```

**Heading Utama:**
```html
<h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 animate-on-load animate-fade-in-up delay-200">
```

**Deskripsi:**
```html
<p class="text-gray-600 text-lg max-w-2xl mx-auto mb-10 animate-on-load animate-fade-in-up delay-300">
```

**Buttons:**
```html
<div class="flex flex-col sm:flex-row gap-4 justify-center animate-on-load animate-fade-in-up delay-400">
```

### C. Alternatif: Tailwind Built-in Animation

Jika tidak ingin custom CSS, gunakan built-in Tailwind:

```html
<!-- Dengan animate-pulse atau animate-bounce -->
<h1 class="... animate-[fadeIn_1s_ease-out]">
```

Atau tambahkan di `tailwind.config.js`:

```javascript
module.exports = {
    theme: {
        extend: {
            animation: {
                'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                'fade-in': 'fadeIn 1s ease-out forwards',
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
            },
        },
    },
}
```

Lalu gunakan:
```html
<h1 class="animate-fade-in-up">Cucian Bersih...</h1>
```

---

## 3️⃣ ENHANCED CARD SHADOW

### A. Update Service Cards

**SEBELUM (machine-card biasa):**
```html
<div class="machine-card text-center">
```

**SESUDAH (dengan shadow yang lebih tegas):**
```html
<div class="machine-card text-center shadow-xl hover:shadow-2xl transition-shadow duration-300 border border-gray-100">
```

### B. Class Shadow Tailwind

| Class | Ukuran Shadow |
|-------|---------------|
| `shadow` | Kecil (default) |
| `shadow-md` | Medium |
| `shadow-lg` | Large |
| `shadow-xl` | Extra Large |
| `shadow-2xl` | 2X Extra Large (paling besar) |

### C. Custom Shadow dengan Warna

Untuk shadow dengan warna purple (sesuai theme):

```html
<div class="machine-card text-center shadow-[0_10px_40px_-10px_rgba(139,92,246,0.3)] hover:shadow-[0_20px_60px_-10px_rgba(139,92,246,0.4)] transition-all duration-300">
```

### D. Contoh Card dengan Semua Enhancement

```html
<!-- Service Card dengan Shadow + Animation -->
<div class="machine-card text-center 
            shadow-xl hover:shadow-2xl 
            border border-gray-100
            transition-all duration-300 
            hover:-translate-y-2
            animate-on-load animate-scale-in delay-200">
    <!-- content -->
</div>
```

---

## 🎨 CONTOH LENGKAP: HERO SECTION DENGAN ANIMASI

```html
<!-- Hero Section -->
<section id="home" class="relative z-10 py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        
        <!-- Tagline dengan animasi -->
        <p class="text-primary-600 font-medium mb-4 uppercase tracking-wider 
                  animate-on-load animate-fade-in-up">
            Layanan Laundry Profesional
        </p>
        
        <!-- Heading dengan animasi delay -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 
                   animate-on-load animate-fade-in-up delay-200">
            Cucian Bersih, <br>
            <span class="text-primary-600">Hidup Lebih Mudah!</span>
        </h1>
        
        <!-- Deskripsi dengan animasi delay lebih lama -->
        <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-10 
                  animate-on-load animate-fade-in-up delay-300">
            Hemat waktu Anda dengan layanan laundry terpercaya...
        </p>
        
        <!-- Buttons dengan animasi terakhir -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center 
                    animate-on-load animate-fade-in-up delay-400">
            <a href="#services" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                Lihat Layanan
            </a>
            <a href="<?= baseUrl('pages/auth/login.php') ?>" class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-2xl font-semibold transition-all shadow-lg border border-gray-200 hover:-translate-y-1">
                Login Pelanggan
            </a>
        </div>
        
    </div>
</section>
```

---

## 📋 CHECKLIST IMPLEMENTASI

- [ ] Tambahkan `<style>` dengan keyframes animation di `<head>`
- [ ] Ubah navbar utama: tambah `hidden md:block`
- [ ] Tambah komponen Mobile Bottom Navigation
- [ ] Tambah class animasi ke Hero Section elements
- [ ] Update Service Cards dengan `shadow-xl hover:shadow-2xl`
- [ ] Test di browser dengan responsive mode (F12 → Toggle Device)
- [ ] Run `npm run build` untuk compile Tailwind

---

## 🔧 TIPS

1. **Testing Mobile**: Tekan F12 di browser, lalu klik icon device untuk simulasi mobile
2. **Breakpoint Tailwind**: 
   - `sm:` = 640px
   - `md:` = 768px (tablet)
   - `lg:` = 1024px
   - `xl:` = 1280px
3. **Jangan lupa rebuild Tailwind** setelah edit jika ada class baru yang belum tersedia

---

*Panduan ini menggunakan 100% Tailwind CSS dengan sedikit custom CSS untuk animasi*
