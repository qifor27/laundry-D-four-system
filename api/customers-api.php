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
            
            $stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $userId = $user ? $user['id'] : null;
            $registeredAt = $user ? date('Y-m-d H:i:s') : null;
            
            $stmt = $db->prepare("INSERT INTO customers (user_id, name, phone, address, registered_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $phone, $address, $registeredAt]);

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
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("
                SELECT c.*,
                       u.email as user_email,
                       CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
                FROM customers c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.id = ?
            ");
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
            
            $stmt = $db->prepare("
                SELECT c.*,
                       u.email as user_email,
                       CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered
                FROM customers c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.phone LIKE ?
            ");
            $stmt->execute(["%{$phone}%"]);
            $customers = $stmt->fetchAll();

            $response['success'] = true;
            $response['data'] = $customers;
            break;
            
        case 'get_all':
            // Get all customers
          $stmt = $db->query("
          SELECT c.*,
          u.email as user_email,
          CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END as
          is_registered
          FROM customers c
          LEFT JOIN users u ON c.user_id = u.id
          ORDER BY c.name ASC");

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
