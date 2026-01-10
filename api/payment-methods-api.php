<?php
/**
 * Payment Methods API
 * 
 * Endpoints:
 * - GET ?action=list - Get all payment methods
 * - GET ?action=get&id=X - Get single payment method
 * - GET ?action=active - Get active payment methods only
 * - POST action=create - Create new payment method
 * - POST action=update - Update payment method
 * - POST action=delete - Delete payment method
 * - POST action=toggle - Toggle active status
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/auth.php';

// Require admin login
requireRole(['admin', 'superadmin']);

$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'list':
            $stmt = $db->query("SELECT * FROM payment_methods ORDER BY type, name");
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'active':
            $stmt = $db->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY type, name");
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'banks':
            // Get only bank transfer methods (for WhatsApp)
            $stmt = $db->query("
                SELECT * FROM payment_methods 
                WHERE type = 'bank_transfer' AND is_active = 1 
                ORDER BY name
            ");
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("SELECT * FROM payment_methods WHERE id = ?");
            $stmt->execute([$id]);
            $method = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($method) {
                $response['success'] = true;
                $response['data'] = $method;
            } else {
                $response['message'] = 'Payment method not found';
            }
            break;
            
        case 'create':
            $name = trim($_POST['name'] ?? '');
            $type = $_POST['type'] ?? 'cash';
            $bankName = trim($_POST['bank_name'] ?? '') ?: null;
            $accountNumber = trim($_POST['account_number'] ?? '') ?: null;
            $accountHolder = trim($_POST['account_holder'] ?? '') ?: null;
            
            if (empty($name)) {
                $response['message'] = 'Nama metode pembayaran wajib diisi';
                break;
            }
            
            if ($type === 'bank_transfer' && (empty($bankName) || empty($accountNumber))) {
                $response['message'] = 'Nama bank dan nomor rekening wajib diisi untuk transfer bank';
                break;
            }
            
            $stmt = $db->prepare("
                INSERT INTO payment_methods (name, type, bank_name, account_number, account_holder)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $type, $bankName, $accountNumber, $accountHolder]);
            
            $response['success'] = true;
            $response['message'] = 'Metode pembayaran berhasil ditambahkan';
            $response['data'] = ['id' => $db->lastInsertId()];
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $type = $_POST['type'] ?? 'cash';
            $bankName = trim($_POST['bank_name'] ?? '') ?: null;
            $accountNumber = trim($_POST['account_number'] ?? '') ?: null;
            $accountHolder = trim($_POST['account_holder'] ?? '') ?: null;
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
            
            if (empty($name)) {
                $response['message'] = 'Nama metode pembayaran wajib diisi';
                break;
            }
            
            $stmt = $db->prepare("
                UPDATE payment_methods 
                SET name = ?, type = ?, bank_name = ?, account_number = ?, 
                    account_holder = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $type, $bankName, $accountNumber, $accountHolder, $isActive, $id]);
            
            $response['success'] = true;
            $response['message'] = 'Metode pembayaran berhasil diupdate';
            break;
            
        case 'toggle':
            $id = $_POST['id'] ?? 0;
            
            $stmt = $db->prepare("UPDATE payment_methods SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$id]);
            
            $response['success'] = true;
            $response['message'] = 'Status berhasil diubah';
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            // Check if method is used in any payment
            $stmt = $db->prepare("SELECT COUNT(*) FROM payments WHERE payment_method_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                $response['message'] = 'Tidak dapat menghapus: metode ini sudah digunakan dalam pembayaran';
                break;
            }
            
            // Check if used in transactions
            $stmt = $db->prepare("SELECT COUNT(*) FROM transactions WHERE payment_method_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                $response['message'] = 'Tidak dapat menghapus: metode ini sudah digunakan dalam transaksi';
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM payment_methods WHERE id = ?");
            $stmt->execute([$id]);
            
            $response['success'] = true;
            $response['message'] = 'Metode pembayaran berhasil dihapus';
            break;
            
        default:
            $response['message'] = 'Action tidak valid';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>
