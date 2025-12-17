<?php
if (!isset($pageTitle)) {
    $pageTitle = "D'four Laundry Management";
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="D'four Laundry Management System - Sistem manajemen laundry modern dan efisien">
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
    
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-30 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold gradient-text">D'four Laundry</h1>
                        <p class="text-xs text-gray-500 hidden sm:block">Management System</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="<?= baseUrl('pages/dashboard.php') ?>" 
                       class="nav-link text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'text-primary-600' : '' ?>">
                        Dashboard
                    </a>
                    <a href="<?= baseUrl('pages/customers.php') ?>" 
                       class="nav-link text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'customers.php') ? 'text-primary-600' : '' ?>">
                        Pelanggan
                    </a>
                    <a href="<?= baseUrl('pages/transactions.php') ?>" 
                       class="nav-link text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'text-primary-600' : '' ?>">
                        Transaksi
                    </a>
                    <a href="<?= baseUrl('pages/check-order.php') ?>" 
                       class="nav-link text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'check-order.php') ? 'text-primary-600' : '' ?>">
                        Cek Order
                    </a>
                    
                    <!-- User Menu (Optional) -->
                    <div class="flex items-center space-x-2 pl-4 border-l border-gray-200">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 font-semibold text-sm">A</span>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Admin</span>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-16 6h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-2">
                <a href="<?= baseUrl('pages/dashboard.php') ?>" 
                   class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-primary-50 text-primary-600' : '' ?>">
                    Dashboard
                </a>
                <a href="<?= baseUrl('pages/customers.php') ?>" 
                   class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'customers.php') ? 'bg-primary-50 text-primary-600' : '' ?>">
                    Pelanggan
                </a>
                <a href="<?= baseUrl('pages/transactions.php') ?>" 
                   class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'bg-primary-50 text-primary-600' : '' ?>">
                    Transaksi
                </a>
                <a href="<?= baseUrl('pages/check-order.php') ?>" 
                   class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'check-order.php') ? 'bg-primary-50 text-primary-600' : '' ?>">
                    Cek Order
                </a>
            </div>
        </div>
    </nav>
    
    <?php
    // Display flash message if exists
    $flashMessage = getFlashMessage();
    if ($flashMessage):
        $alertClass = [
            'success' => 'bg-green-50 border-green-500 text-green-800',
            'error' => 'bg-red-50 border-red-500 text-red-800',
            'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
            'info' => 'bg-blue-50 border-blue-500 text-blue-800'
        ];
        $class = $alertClass[$flashMessage['type']] ?? $alertClass['info'];
    ?>
    <div class="max-w-7xl mx-auto px-4 mt-4 no-print">
        <div class="<?= $class ?> border-l-4 p-4 rounded-lg shadow-md animate-slide-up" role="alert">
            <p class="font-medium"><?= htmlspecialchars($flashMessage['message']) ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content Wrapper -->
    <main class="flex-1">
