<?php
if (!isset($pageTitle)) {
    $pageTitle = "Admin - D'four Laundry";
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="D'four Laundry Management System - Admin Panel">
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
<body class="bg-gray-50 font-outfit">
    
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar-admin.php'; ?>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm z-10 no-print">
                <div class="px-6 sm:px-8 lg:px-10 py-8">
                    <div class="flex items-center justify-between">
                        <!-- Page Title -->
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                            <p class="text-sm text-gray-500 mt-2">Kelola data laundry Anda</p>
                        </div>
                        
                        <!-- User Info & Actions -->
                        <div class="flex items-center space-x-6">
                            <!-- Back to Public Site -->
                            <a href="<?= baseUrl('pages/check-order.php') ?>" 
                               class="hidden sm:flex items-center text-sm text-gray-600 hover:text-primary-600 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Situs
                            </a>
                            
                            <!-- User Avatar -->
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold text-sm">A</span>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-sm font-medium text-gray-700">Admin</p>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 sm:p-8 lg:p-10">
