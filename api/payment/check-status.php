<?php

/**
 * Check Payment Status API
 * 
 * Endpoint: GET /api/payment/check-status.php?transaction_id=123
 * 
 * Response: { "success": true, "payment_status": "paid" }
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/midtrans.php';
require_once __DIR__ . '/../../includes/auth.php';

try {
    if (!isset($_GET['transaction_id'])) {
        throw new Exception('Transaction ID diperlukan');
    }

    $transactionId = (int) $_GET['transaction_id'];
    $db = Database::getInstance()->getConnection();

    // Get transaction
    $stmt = $db->prepare("
        SELECT t.*, c.user_id
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE t.id = ?
    ");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception('Transaksi tidak ditemukan');
    }

    // Optional: Check ownership if user is logged in
    if (isLoggedIn()) {
        $userData = getUserData();
        // Only check ownership for regular users, not admins
        if ($userData['role'] === 'user' && $transaction['user_id'] != $userData['id']) {
            throw new Exception('Anda tidak memiliki akses ke transaksi ini');
        }
    }

    // Get payment details if exists
    $stmt = $db->prepare("
        SELECT p.*, pm.name as payment_method_name
        FROM payments p
        JOIN payment_methods pm ON p.payment_method_id = pm.id
        WHERE p.transaction_id = ?
        ORDER BY p.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$transactionId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'transaction_id' => $transactionId,
        'payment_status' => $transaction['payment_status'],
        'price' => (float) $transaction['price'],
        'payment' => $payment ? [
            'method' => $payment['payment_method_name'],
            'amount' => (float) $payment['amount'],
            'status' => $payment['status'],
            'paid_at' => $payment['paid_at'],
        ] : null
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
