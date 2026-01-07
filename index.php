<?php
/**
 * Landing Page - D'four Laundry
 * Halaman utama dengan info layanan dan lokasi
 */
require_once __DIR__ . '/includes/functions.php';
$pageTitle = "D'four Smart Laundry System";
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="D'four Laundry - Layanan laundry profesional dengan harga terjangkau">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit min-h-screen relative overflow-x-hidden">

    <!-- Bubble Decorations -->
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
        <div class="bubble bubble-blue w-72 h-72 bottom-20 left-1/4"></div>
        <div class="bubble bubble-cyan w-64 h-64 bottom-10 right-1/3"></div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </div>
                
                <!-- Nav Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-600 hover:text-primary-600 transition-colors">Home</a>
                    <a href="#services" class="text-gray-600 hover:text-primary-600 transition-colors">Layanan</a>
                    <a href="#location" class="text-gray-600 hover:text-primary-600 transition-colors">Lokasi</a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="<?= baseUrl('pages/auth/login.php') ?>" class="text-gray-600 hover:text-primary-600 font-medium transition-colors">
                        Login
                    </a>
                    <a href="<?= baseUrl('pages/auth/register.php') ?>" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded-xl font-medium transition-all shadow-lg hover:shadow-xl">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative z-10 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-primary-600 font-medium mb-4 uppercase tracking-wider">Layanan Laundry Profesional</p>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                Cucian Bersih, <br>
                <span class="text-primary-600">Hidup Lebih Mudah!</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-10">
                Hemat waktu Anda dengan layanan laundry terpercaya. Kami cuci, setrika, dan antar cucian Anda dengan cepat dan berkualitas.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#services" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all shadow-xl hover:shadow-2xl">
                    Lihat Layanan
                </a>
                <a href="<?= baseUrl('pages/check-order.php') ?>" class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-2xl font-semibold transition-all shadow-lg border border-gray-200">
                    Cek Status Order
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="relative z-10 py-20 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-primary-600 font-medium mb-2">LAYANAN KAMI</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Pilih Layanan Sesuai Kebutuhan</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="machine-card text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cuci Reguler</h3>
                    <p class="text-gray-600 mb-4">Layanan cuci standar dengan hasil bersih dan wangi. Cocok untuk pakaian sehari-hari.</p>
                    <p class="text-2xl font-bold text-primary-600">Rp 7.000<span class="text-sm text-gray-500">/kg</span></p>
                </div>
                
                <!-- Service 2 -->
                <div class="machine-card text-center border-2 border-primary-200 bg-primary-50/50">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary-600 text-white text-xs font-bold px-3 py-1 rounded-full">POPULER</span>
                    </div>
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6 mt-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cuci + Setrika</h3>
                    <p class="text-gray-600 mb-4">Paket lengkap cuci dan setrika. Pakaian siap pakai tanpa repot!</p>
                    <p class="text-2xl font-bold text-primary-600">Rp 10.000<span class="text-sm text-gray-500">/kg</span></p>
                </div>
                
                <!-- Service 3 -->
                <div class="machine-card text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Express (1 Hari)</h3>
                    <p class="text-gray-600 mb-4">Butuh cepat? Layanan express selesai dalam 24 jam!</p>
                    <p class="text-2xl font-bold text-primary-600">Rp 15.000<span class="text-sm text-gray-500">/kg</span></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Section with Map -->
    <section id="location" class="relative z-10 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-primary-600 font-medium mb-2">LOKASI KAMI</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Temukan Kami</h2>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Map -->
                <div class="machine-card p-0 overflow-hidden h-80 lg:h-96">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.194741299999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e67356080477!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1640000000000!5m2!1sid!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
                <!-- Contact Info -->
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Alamat</h4>
                            <p class="text-gray-600">Jl. Contoh No. 123, Kota Anda, Indonesia</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Telepon / WhatsApp</h4>
                            <p class="text-gray-600">+62 812-3456-7890</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Jam Operasional</h4>
                            <p class="text-gray-600">Senin - Sabtu: 08.00 - 20.00</p>
                            <p class="text-gray-600">Minggu: 09.00 - 17.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">D</span>
                </div>
                <span class="text-xl font-bold">D'four Laundry</span>
            </div>
            <p class="text-gray-400 mb-4">Layanan laundry terpercaya untuk keluarga Anda</p>
            <p class="text-gray-500 text-sm">&copy; <?= date('Y') ?> D'four Laundry. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>