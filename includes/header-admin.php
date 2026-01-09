<?php
if (!isset($pageTitle)) {
    $pageTitle = "Admin - D'four Laundry";
}

// Get current user data (assuming auth.php is already included by the page)
$currentUser = getUserData();
$userName = $currentUser['name'] ?? 'Admin';
$userPicture = $currentUser['picture'] ?? null;
$userRole = $currentUser['role'] ?? 'user';
$userInitial = strtoupper(substr($userName, 0, 1));
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="D'four Laundry Management System - Admin Panel">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts - Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons (Modern line icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>


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

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button onclick="toggleUserMenu()"
                                    class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-2 transition-colors">
                                    <!-- User Avatar -->
                                    <?php if ($userPicture): ?>
                                        <img src="<?= htmlspecialchars($userPicture) ?>"
                                            alt="<?= htmlspecialchars($userName) ?>"
                                            class="w-10 h-10 rounded-full object-cover border-2 border-primary-100">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 font-semibold text-sm"><?= $userInitial ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($userName) ?></p>
                                        <p class="text-xs text-gray-500 capitalize"><?= htmlspecialchars($userRole) ?></p>
                                    </div>

                                    <!-- Dropdown Arrow -->
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="userDropdown"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($userName) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-1">
                                        <a href="<?= baseUrl('pages/check-order.php') ?>"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            Cek Order
                                        </a>
                                        <a href="<?= baseUrl() ?>"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            Landing Page
                                        </a>
                                    </div>

                                    <!-- Logout -->
                                    <div class="border-t border-gray-100 pt-1">
                                        <button onclick="handleLogout()"
                                            class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 sm:p-8 lg:p-10">

                <script>
                    // Toggle user dropdown menu
                    function toggleUserMenu() {
                        const dropdown = document.getElementById('userDropdown');
                        dropdown.classList.toggle('hidden');
                    }

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('userDropdown');
                        const button = event.target.closest('button');

                        if (dropdown && !dropdown.contains(event.target) && (!button || !button.onclick?.toString().includes('toggleUserMenu'))) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    // Handle logout
                    function handleLogout() {
                        if (confirm('Apakah Anda yakin ingin logout?')) {
                            fetch('<?= baseUrl('api/logout.php') ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href = data.redirect || '<?= baseUrl('pages/login.php') ?>';
                                    }
                                })
                                .catch(() => {
                                    // Fallback: redirect directly
                                    window.location.href = '<?= baseUrl('pages/login.php') ?>';
                                });
                        }
                    }

                    // Initialize Lucide icons
                    document.addEventListener('DOMContentLoaded', function() {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    });
                </script>