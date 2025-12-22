<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Navigation -->
<aside class="w-64 bg-white shadow-lg flex-shrink-0 no-print">
    <div class="h-full flex flex-col">
        <!-- Logo & Brand -->
        <div class="px-6 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-xl">D</span>
                </div>
                <div>
                    <h1 class="text-lg font-bold gradient-text">D'four Laundry</h1>
                    <p class="text-xs text-gray-500">Admin Panel</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="<?= baseUrl('pages/dashboard.php') ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?= ($currentPage == 'dashboard.php') ? 'bg-primary-50 text-primary-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>
            
            <!-- Customers -->
            <a href="<?= baseUrl('pages/customers.php') ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?= ($currentPage == 'customers.php') ? 'bg-primary-50 text-primary-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Pelanggan
            </a>
            
            <!-- Transactions -->
            <a href="<?= baseUrl('pages/transactions.php') ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?= ($currentPage == 'transactions.php') ? 'bg-primary-50 text-primary-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Transaksi
            </a>
        </nav>
        
        <!-- Bottom Section -->
        <div class="px-4 py-4 border-t border-gray-200">
            <!-- Help/Support -->
            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-primary-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bantuan
            </a>
            
            <!-- Logout -->
            <a href="<?= baseUrl('pages/check-order.php') ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:text-red-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
        </div>
    </div>
</aside>
