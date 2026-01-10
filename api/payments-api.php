<?php
/**
 * Payments API
 * 
 * Endpoints:
 * - POST action=confirm - Confirm payment (mark as paid)
 * - GET ?action=pending - Get pending payments
 * - GET ?action=history&transaction_id=X - Get payment history
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'confirm':
            $transactionId = $_POST['transaction_id'] ?? 0;
            $paymentMethodId = $_POST['payment_method_id'] ?? 1; // Default cash
            $notes = trim($_POST['notes'] ?? '');
            
            // Get transaction
            $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
            $stmt->execute([$transactionId]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$transaction) {
                $response['message'] = 'Transaksi tidak ditemukan';
                break;
            }
            
            // Check if already paid
            if ($transaction['payment_status'] === 'paid') {
                $response['message'] = 'Transaksi sudah dibayar sebelumnya';
                break;
            }
            
            // Update transaction payment status
            $stmt = $db->prepare("
                UPDATE transactions 
                SET payment_status = 'paid', payment_method_id = ?
                WHERE id = ?
            ");
            $stmt->execute([$paymentMethodId, $transactionId]);
            
            // Insert payment record
            $adminId = getUserData()['id'] ?? null;
            $stmt = $db->prepare("
                INSERT INTO payments (transaction_id, payment_method_id, amount, status, paid_at, confirmed_by, notes)
                VALUES (?, ?, ?, 'paid', NOW(), ?, ?)
            ");
            $stmt->execute([$transactionId, $paymentMethodId, $transaction['price'], $adminId, $notes]);
            
            $response['success'] = true;
            $response['message'] = 'Pembayaran berhasil dikonfirmasi';
            break;
            
        case 'pending':
            $stmt = $db->query("
                SELECT t.*, c.name as customer_name, c.phone as customer_phone
                FROM transactions t
                JOIN customers c ON t.customer_id = c.id
                WHERE t.payment_status = 'unpaid' OR t.payment_status IS NULL
                ORDER BY t.created_at DESC
            ");
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'history':
            $transactionId = $_GET['transaction_id'] ?? 0;
            $stmt = $db->prepare("
                SELECT p.*, pm.name as method_name, u.name as confirmed_by_name
                FROM payments p
                LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                LEFT JOIN users u ON p.confirmed_by = u.id
                WHERE p.transaction_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$transactionId]);
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default:
            $response['message'] = 'Action tidak valid';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>
