<?php
/**
 * Payment Methods Admin Page
 * Halaman untuk mengelola metode pembayaran (rekening bank)
 */

require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Metode Pembayaran - D'four Laundry";
$db = Database::getInstance()->getConnection();

// Get all payment methods
$paymentMethods = $db->query("SELECT * FROM payment_methods ORDER BY type, name")->fetchAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">💳 Metode Pembayaran</h1>
            <p class="text-gray-600">Kelola rekening bank untuk pembayaran transfer</p>
        </div>
        <button onclick="openModal('add-method-modal')" class="btn-primary">
            + Tambah Metode
        </button>
    </div>

    <!-- Payment Methods List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($paymentMethods as $method): ?>
        <div class="card <?= $method['is_active'] ? '' : 'opacity-50' ?>">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <?php if ($method['type'] === 'cash'): ?>
                        <span class="text-3xl">💵</span>
                    <?php else: ?>
                        <span class="text-3xl">🏦</span>
                    <?php endif; ?>
                </div>
                <span class="badge <?= $method['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                    <?= $method['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($method['name']) ?></h3>
            
            <?php if ($method['type'] === 'bank_transfer'): ?>
            <div class="mt-2 text-sm text-gray-600">
                <p><strong>Bank:</strong> <?= htmlspecialchars($method['bank_name']) ?></p>
                <p><strong>No. Rek:</strong> <?= htmlspecialchars($method['account_number']) ?></p>
                <p><strong>A/N:</strong> <?= htmlspecialchars($method['account_holder']) ?></p>
            </div>
            <?php else: ?>
            <p class="mt-2 text-sm text-gray-600">Pembayaran tunai di tempat</p>
            <?php endif; ?>
            
            <div class="flex gap-2 mt-4">
                <button onclick="editMethod(<?= htmlspecialchars(json_encode($method)) ?>)" 
                        class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                    Edit
                </button>
                <button onclick="toggleMethod(<?= $method['id'] ?>)" 
                        class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                    <?= $method['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                </button>
                <?php if ($method['type'] !== 'cash'): ?>
                <button onclick="deleteMethod(<?= $method['id'] ?>)" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Hapus
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="add-method-modal" class="hidden">
    <div class="modal-overlay" onclick="closeModal('add-method-modal')"></div>
    <div class="modal-content max-w-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-6" id="modal-title">Tambah Metode Pembayaran</h2>
        <form id="method-form">
            <input type="hidden" name="id" id="method-id">
            
            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Metode</label>
                    <input type="text" name="name" id="method-name" required class="form-input" 
                           placeholder="Contoh: Transfer BCA">
                </div>
                
                <div>
                    <label class="form-label">Jenis</label>
                    <select name="type" id="method-type" class="form-input" onchange="toggleBankFields()">
                        <option value="cash">Cash (Tunai)</option>
                        <option value="bank_transfer">Transfer Bank</option>
                    </select>
                </div>
                
                <div id="bank-fields" class="hidden space-y-4">
                    <div>
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" id="method-bank-name" class="form-input" 
                               placeholder="Contoh: BCA, BNI, BRI">
                    </div>
                    
                    <div>
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="account_number" id="method-account-number" class="form-input" 
                               placeholder="Contoh: 1234567890">
                    </div>
                    
                    <div>
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" id="method-account-holder" class="form-input" 
                               placeholder="Contoh: D'four Laundry">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal('add-method-modal')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleBankFields() {
    const type = document.getElementById('method-type').value;
    const bankFields = document.getElementById('bank-fields');
    
    if (type === 'bank_transfer') {
        bankFields.classList.remove('hidden');
        document.getElementById('method-bank-name').required = true;
        document.getElementById('method-account-number').required = true;
    } else {
        bankFields.classList.add('hidden');
        document.getElementById('method-bank-name').required = false;
        document.getElementById('method-account-number').required = false;
    }
}

function editMethod(method) {
    document.getElementById('modal-title').textContent = 'Edit Metode Pembayaran';
    document.getElementById('method-id').value = method.id;
    document.getElementById('method-name').value = method.name;
    document.getElementById('method-type').value = method.type;
    document.getElementById('method-bank-name').value = method.bank_name || '';
    document.getElementById('method-account-number').value = method.account_number || '';
    document.getElementById('method-account-holder').value = method.account_holder || '';
    
    toggleBankFields();
    openModal('add-method-modal');
}

function resetForm() {
    document.getElementById('modal-title').textContent = 'Tambah Metode Pembayaran';
    document.getElementById('method-form').reset();
    document.getElementById('method-id').value = '';
    toggleBankFields();
}

// Override openModal to reset form
const originalOpenModal = window.openModal;
window.openModal = function(id) {
    if (id === 'add-method-modal' && !document.getElementById('method-id').value) {
        resetForm();
    }
    originalOpenModal(id);
};

// Form submission
document.getElementById('method-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEdit = formData.get('id');
    formData.append('action', isEdit ? 'update' : 'create');
    
    try {
        const response = await fetch('../api/payment-methods-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(result.message || 'Gagal menyimpan', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
});

async function toggleMethod(id) {
    try {
        const formData = new FormData();
        formData.append('action', 'toggle');
        formData.append('id', id);
        
        const response = await fetch('../api/payment-methods-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Status berhasil diubah', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(result.message || 'Gagal mengubah status', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}

async function deleteMethod(id) {
    if (!confirm('Yakin ingin menghapus metode pembayaran ini?')) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        const response = await fetch('../api/payment-methods-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Berhasil dihapus', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(result.message || 'Gagal menghapus', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
