<?php
/**
 * Customers Page
 * CRUD untuk manajemen pelanggan
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin(); // Redirect to login if not authenticated

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Pelanggan - D'four Laundry";
$db = Database::getInstance()->getConnection();

// Handle POST requests (Create/Update/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $name = sanitize($_POST['name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address'] ?? '');
        
        try {
            $stmt = $db->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
            $stmt->execute([$name, $phone, $address]);
            setFlashMessage('success', 'Pelanggan berhasil ditambahkan!');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal menambahkan pelanggan: ' . $e->getMessage());
        }
        redirect('customers.php');
    }
    
    if ($action === 'update') {
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address'] ?? '');
        
        try {
            $stmt = $db->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $address, $id]);
            setFlashMessage('success', 'Pelanggan berhasil diupdate!');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal update pelanggan: ' . $e->getMessage());
        }
        redirect('customers.php');
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Pelanggan berhasil dihapus!');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Gagal hapus pelanggan: ' . $e->getMessage());
        }
        redirect('customers.php');
    }
}

// Get all customers with registration status
$customers = $db->query("
    SELECT c.*, 
           u.email as user_email,
           CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
    FROM customers c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
")->fetchAll();

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Customers Content -->
<div>
    <!-- Add Customer Button -->
    <div class="mb-6 flex justify-end">
        <button onclick="openModal('add-customer-modal')" class="btn-primary">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Pelanggan
        </button>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-6">
        <input type="text" id="search-input" placeholder="Cari pelanggan..." 
               class="form-input max-w-md" onkeyup="filterTable('search-input', 'customers-table')">
    </div>
    
    <!-- Customers Table -->
    <div class="card">
        <?php if (empty($customers)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 mb-4">Belum ada data pelanggan</p>
                <button onclick="openModal('add-customer-modal')" class="btn-primary">
                    Tambah Pelanggan Pertama
                </button>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table" id="customers-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Terdaftar</th>
                            <th> Status </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td class="font-mono">#<?= $customer['id'] ?></td>
                            <td class="font-medium text-gray-900"><?= htmlspecialchars($customer['name']) ?></td>
                            <td><?= htmlspecialchars($customer['phone']) ?></td>
                            <td class="text-sm text-gray-600"><?= htmlspecialchars($customer['address']) ?: '-' ?></td>
                            <td class="text-sm text-gray-600"><?= formatDate($customer['created_at'], false) ?></td>
                            <td>
                                <?php if ($customer['is_registered']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terdaftar</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Belum Daftar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button onclick='editCustomer(<?= json_encode($customer) ?>)' 
                                            class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        Edit
                                    </button>
                                    <button onclick="deleteCustomer(<?= $customer['id'] ?>)" 
                                            class="text-red-600 hover:text-red-800 font-medium text-sm">
                                        Hapus
                                    </button>
                                    <?php if (!$customer['is_registered']): ?>
                                    <button onclick="inviteCustomer('<?= htmlspecialchars($customer['phone']) ?>')" 
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        Undang
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Total: <span class="font-semibold"><?= count($customers) ?></span> pelanggan
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="add-customer-modal" class="hidden">
    <div class="modal-overlay" onclick="closeModal('add-customer-modal')"></div>
    <div class="modal-content">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Tambah Pelanggan Baru</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-4">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" required class="form-input" placeholder="Masukkan nama lengkap">
            </div>
            
            <div class="mb-4">
                <label class="form-label">No. Telepon</label>
                <input type="tel" name="phone" required class="form-input" placeholder="08123456789" 
                       oninput="formatPhoneNumber(this)">
            </div>
            
            <div class="mb-6">
                <label class="form-label">Alamat</label>
                <textarea name="address" rows="3" class="form-input" placeholder="Masukkan alamat (opsional)"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('add-customer-modal')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="edit-customer-modal" class="hidden">
    <div class="modal-overlay" onclick="closeModal('edit-customer-modal')"></div>
    <div class="modal-content">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Pelanggan</h2>
        <form method="POST" action="" id="edit-form">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-id">
            
            <div class="mb-4">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required class="form-input">
            </div>
            
            <div class="mb-4">
                <label class="form-label">No. Telepon</label>
                <input type="tel" name="phone" id="edit-phone" required class="form-input">
            </div>
            
            <div class="mb-6">
                <label class="form-label">Alamat</label>
                <textarea name="address" id="edit-address" rows="3" class="form-input"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('edit-customer-modal')" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editCustomer(customer) {
    document.getElementById('edit-id').value = customer.id;
    document.getElementById('edit-name').value = customer.name;
    document.getElementById('edit-phone').value = customer.phone;
    document.getElementById('edit-address').value = customer.address || '';
    openModal('edit-customer-modal');
}

function deleteCustomer(id) {
    if (confirm('Yakin ingin menghapus pelanggan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function inviteCustomer(phone) {
    const registerUrl = `${window.location.origin}/laundry-D-four/pages/auth/register.php?phone=${phone}`;
    
    // Copy ke clipboard
    navigator.clipboard.writeText(registerUrl).then(() => {
        alert('Link registrasi sudah dicopy!\n\nKirim ke customer via WhatsApp.');
    }).catch(() => {
        // Fallback: buka WhatsApp langsung
        const waUrl = `https://wa.me/62${phone.substring(1)}?text=Silakan daftar di D'four Laundry: ${encodeURIComponent(registerUrl)}`;
        window.open(waUrl, '_blank');
    });
}
</script>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
