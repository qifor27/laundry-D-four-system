



# 📊 Customer Dashboard Redesign Guide

**Branch:** `feature/frontend-customer-dashboard`  
**Approach:** 100% Tailwind CSS Classes + Components

---

## 📋 Komponen CSS yang Tersedia (di `input.css`)

| Class | Fungsi |
|-------|--------|
| `.glass-card` | Card dengan frosted glass effect |
| `.bubble-decoration` | Container untuk background bubbles |
| `.bubble` + `.bubble-pink/purple/blue` | Bubble warna-warni |
| `.dash-stat-card` | Stat card dengan gradient |
| `.dash-transaction-item` | Item transaksi dengan glass effect |
| `.dash-quick-link` | Quick link card dengan hover scale |

---

## 📄 File: `pages/customer-dashboard.php`

### Step 1: Body + Bubble Decorations

```php
<!-- BEFORE -->
<body class="bg-gray-50 font-outfit min-h-screen">

<!-- AFTER -->
<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit min-h-screen">

    <!-- Bubble Decorations -->
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
        <div class="bubble bubble-blue w-72 h-72 bottom-20 left-1/4"></div>
    </div>
```

---

### Step 2: Header Glassmorphism

```php
<!-- BEFORE -->
<header class="bg-white shadow-sm sticky top-0 z-50">

<!-- AFTER -->
<header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
```

---

### Step 3: Main dengan z-index

```php
<!-- BEFORE -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

<!-- AFTER -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
```

---

### Step 4: Stat Cards

```php
<!-- BEFORE -->
<div class="card bg-gradient-to-br from-primary-500 to-primary-600 text-white">

<!-- AFTER - Pesanan Aktif -->
<div class="dash-stat-card bg-gradient-to-br from-primary-500 to-primary-600">

<!-- AFTER - Selesai -->
<div class="dash-stat-card bg-gradient-to-br from-secondary-500 to-secondary-600">

<!-- AFTER - Total Pesanan -->
<div class="dash-stat-card bg-gradient-to-br from-purple-500 to-purple-600">
```

---

### Step 5: Riwayat Pesanan Card

```php
<!-- BEFORE -->
<div class="card">

<!-- AFTER -->
<div class="glass-card">
```

---

### Step 6: Transaction Items

```php
<!-- BEFORE -->
<div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors">

<!-- AFTER -->
<div class="dash-transaction-item">
```

---

### Step 7: Quick Links

```php
<!-- BEFORE -->
<a href="..." class="card hover:scale-[1.02] transition-transform flex items-center gap-4">

<!-- AFTER -->
<a href="..." class="dash-quick-link">
```

---

### Step 8: Footer

```php
<!-- BEFORE -->
<footer class="bg-white border-t border-gray-200 py-6 mt-8">

<!-- AFTER -->
<footer class="bg-white/50 backdrop-blur-sm border-t border-white/50 py-6 mt-8 relative z-10">
```

---

## ✅ Checklist

- [ ] Body background + bubbles
- [ ] Header glassmorphism
- [ ] Main z-index
- [ ] Stat cards → `dash-stat-card`
- [ ] Riwayat pesanan → `glass-card`
- [ ] Transaction items → `dash-transaction-item`
- [ ] Quick links → `dash-quick-link`
- [ ] Footer glass effect

---

## 🚀 Git Commands

```bash
# Build CSS
npm run build

# Commit & Push
git add assets/css/input.css pages/customer-dashboard.php
git commit -m "style: redesign customer dashboard with glassmorphism"
git push origin feature/frontend-customer-dashboard

# Buat PR: feature/frontend-customer-dashboard → develop
```
