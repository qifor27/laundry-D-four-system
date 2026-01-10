<?php
/**
 * Payment Helper Functions
 */

require_once __DIR__ . '/../config/database_mysql.php';

/**
 * Get all active payment methods
 */
function getActivePaymentMethods() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY type, name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get payment method by ID
 */
function getPaymentMethod($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM payment_methods WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get bank transfer methods only
 */
function getBankTransferMethods() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("
        SELECT * FROM payment_methods 
        WHERE type = 'bank_transfer' AND is_active = 1 
        ORDER BY name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Check if transaction is paid
 */
function isTransactionPaid($transactionId) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT payment_status FROM transactions WHERE id = ?");
    $stmt->execute([$transactionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['payment_status'] === 'paid';
}

/**
 * Get payment status label (Indonesian)
 */
function getPaymentStatusLabel($status) {
    $labels = [
        'unpaid' => 'Belum Bayar',
        'pending' => 'Menunggu Konfirmasi',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Get payment status badge class
 */
function getPaymentStatusBadge($status) {
    $badges = [
        'unpaid' => 'bg-red-100 text-red-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-gray-100 text-gray-800'
    ];
    return $badges[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get payment history for a transaction
 */
function getPaymentHistory($transactionId) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT p.*, pm.name as method_name, u.name as confirmed_by_name
        FROM payments p
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
        LEFT JOIN users u ON p.confirmed_by = u.id
        WHERE p.transaction_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$transactionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
