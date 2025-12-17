<?php
/**
 * Customers API
 * Handle AJAX requests untuk customers
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            // Create new customer
            $name = sanitize($_POST['name'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $address = sanitize($_POST['address'] ?? '');
            
            // Validation
            if (empty($name) || empty($phone)) {
                throw new Exception('Nama dan nomor telepon wajib diisi');
            }
            
            // Validate phone format
            if (!validatePhone($phone)) {
                throw new Exception('Format nomor telepon tidak valid');
            }
            
            $stmt = $db->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
            $stmt->execute([$name, $phone, $address]);
            
            $response['success'] = true;
            $response['message'] = 'Pelanggan berhasil ditambahkan';
            $response['customer_id'] = $db->lastInsertId();
            break;
            
        case 'update':
            // Update customer
            $id = $_POST['id'] ?? 0;
            $name = sanitize($_POST['name'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $address = sanitize($_POST['address'] ?? '');
            
            if (empty($name) || empty($phone)) {
                throw new Exception('Nama dan nomor telepon wajib diisi');
            }
            
            $stmt = $db->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $address, $id]);
            
            $response['success'] = true;
            $response['message'] = 'Pelanggan berhasil diupdate';
            break;
            
        case 'delete':
            // Delete customer
            $id = $_POST['id'] ?? 0;
            
            // Check if customer has transactions
            $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM transactions WHERE customer_id = ?");
            $checkStmt->execute([$id]);
            $count = $checkStmt->fetch()['count'];
            
            if ($count > 0) {
                throw new Exception('Tidak dapat menghapus pelanggan yang memiliki transaksi');
            }
            
            $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            
            $response['success'] = true;
            $response['message'] = 'Pelanggan berhasil dihapus';
            break;
            
        case 'get_by_id':
            // Get single customer
            $id = $_GET['id'] ?? 0;
            
            $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                throw new Exception('Pelanggan tidak ditemukan');
            }
            
            $response['success'] = true;
            $response['data'] = $customer;
            break;
            
        case 'get_by_phone':
            // Search customer by phone
            $phone = $_GET['phone'] ?? '';
            
            $stmt = $db->prepare("SELECT * FROM customers WHERE phone LIKE ?");
            $stmt->execute(["%{$phone}%"]);
            $customers = $stmt->fetchAll();
            
            $response['success'] = true;
            $response['data'] = $customers;
            break;
            
        case 'get_all':
            // Get all customers
            $stmt = $db->query("SELECT * FROM customers ORDER BY name ASC");
            
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll();
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
