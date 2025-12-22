<?php
if (!isset($pageTitle)) {
    $pageTitle = "D'four Laundry";
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="D'four Laundry - Cek status cucian Anda">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">
    
    <!-- Favicon (Optional) -->
    <link rel="icon" type="image/x-icon" href="<?= baseUrl('assets/images/favicon.ico') ?>">
</head>
<body class="bg-gray-50 font-outfit min-h-screen flex flex-col">
    
    <!-- Public Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold gradient-text">D'four Laundry</h1>
                        <p class="text-xs text-gray-500 hidden sm:block">Cek Status Cucian</p>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="<?= baseUrl('pages/check-order.php') ?>" 
                       class="nav-link text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'check-order.php') ? 'text-primary-600' : '' ?>">
                        🔍 Cek Order
                    </a>
                    
                    <!-- Admin Link -->
                    <a href="<?= baseUrl('pages/dashboard.php') ?>" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Login Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <main class="flex-1">
