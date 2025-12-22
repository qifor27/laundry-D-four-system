<?php
/**
 * Check Order Page
 * Portal untuk customer cek status order mereka
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Cek Status Order - D'four Laundry";
$db = Database::getInstance()->getConnection();

$orders = null;
$searchPerformed = false;

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['phone'])) {
    $phone = sanitize($_POST['phone'] ?? $_GET['phone']);
    $searchPerformed = true;
    
    // Search by phone number
    $stmt = $db->prepare("
        SELECT t.*, c.name as customer_name, c.phone, c.address
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE c.phone LIKE ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute(["%{$phone}%"]);
    $orders = $stmt->fetchAll();
}

include __DIR__ . '/../includes/header-public.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-xl mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold gradient-text mb-4">Cek Status Order</h1>
        <p class="text-gray-600 text-lg">Masukkan nomor telepon untuk melihat status cucian Anda</p>
    </div>
    
    <!-- Search Form -->
    <div class="card max-w-md mx-auto mb-8">
        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="form-label">Nomor Telepon</label>
                <input type="tel" name="phone" required 
                       class="form-input text-lg" 
                       placeholder="08123456789"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       oninput="formatPhoneNumber(this)">
                <p class="text-xs text-gray-500 mt-2">
                    Masukkan nomor telepon yang terdaftar saat melakukan transaksi
                </p>
            </div>
            
            <button type="submit" class="btn-primary w-full">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Cek Status
            </button>
        </form>
    </div>
    
    <!-- Search Results -->
    <?php if ($searchPerformed): ?>
        <?php if (empty($orders)): ?>
            <!-- No Results -->
            <div class="card text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-600">
                    Tidak ditemukan transaksi dengan nomor telepon <strong><?= htmlspecialchars($_POST['phone'] ?? '') ?></strong>
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Pastikan nomor telepon sudah benar atau hubungi kami untuk bantuan
                </p>
            </div>
        <?php else: ?>
            <!-- Customer Info -->
            <div class="card mb-6 bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-200">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        <?= strtoupper(substr($orders[0]['customer_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($orders[0]['customer_name']) ?></h3>
                        <p class="text-gray-600"><?= htmlspecialchars($orders[0]['phone']) ?></p>
                        <?php if (!empty($orders[0]['address'])): ?>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($orders[0]['address']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Orders List -->
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Riwayat Transaksi 
                    <span class="text-primary-600">(<?= count($orders) ?>)</span>
                </h2>
                
                <?php foreach ($orders as $order): ?>
                <div class="card hover:shadow-xl transition-shadow duration-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Order Info -->
                        <div class="flex-1 mb-4 md:mb-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="font-mono text-sm font-semibold text-gray-600">#<?= $order['id'] ?></span>
                                <span class="badge <?= getStatusBadge($order['status']) ?>">
                                    <?= getStatusLabel($order['status']) ?>
                                </span>
                            </div>
                            
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                <?= htmlspecialchars($order['service_type']) ?>
                            </h4>
                            
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                    </svg>
                                    <?php if ($order['weight'] > 0): ?>
                                        <?= $order['weight'] ?> kg
                                    <?php else: ?>
                                        <?= $order['quantity'] ?> pcs
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?= formatDate($order['created_at']) ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($order['notes'])): ?>
                                <p class="text-sm text-gray-500 mt-2 italic">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    <?= htmlspecialchars($order['notes']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price -->
                        <div class="text-right md:ml-6">
                            <p class="text-sm text-gray-500 mb-1">Total</p>
                            <p class="text-2xl font-bold text-primary-600">
                                <?= formatRupiah($order['price']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Status Progress -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Progress:</p>
                        <div class="flex items-center justify-between">
                            <?php 
                            $statuses = ['pending', 'washing', 'ironing', 'done', 'picked_up'];
                            $currentStatusIndex = array_search($order['status'], $statuses);
                            ?>
                            
                            <?php foreach ($statuses as $index => $status): ?>
                                <?php 
                                $isActive = $index <= $currentStatusIndex;
                                $isCurrent = $status === $order['status'];
                                ?>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mb-1 <?= $isActive ? 'bg-primary-600 text-white' : 'bg-gray-300 text-gray-600' ?> <?= $isCurrent ? 'ring-4 ring-primary-200' : '' ?>">
                                        <?php if ($isActive): ?>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs text-center <?= $isActive ? 'text-gray-900 font-medium' : 'text-gray-500' ?>">
                                        <?= ['pending' => 'Diterima', 'washing' => 'Cuci', 'ironing' => 'Setrika', 'done' => 'Selesai', 'picked_up' => 'Diambil'][$status] ?>
                                    </span>
                                </div>
                                
                                <?php if ($index < count($statuses) - 1): ?>
                                    <div class="flex-1 h-1 <?= $index < $currentStatusIndex ? 'bg-primary-600' : 'bg-gray-300' ?> mx-1"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Info Section -->
    <div class="mt-12 card bg-gradient-to-br from-gray-50 to-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi</h3>
        <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-start">
                <svg class="w-5 h-5 text-primary-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Pesanan dapat diambil setelah status "Selesai"
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-primary-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Kami akan menghubungi Anda via WhatsApp saat pesanan selesai
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-primary-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Hubungi kami di +62 812-3456-7890 untuk informasi lebih lanjut
            </li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
