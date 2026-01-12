<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Navigation - Icon Only -->
<aside class="w-20 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 shadow-2xl flex-shrink-0 no-print rounded-r-3xl">
    <div class="h-full flex flex-col items-center py-8">
        <!-- Logo -->
        <div class="mb-10">
            <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-12 h-12">
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 flex flex-col items-center space-y-4">
            <!-- Dashboard -->
            <a href="<?= baseUrl('pages/dashboard.php') ?>"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 group
               <?= ($currentPage == 'dashboard.php') ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>"
                title="Dashboard">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </a>

            <!-- Customers -->
            <a href="<?= baseUrl('pages/customers.php') ?>"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 group
               <?= ($currentPage == 'customers.php') ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>"
                title="Pelanggan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </a>

            <!-- Transactions -->
            <a href="<?= baseUrl('pages/transactions.php') ?>"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 group
               <?= ($currentPage == 'transactions.php' || $currentPage == 'transaction-detail.php') ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>"
                title="Transaksi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </a>

            <!-- Reports -->
            <a href="<?= baseUrl('pages/reports.php') ?>"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 group
               <?= ($currentPage == 'reports.php') ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>"
                title="Laporan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </a>

            <!-- Payment Methods -->
            <a href="<?= baseUrl('pages/payment-methods.php') ?>"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 group
               <?= ($currentPage == 'payment-methods.php') ? 'bg-white/20 text-white shadow-lg' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>"
                title="Metode Pembayaran">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </a>
        </nav>

        <!-- Bottom Section -->
        <div class="flex flex-col items-center space-y-4 pt-6 border-t border-white/10">
            <!-- Help -->
            <a href="#" class="w-12 h-12 flex items-center justify-center rounded-2xl text-white/40 hover:bg-white/10 hover:text-white transition-all duration-300" title="Bantuan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </a>

            <!-- Logout -->
            <a href="<?= baseUrl('api/logout.php') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all duration-300" title="Logout">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </a>
        </div>
    </div>
</aside>