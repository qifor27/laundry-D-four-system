<?php
/**
 * Dashboard Page
 * Menampilkan statistik dan transaksi terbaru
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Dashboard - D'four Laundry";
$db = Database::getInstance()->getConnection();

// Get today's sales
$todayQuery = $db->query("
    SELECT COALESCE(SUM(price), 0) as total 
    FROM transactions 
    WHERE DATE(created_at) = CURDATE()
");
$todaySales = $todayQuery->fetch()['total'];

// Get active jobs (not done or picked up)
$activeQuery = $db->query("
    SELECT COUNT(*) as count 
    FROM transactions 
    WHERE status NOT IN ('done', 'picked_up', 'cancelled')
");
$activeJobs = $activeQuery->fetch()['count'];

// Get total customers
$customersQuery = $db->query("SELECT COUNT(*) as count FROM customers");
$totalCustomers = $customersQuery->fetch()['count'];

// Get month's revenue
$monthQuery = $db->query("
    SELECT COALESCE(SUM(price), 0) as total 
    FROM transactions 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
");
$monthRevenue = $monthQuery->fetch()['total'];

// Get recent transactions
$recentTransactions = $db->query("
    SELECT t.*, c.name as customer_name, c.phone 
    FROM transactions t 
    JOIN customers c ON t.customer_id = c.id 
    ORDER BY t.created_at DESC 
    LIMIT 10
")->fetchAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Dashboard Content -->
<div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Sales -->
        <div class="card-gradient animate-fade-in group hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Penjualan Hari Ini</p>
                    <h3 class="text-3xl font-bold mt-2"><?= formatRupiah($todaySales) ?></h3>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Jobs -->
        <div class="bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl p-6 text-white shadow-xl group hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Pesanan Aktif</p>
                    <h3 class="text-3xl font-bold mt-2"><?= $activeJobs ?></h3>
                    <p class="text-xs opacity-75 mt-1">Sedang diproses</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Customers -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl group hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total Pelanggan</p>
                    <h3 class="text-3xl font-bold mt-2"><?= $totalCustomers ?></h3>
                    <p class="text-xs opacity-75 mt-1">Terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Month Revenue -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl group hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Pendapatan Bulan Ini</p>
                    <h3 class="text-3xl font-bold mt-2"><?= formatRupiah($monthRevenue) ?></h3>
                    <p class="text-xs opacity-75 mt-1"><?= date('F Y') ?></p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Transaksi Terbaru</h2>
            <a href="<?= baseUrl('pages/transactions.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                Lihat Semua →
            </a>
        </div>
        
        <?php if (empty($recentTransactions)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">Belum ada transaksi</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $trx): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?= $trx['id'] ?></td>
                            <td>
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($trx['customer_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($trx['phone']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($trx['service_type']) ?></td>
                            <td class="font-semibold text-gray-900"><?= formatRupiah($trx['price']) ?></td>
                            <td>
                                <span class="badge <?= getStatusBadge($trx['status']) ?>">
                                    <?= getStatusLabel($trx['status']) ?>
                                </span>
                            </td>
                            <td class="text-sm text-gray-600"><?= formatDate($trx['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
