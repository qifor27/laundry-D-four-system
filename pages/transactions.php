<?php
/**
 * Transactions Page
 * Mengelola transaksi laundry
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Transaksi - D'four Laundry";
$db = Database::getInstance()->getConnection();

// Get all transactions with customer info
$transactions = $db->query("
    SELECT t.*, c.name as customer_name, c.phone 
    FROM transactions t 
    JOIN customers c ON t.customer_id = c.id 
    ORDER BY t.created_at DESC
")->fetchAll();

// Get all customers for dropdown
$customers = $db->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();

// Get service types
$serviceTypes = $db->query("SELECT * FROM service_types WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Transactions Content -->
<div>
    <!-- Add Transaction Button -->
    <div class="mb-6 flex justify-end">
        <button onclick="openModal('add-transaction-modal')" class="btn-primary">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Transaksi Baru
        </button>
    </div>
    
    <!-- Filter & Search -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <input type="text" id="search-input" placeholder="Cari transaksi..." 
               class="form-input" onkeyup="filterTable('search-input', 'transactions-table')">
        
        <select id="status-filter" class="form-input" onchange="filterByStatus()">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="washing">Sedang Dicuci</option>
            <option value="drying">Sedang Dikeringkan</option>
            <option value="ironing">Sedang Disetrika</option>
            <option value="done">Selesai</option>
            <option value="picked_up">Sudah Diambil</option>
        </select>
    </div>
    
    <!-- Transactions Table -->
    <div class="card">
        <?php if (empty($transactions)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 mb-4">Belum ada transaksi</p>
                <button onclick="openModal('add-transaction-modal')" class="btn-primary">
                    Buat Transaksi Pertama
                </button>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table" id="transactions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Qty/Berat</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trx): ?>
                        <tr data-status="<?= $trx['status'] ?>">
                            <td class="font-mono text-sm">#<?= $trx['id'] ?></td>
                            <td>
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($trx['customer_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($trx['phone']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($trx['service_type']) ?></td>
                            <td>
                                <?php if ($trx['weight'] > 0): ?>
                                    <?= $trx['weight'] ?> kg
                                <?php else: ?>
                                    <?= $trx['quantity'] ?> pcs
                                <?php endif; ?>
                            </td>
                            <td class="font-semibold text-gray-900"><?= formatRupiah($trx['price']) ?></td>
                            <td>
                                <select onchange="updateStatus(<?= $trx['id'] ?>, this.value)" 
                                        class="badge <?= getStatusBadge($trx['status']) ?> border-0 cursor-pointer">
                                    <option value="pending" <?= $trx['status'] == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="washing" <?= $trx['status'] == 'washing' ? 'selected' : '' ?>>Dicuci</option>
                                    <option value="drying" <?= $trx['status'] == 'drying' ? 'selected' : '' ?>>Dikeringkan</option>
                                    <option value="ironing" <?= $trx['status'] == 'ironing' ? 'selected' : '' ?>>Disetrika</option>
                                    <option value="done" <?= $trx['status'] == 'done' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="picked_up" <?= $trx['status'] == 'picked_up' ? 'selected' : '' ?>>Diambil</option>
                                </select>
                            </td>
                            <td class="text-sm text-gray-600"><?= formatDate($trx['created_at']) ?></td>
                            <td>
                                <button onclick="viewDetails(<?= $trx['id'] ?>)" 
                                        class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Total: <span class="font-semibold"><?= count($transactions) ?></span> transaksi
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="add-transaction-modal" class="hidden">
    <div class="modal-overlay" onclick="closeModal('add-transaction-modal')"></div>
    <div class="modal-content max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Transaksi Baru</h2>
        <form method="POST" action="../api/transactions-api.php" id="transaction-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Customer Selection -->
                <div class="md:col-span-2">
                    <label class="form-label">Pelanggan</label>
                    <select name="customer_id" required class="form-input">
                        <option value="">Pilih Pelanggan</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= htmlspecialchars($customer['name']) ?> - <?= htmlspecialchars($customer['phone']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Belum terdaftar? <a href="customers.php" class="text-primary-600">Tambah pelanggan baru</a>
                    </p>
                </div>
                
                <!-- Service Type -->
                <div class="md:col-span-2">
                    <label class="form-label">Jenis Layanan</label>
                    <select name="service_type" id="service-select" required class="form-input" onchange="updatePricing()">
                        <option value="">Pilih Layanan</option>
                        <?php foreach ($serviceTypes as $service): ?>
                            <option value="<?= htmlspecialchars($service['name']) ?>" 
                                    data-unit="<?= $service['unit'] ?>" 
                                    data-price="<?= $service['price_per_unit'] ?>">
                                <?= htmlspecialchars($service['name']) ?> 
                                (<?= formatRupiah($service['price_per_unit']) ?>/<?= $service['unit'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Weight/Quantity -->
                <div id="weight-field">
                    <label class="form-label">Berat (kg)</label>
                    <input type="number" name="weight" step="0.5" min="0" class="form-input" 
                           oninput="calculateTotal()" placeholder="0">
                </div>
                
                <div id="quantity-field" class="hidden">
                    <label class="form-label">Jumlah (pcs)</label>
                    <input type="number" name="quantity" min="1" class="form-input" 
                           oninput="calculateTotal()" placeholder="0">
                </div>
                
                <!-- Price -->
                <div>
                    <label class="form-label">Total Harga</label>
                    <input type="number" name="price" id="price-input" required class="form-input" 
                           placeholder="0" min="0">
                    <p class="text-sm text-primary-600 mt-1" id="price-preview"></p>
                </div>
                
                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2" class="form-input" 
                              placeholder="Tambahkan catatan khusus..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal('add-transaction-modal')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update pricing based on service selection
function updatePricing() {
    const select = document.getElementById('service-select');
    const selectedOption = select.options[select.selectedIndex];
    const unit = selectedOption.dataset.unit;
    
    // Show/hide weight or quantity field
    if (unit === 'kg') {
        document.getElementById('weight-field').classList.remove('hidden');
        document.getElementById('quantity-field').classList.add('hidden');
        document.querySelector('[name="weight"]').required = true;
        document.querySelector('[name="quantity"]').required = false;
    } else {
        document.getElementById('weight-field').classList.add('hidden');
        document.getElementById('quantity-field').classList.remove('hidden');
        document.querySelector('[name="weight"]').required = false;
        document.querySelector('[name="quantity"]').required = true;
    }
    
    calculateTotal();
}

// Calculate total price
function calculateTotal() {
    const select = document.getElementById('service-select');
    const selectedOption = select.options[select.selectedIndex];
    const pricePerUnit = parseFloat(selectedOption.dataset.price) || 0;
    const unit = selectedOption.dataset.unit;
    
    let quantity = 0;
    if (unit === 'kg') {
        quantity = parseFloat(document.querySelector('[name="weight"]').value) || 0;
    } else {
        quantity = parseInt(document.querySelector('[name="quantity"]').value) || 0;
    }
    
    const total = pricePerUnit * quantity;
    document.getElementById('price-input').value = total;
    document.getElementById('price-preview').textContent = formatRupiah(total);
}

// Format currency
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Update transaction status
async function updateStatus(id, status) {
    try {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
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

// Filter by status
function filterByStatus() {
    const filter = document.getElementById('status-filter').value.toLowerCase();
    const rows = document.querySelectorAll('#transactions-table tbody tr');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        if (filter === '' || status === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Handle form submission
document.getElementById('transaction-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create');
    
    try {
        const response = await fetch('../api/transactions-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Transaksi berhasil dibuat!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(result.message || 'Gagal membuat transaksi', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
});

function viewDetails(id) {
    window.location.href = `transaction-detail.php?id=${id}`;
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
