    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto py-8 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Section -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">D</span>
                        </div>
                        <h3 class="text-lg font-bold gradient-text">D'four Laundry</h3>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Sistem manajemen laundry modern untuk memudahkan pengelolaan bisnis laundry Anda.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="<?= baseUrl('pages/dashboard.php') ?>" class="text-gray-600 hover:text-primary-600 transition-colors duration-200 text-sm">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?= baseUrl('pages/customers.php') ?>" class="text-gray-600 hover:text-primary-600 transition-colors duration-200 text-sm">
                                Pelanggan
                            </a>
                        </li>
                        <li>
                            <a href="<?= baseUrl('pages/transactions.php') ?>" class="text-gray-600 hover:text-primary-600 transition-colors duration-200 text-sm">
                                Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="<?= baseUrl('pages/check-order.php') ?>" class="text-gray-600 hover:text-primary-600 transition-colors duration-200 text-sm">
                                Cek Order
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>info@dfourlaundry.com</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Jakarta, Indonesia</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-center text-gray-600 text-sm">
                    &copy; <?= date('Y') ?> <span class="font-semibold">D'four Laundry</span>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= baseUrl('assets/js/main.js') ?>"></script>
</body>
</html>
