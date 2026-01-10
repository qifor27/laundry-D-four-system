<?php
/**
 * Transaction Detail Page
 * Menampilkan detail transaksi dengan tombol WhatsApp dan pembayaran
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/whatsapp-helper.php';
require_once __DIR__ . '/../includes/payment-helper.php';

$pageTitle = "Detail Transaksi - D'four Laundry";
$db = Database::getInstance()->getConnection();

// Get transaction ID
$id = $_GET['id'] ?? 0;

if (!$id) {
    header('Location: transactions.php');
    exit;
}

// Get transaction with customer info
$stmt = $db->prepare("
    SELECT t.*, c.name as customer_name, c.phone
    FROM transactions t 
    JOIN customers c ON t.customer_id = c.id 
    WHERE t.id = ?
");
$stmt->execute([$id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: transactions.php');
    exit;
}

// Prepare customer data for WhatsApp
$customer = [
    'name' => $transaction['customer_name'],
    'phone' => $transaction['phone']
];

// Generate WhatsApp URLs
$waUrls = getAllWhatsAppUrls($transaction, $customer);

// Get active payment methods
$paymentMethods = getActivePaymentMethods();

include __DIR__ . '/../includes/header-admin.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="transactions.php" class="text-primary-600 hover:text-primary-800 font-medium">
            ← Kembali ke Daftar Transaksi
        </a>
    </div>

    <!-- Transaction Header -->
    <div class="card mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Transaksi #<?= $transaction['id'] ?>
                </h1>
                <p class="text-gray-600 mt-1">
                    <?= formatDate($transaction['created_at']) ?>
                </p>
            </div>
            <div class="text-right">
                <span class="badge <?= getStatusBadge($transaction['status']) ?>">
                    <?= getStatusLabel($transaction['status']) ?>
                </span>
                <br>
                <span class="badge <?= getPaymentStatusBadge($transaction['payment_status'] ?? 'unpaid') ?> mt-2 inline-block">
                    <?= getPaymentStatusLabel($transaction['payment_status'] ?? 'unpaid') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Customer Info -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">👤 Pelanggan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Nama</p>
                    <p class="font-medium"><?= htmlspecialchars($transaction['customer_name']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Telepon</p>
                    <p class="font-medium"><?= htmlspecialchars($transaction['phone']) ?></p>
                </div>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🧺 Detail Pesanan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Layanan</p>
                    <p class="font-medium"><?= htmlspecialchars($transaction['service_type']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Qty/Berat</p>
                    <p class="font-medium">
                        <?php if ($transaction['weight'] > 0): ?>
                            <?= $transaction['weight'] ?> kg
                        <?php else: ?>
                            <?= $transaction['quantity'] ?> pcs
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-bold text-primary-600"><?= formatRupiah($transaction['price']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation (jika belum bayar) -->
    <?php if (($transaction['payment_status'] ?? 'unpaid') !== 'paid'): ?>
    <div class="card mt-6 bg-yellow-50 border-yellow-200">
        <h2 class="text-lg font-semibold text-yellow-800 mb-4">💳 Konfirmasi Pembayaran</h2>
        <form id="payment-form" class="space-y-4">
            <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
            
            <div>
                <label class="form-label">Metode Pembayaran</label>
                <select name="payment_method_id" class="form-input" required>
                    <?php foreach ($paymentMethods as $method): ?>
                    <option value="<?= $method['id'] ?>">
                        <?= htmlspecialchars($method['name']) ?>
                        <?php if ($method['bank_name']): ?>
                            (<?= $method['bank_name'] ?> - <?= $method['account_number'] ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="form-label">Catatan (opsional)</label>
                <input type="text" name="notes" class="form-input" placeholder="Contoh: Transfer bukti #123">
            </div>
            
            <button type="submit" class="btn-primary w-full">
                ✓ Konfirmasi Pembayaran
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- WhatsApp Actions -->
    <div class="card mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📱 Kirim WhatsApp</h2>
        <p class="text-gray-600 text-sm mb-4">
            Klik tombol di bawah untuk membuka WhatsApp Web dengan pesan terisi.
        </p>
        
        <?php if (empty($transaction['phone'])): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                ⚠️ Pelanggan ini tidak memiliki nomor HP.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php if ($waUrls['order_created']): ?>
                <a href="<?= $waUrls['order_created'] ?>" target="_blank"
                   class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors">
                    <span class="text-2xl mb-2">📦</span>
                    <span class="text-sm font-medium text-green-800 text-center">Pesanan Diterima</span>
                </a>
                <?php endif; ?>

                <?php if ($waUrls['status_update']): ?>
                <a href="<?= $waUrls['status_update'] ?>" target="_blank"
                   class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                    <span class="text-2xl mb-2">🔄</span>
                    <span class="text-sm font-medium text-blue-800 text-center">Update Status</span>
                </a>
                <?php endif; ?>

                <?php if ($waUrls['ready_pickup']): ?>
                <a href="<?= $waUrls['ready_pickup'] ?>" target="_blank"
                   class="flex flex-col items-center justify-center p-4 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition-colors">
                    <span class="text-2xl mb-2">✅</span>
                    <span class="text-sm font-medium text-amber-800 text-center">Siap Diambil</span>
                </a>
                <?php endif; ?>

                <?php if ($waUrls['payment_reminder']): ?>
                <a href="<?= $waUrls['payment_reminder'] ?>" target="_blank"
                   class="flex flex-col items-center justify-center p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                    <span class="text-2xl mb-2">💳</span>
                    <span class="text-sm font-medium text-red-800 text-center">Reminder Bayar</span>
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Update Status -->
    <div class="card mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Update Status</h2>
        <div class="flex flex-wrap gap-2">
            <?php
            $statuses = [
                'pending' => ['label' => 'Menunggu', 'class' => 'bg-gray-100 text-gray-800 hover:bg-gray-200'],
                'washing' => ['label' => 'Dicuci', 'class' => 'bg-blue-100 text-blue-800 hover:bg-blue-200'],
                'drying' => ['label' => 'Dikeringkan', 'class' => 'bg-cyan-100 text-cyan-800 hover:bg-cyan-200'],
                'ironing' => ['label' => 'Disetrika', 'class' => 'bg-purple-100 text-purple-800 hover:bg-purple-200'],
                'done' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800 hover:bg-green-200'],
                'picked_up' => ['label' => 'Diambil', 'class' => 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'],
            ];
            foreach ($statuses as $status => $info):
                $isActive = $transaction['status'] === $status;
            ?>
            <button onclick="updateStatus('<?= $status ?>')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors <?= $info['class'] ?> <?= $isActive ? 'ring-2 ring-offset-2 ring-primary-500' : '' ?>">
                <?= $info['label'] ?>
                <?php if ($isActive): ?>✓<?php endif; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Delete Transaction -->
    <div class="card mt-6 bg-red-50 border-red-200">
        <h2 class="text-lg font-semibold text-red-800 mb-4">⚠️ Zona Bahaya</h2>
        <button onclick="deleteTransaction()" class="btn-danger">
            Hapus Transaksi
        </button>
    </div>
</div>

<script>
// Confirm payment
document.getElementById('payment-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'confirm');
    
    try {
        const response = await fetch('../api/payments-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Pembayaran berhasil dikonfirmasi!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(result.message || 'Gagal konfirmasi pembayaran', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
});

// Update status
async function updateStatus(status) {
    try {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', <?= $transaction['id'] ?>);
        formData.append('status', status);
        
        const response = await fetch('../api/transactions-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Status berhasil diupdate!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Gagal update status', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}

// Delete transaction
async function deleteTransaction() {
    if (!confirm('Yakin ingin menghapus transaksi ini?')) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', <?= $transaction['id'] ?>);
        
        const response = await fetch('../api/transactions-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Transaksi berhasil dihapus!', 'success');
            setTimeout(() => window.location.href = 'transactions.php', 1000);
        } else {
            showNotification('Gagal menghapus transaksi', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
