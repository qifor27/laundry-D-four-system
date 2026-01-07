<?php
/**
 * Customer Dashboard Page
 * Halaman khusus untuk pelanggan setelah login
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin(); // Redirect to login if not authenticated

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Dashboard Pelanggan - D'four Laundry";
$db = Database::getInstance()->getConnection();
$userData = getUserData();

// Ambil phone dari user
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT phone FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$userPhone = $userData['phone'] ?? '';

// Ambil transaksi berdasarkan phone
$transactions = [];
if ($userPhone){
    $stmt = $db->prepare("
        SELECT t.*, c.name as customer_name
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE c.phone = ?
        ORDER BY t.created_at DESC
        ");
        $stmt->execute([$userPhone]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get customer's transactions based on email
$userEmail = $userData['email'] ?? '';
$userName = $userData['name'] ?? 'Pelanggan';
$userPicture = $userData['picture'] ?? null;

// Try to find customer by matching email or phone
$customerData = null;
$transactions = [];

// First, try to find customer with matching name pattern
$stmt = $db->prepare("
    SELECT * FROM customers 
    WHERE name LIKE :name 
    LIMIT 1
");
$stmt->execute([':name' => '%' . $userName . '%']);
$customerData = $stmt->fetch(PDO::FETCH_ASSOC);

// If customer found, get their transactions
if ($customerData) {
    $stmt = $db->prepare("
        SELECT t.*, s.name as service_name, s.unit, s.price_per_unit
        FROM transactions t
        LEFT JOIN service_types s ON t.service_type = s.name
        WHERE t.customer_id = :customer_id
        ORDER BY t.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([':customer_id' => $customerData['id']]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Count transactions by status
$activeCount = 0;
$completedCount = 0;
foreach ($transactions as $trx) {
    if (in_array($trx['status'], ['done', 'picked_up'])) {
        $completedCount++;
    } else {
        $activeCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 font-outfit min-h-screen">
    
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="<?= baseUrl() ?>" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">D</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <?php if ($userPicture): ?>
                            <img src="<?= htmlspecialchars($userPicture) ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-primary-100">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-semibold"><?= strtoupper(substr($userName, 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($userName) ?></p>
                            <p class="text-xs text-gray-500">Pelanggan</p>
                        </div>
                    </div>
                    
                    <button onclick="handleLogout()" class="text-gray-500 hover:text-red-600 transition-colors p-2" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Selamat Datang, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>! 👋
            </h1>
            <p class="text-gray-600 mt-2">Pantau status cucian Anda di sini</p>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <!-- Active Orders -->
            <div class="card bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Pesanan Aktif</p>
                        <h3 class="text-3xl font-bold mt-1"><?= $activeCount ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Completed -->
            <div class="card bg-gradient-to-br from-secondary-500 to-secondary-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Selesai</p>
                        <h3 class="text-3xl font-bold mt-1"><?= $completedCount ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Total Orders -->
            <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Pesanan</p>
                        <h3 class="text-3xl font-bold mt-1"><?= count($transactions) ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Riwayat Pesanan</h2>
                <a href="<?= baseUrl('pages/check-order.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                    Cek dengan Nomor HP →
                </a>
            </div>
            
            <?php if (empty($transactions)): ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 mb-2">Belum ada riwayat pesanan</p>
                    <p class="text-sm text-gray-400">Data akan muncul setelah Anda melakukan transaksi di outlet kami</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($transactions as $trx): ?>
                        <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-mono text-sm text-gray-500">#<?= $trx['id'] ?></span>
                                        <span class="badge <?= getStatusBadge($trx['status']) ?>">
                                            <?= getStatusLabel($trx['status']) ?>
                                        </span>
                                    </div>
                                    <h4 class="font-medium text-gray-900"><?= htmlspecialchars($trx['service_type']) ?></h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?php if ($trx['weight'] > 0): ?>
                                            <?= $trx['weight'] ?> kg
                                        <?php else: ?>
                                            <?= $trx['quantity'] ?> pcs
                                        <?php endif; ?>
                                        • <?= formatDate($trx['created_at']) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900"><?= formatRupiah($trx['price']) ?></p>
                                    <?php if ($trx['notes']): ?>
                                        <p class="text-xs text-gray-400 mt-1 max-w-[150px] truncate"><?= htmlspecialchars($trx['notes']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <?php 
                            $statusOrder = ['pending' => 1, 'washing' => 2, 'drying' => 3, 'ironing' => 4, 'done' => 5, 'picked_up' => 6];
                            $currentStep = $statusOrder[$trx['status']] ?? 1;
                            $progress = ($currentStep / 6) * 100;
                            ?>
                            <div class="mt-4">
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-500" style="width: <?= $progress ?>%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>Diterima</span>
                                    <span>Selesai</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Links -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
            <a href="<?= baseUrl('pages/check-order.php') ?>" class="card hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Cek Order</h3>
                    <p class="text-sm text-gray-500">Cari dengan nomor telepon</p>
                </div>
            </a>
            
            <a href="<?= baseUrl() ?>" class="card hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Beranda</h3>
                    <p class="text-sm text-gray-500">Kembali ke halaman utama</p>
                </div>
            </a>
        </div>
        
    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gray-500">&copy; <?= date('Y') ?> D'four Laundry. All rights reserved.</p>
        </div>
    </footer>

    <script>
    function handleLogout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            fetch('<?= baseUrl('api/logout.php') ?>', {
                method: 'POST',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = data.redirect || '<?= baseUrl('pages/login.php') ?>';
            })
            .catch(() => {
                window.location.href = '<?= baseUrl('pages/login.php') ?>';
            });
        }
    }
    </script>
</body>
</html>
