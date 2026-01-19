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

        /* Slide In From Bottom */
        @keyframes slideInBottom {
            from {
                opacity: 0;
                transform: translateY(100%);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation Classes */
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.6s ease-out forwards;
        }

        .animate-slide-in-bottom {
            animation: slideInBottom 0.5s ease-out forwards;
        }

        /* Delay Classes */
        .animation-delay-100 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .animation-delay-300 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .animation-delay-400 {
            animation-delay: 0.4s;
            opacity: 0;
        }

        .animation-delay-500 {
            animation-delay: 0.5s;
            opacity: 0;
        }

        .animation-delay-600 {
            animation-delay: 0.6s;
            opacity: 0;
        }

        .animation-delay-700 {
            animation-delay: 0.7s;
            opacity: 0;
        }

        .animation-delay-800 {
            animation-delay: 0.8s;
            opacity: 0;
        }

        /* Enhanced Card Shadow */
        .service-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            position: relative;
            box-shadow: 0 10px 40px -10px rgba(139, 92, 246, 0.15);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px -15px rgba(139, 92, 246, 0.25);
        }

        /* Bottom Nav Active State */
        .bottom-nav-item.active {
            color: #8b5cf6;
        }

        .bottom-nav-item {
            transition: all 0.2s ease;
        }

        .bottom-nav-item:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-white to-fuchsia-50 font-outfit min-h-screen relative overflow-x-hidden">

    <!-- Real Bubble Images - Dribbble Style Arrangement with Blur -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Left side - Large blurred bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-72 h-72 opacity-40 blur-sm animate-float" style="top: 10%; left: -5%;">

        <!-- Top left corner - Medium clear bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-40 h-40 opacity-60 animate-float" style="top: 5%; left: 15%; animation-delay: -4s;">

        <!-- Bottom left - Small clear bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-20 h-20 opacity-80 animate-float" style="bottom: 25%; left: 8%; animation-delay: -8s;">

        <!-- Bottom left - Medium blurred bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-48 h-48 opacity-35 blur-[2px] animate-float" style="bottom: 5%; left: 3%; animation-delay: -12s;">

        <!-- Top right - Large purple-tinted area bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-80 h-80 opacity-30 blur-sm animate-float" style="top: -10%; right: -10%; animation-delay: -6s;">

        <!-- Right side - Medium clear bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-32 h-32 opacity-65 animate-float" style="top: 40%; right: 5%; animation-delay: -10s;">

        <!-- Bottom right - Small clear bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-16 h-16 opacity-85 animate-float" style="bottom: 15%; right: 10%; animation-delay: -3s;">

        <!-- Bottom right corner - Large blurred bubble -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-64 h-64 opacity-25 blur-md animate-float" style="bottom: -10%; right: -5%; animation-delay: -15s;">

        <!-- Center scattered - Small bubbles -->
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-12 h-12 opacity-70 animate-float" style="top: 55%; left: 25%; animation-delay: -7s;">
        <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-10 h-10 opacity-75 animate-float" style="top: 75%; right: 35%; animation-delay: -9s;">
    </div>

    <!-- Desktop Navigation - Hidden on Mobile -->
    <nav class="hidden md:block fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-white/95 backdrop-blur-lg shadow-xl rounded-full">
        <div class="flex items-center h-14 px-4">
            <!-- Logo -->
            <div class="flex items-center space-x-2 mr-8">
                <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-9 h-9">
                <span class="text-lg font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
            </div>

            <!-- Nav Links -->
            <div class="flex items-center space-x-6 mr-8">
                <a href="#home" class="text-gray-600 hover:text-primary-600 transition-colors text-sm font-medium">Home</a>
                <a href="#services" class="text-gray-600 hover:text-primary-600 transition-colors text-sm font-medium">Layanan</a>
                <a href="#location" class="text-gray-600 hover:text-primary-600 transition-colors text-sm font-medium">Lokasi</a>
            </div>

            <!-- CTA Buttons -->
            <div class="flex items-center space-x-3">
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="text-gray-600 hover:text-primary-600 font-medium transition-colors text-sm">
                    Login
                </a>
                <a href="<?= baseUrl('pages/auth/register.php') ?>" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-full font-medium transition-all shadow-md hover:shadow-lg text-sm">
                    Daftar
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Navigation - Floating Pill Style -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 pb-safe">
        <div class="mx-4 mb-4 bg-white rounded-2xl px-4 py-2" style="box-shadow: 0 -5px 30px rgba(0,0,0,0.12);">
            <div class="flex items-center justify-between relative">
                <!-- Home -->
                <a href="#home" class="bottom-nav-item active flex flex-col items-center py-2 px-2 text-primary-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-[10px] mt-1 font-medium">Home</span>
                </a>

                <!-- Layanan -->
                <a href="#services" class="bottom-nav-item flex flex-col items-center py-2 px-2 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <span class="text-[10px] mt-1 font-medium">Layanan</span>
                </a>

                <!-- Center FAB Button - Daftar -->
                <a href="<?= baseUrl('pages/auth/register.php') ?>" class="absolute left-1/2 -translate-x-1/2 -top-6 flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-500 to-primary-600 rounded-full shadow-xl hover:scale-105 transition-transform" style="box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

                <!-- Spacer for FAB -->
                <div class="w-14"></div>

                <!-- Lokasi -->
                <a href="#location" class="bottom-nav-item flex flex-col items-center py-2 px-2 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-[10px] mt-1 font-medium">Lokasi</span>
                </a>

                <!-- Login -->
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="bottom-nav-item flex flex-col items-center py-2 px-2 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-[10px] mt-1 font-medium">Login</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative z-10 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Tagline with animation -->
            <p class="text-primary-600 font-medium mb-4 uppercase tracking-wider animate-fade-in-up">
                Layanan Laundry Profesional
            </p>

            <!-- Heading with animation delay -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up animation-delay-200">
                Cucian Bersih, <br>
                <span class="text-primary-600">Hidup Lebih Mudah!</span>
            </h1>

            <!-- Description with animation delay -->
            <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-10 animate-fade-in-up animation-delay-300">
                Hemat waktu Anda dengan layanan laundry terpercaya. Kami cuci, setrika, dan antar cucian Anda dengan cepat dan berkualitas.
            </p>

            <!-- Buttons with animation -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up animation-delay-400">
                <a href="#services" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                    Lihat Layanan
                </a>
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-2xl font-semibold transition-all shadow-lg border border-gray-200 hover:-translate-y-1">
                    Login Pelanggan
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
                <!-- Service 1 - Enhanced Shadow -->
                <div class="service-card text-center animate-scale-in animation-delay-100">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cuci Reguler</h3>
                    <p class="text-gray-600 mb-4">Layanan cuci standar dengan hasil bersih dan wangi. Cocok untuk pakaian sehari-hari.</p>
                    <p class="text-2xl font-bold text-primary-600">Rp 7.000<span class="text-sm text-gray-500">/kg</span></p>
                </div>

                <!-- Service 2 - Popular with Enhanced Shadow -->
                <div class="service-card text-center border-2 border-primary-200 bg-primary-50/50 animate-scale-in animation-delay-200">
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

                <!-- Service 3 - Enhanced Shadow -->
                <div class="service-card text-center animate-scale-in animation-delay-300">
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
                <div class="service-card p-0 overflow-hidden h-80 lg:h-96">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.3089!2d100.4427415!3d-0.9234548!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b783fff518c5%3A0xd07eb85391c22562!2sD'Four%20Laundry!5e0!3m2!1sid!2sid!4v1705606318000!5m2!1sid!2sid"
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
                            <p class="text-gray-600">Jl. Dr Moh hatta No.5a padang,Indonesia</p>
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


    <footer class="relative z-10 overflow-hidden bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 text-white py-12 mb-20 md:mb-0">
        <!-- Bubble Images in Footer -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-24 h-24 opacity-30 -bottom-6 left-10">
            <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-16 h-16 opacity-25 top-4 right-16">
            <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-32 h-32 opacity-20 -bottom-8 right-1/4">
            <img src="<?= baseUrl('assets/images/bubble.png') ?>" alt="" class="absolute w-12 h-12 opacity-30 top-1/2 left-1/4">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center space-x-3 mb-3">
                <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-10 h-10">
                <span class="text-xl font-bold">D'four Laundry</span>
            </div>
            <p class="text-purple-200/80 text-sm mb-3">Layanan laundry terpercaya untuk keluarga Anda</p>
            <p class="text-purple-300/50 text-xs">&copy; <?= date('Y') ?> D'four Laundry. All rights reserved.</p>
        </div>
    </footer>

    <!-- Script for Bottom Nav Active State -->
    <script>
        // Update active state on bottom nav based on scroll
        const sections = document.querySelectorAll('section[id]');
        const navItems = document.querySelectorAll('.bottom-nav-item');

        function updateActiveNav() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navItems.forEach(item => {
                item.classList.remove('active', 'text-primary-600');
                item.classList.add('text-gray-500');
                if (item.getAttribute('href') === '#' + current) {
                    item.classList.add('active', 'text-primary-600');
                    item.classList.remove('text-gray-500');
                }
            });
        }

        window.addEventListener('scroll', updateActiveNav);
        updateActiveNav();
    </script>

</body>

</html>