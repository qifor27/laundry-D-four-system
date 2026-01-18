<?php

/**
 * Midtrans Payment Notification Handler (Webhook)
 * 
 * Endpoint: POST /api/payment/notification.php
 * 
 * This endpoint receives payment notifications from Midtrans.
 * Configure this URL in Midtrans Dashboard > Settings > Payment Notification URL
 */

// Log all incoming notifications for debugging
$rawInput = file_get_contents('php://input');
error_log("Midtrans Notification: " . $rawInput);

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/midtrans.php';

try {
    $notification = json_decode($rawInput, true);

    if (!$notification) {
        throw new Exception('Invalid notification data');
    }

    // Verify signature
    $orderId = $notification['order_id'];
    $statusCode = $notification['status_code'];
    $grossAmount = $notification['gross_amount'];
    $serverKey = MIDTRANS_SERVER_KEY;

    $signatureKey = $notification['signature_key'];
    $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

    if ($signatureKey !== $expectedSignature) {
        throw new Exception('Invalid signature');
    }

    // Extract transaction ID from order_id (format: DFOUR-{id}-{timestamp})
    preg_match('/DFOUR-(\d+)-/', $orderId, $matches);
    if (!isset($matches[1])) {
        throw new Exception('Invalid order ID format');
    }
    $transactionId = (int) $matches[1];

    $db = Database::getInstance()->getConnection();

    // Get current transaction
    $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception('Transaction not found');
    }

    // Determine payment status based on Midtrans transaction status
    $transactionStatus = $notification['transaction_status'];
    $fraudStatus = $notification['fraud_status'] ?? 'accept';

    $paymentStatus = 'unpaid';
    $paidAt = null;

    if ($transactionStatus == 'capture') {
        if ($fraudStatus == 'accept') {
            $paymentStatus = 'paid';
            $paidAt = date('Y-m-d H:i:s');
        }
    } else if ($transactionStatus == 'settlement') {
        $paymentStatus = 'paid';
        $paidAt = date('Y-m-d H:i:s');
    } else if ($transactionStatus == 'pending') {
        $paymentStatus = 'pending';
    } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
        $paymentStatus = 'unpaid';
    }

    // Update transaction payment status
    $stmt = $db->prepare("
        UPDATE transactions 
        SET payment_status = ?,
            payment_method = 'midtrans',
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$paymentStatus, $transactionId]);

    // Insert into payments table with reminder tracking info
    $paymentMethodId = getOrCreateMidtransPaymentMethod($db);

    // Calculate expiry time (24 hours from now for pending, null for paid)
    $expiresAt = ($paymentStatus === 'pending') ? date('Y-m-d H:i:s', strtotime('+24 hours')) : null;

    $stmt = $db->prepare("
        INSERT INTO payments (transaction_id, payment_method_id, amount, status, paid_at, notes, midtrans_order_id, expires_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            status = VALUES(status),
            paid_at = VALUES(paid_at),
            notes = VALUES(notes),
            expires_at = VALUES(expires_at),
            reminder_count = CASE WHEN VALUES(status) = 'paid' THEN 0 ELSE reminder_count END
    ");
    $stmt->execute([
        $transactionId,
        $paymentMethodId,
        $grossAmount,
        $paymentStatus,
        $paidAt,
        "Midtrans: $orderId - $transactionStatus",
        $orderId,
        $expiresAt
    ]);

    // Log success
    error_log("Payment Updated: Transaction $transactionId - Status: $paymentStatus");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Midtrans Notification Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Get or create Midtrans payment method
 */
function getOrCreateMidtransPaymentMethod($db)
{
    $stmt = $db->prepare("SELECT id FROM payment_methods WHERE name = 'Midtrans'");
    $stmt->execute();
    $method = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($method) {
        return $method['id'];
    }

    // Create new payment method
    $stmt = $db->prepare("INSERT INTO payment_methods (name, type) VALUES ('Midtrans', 'ewallet')");
    $stmt->execute();
    return $db->lastInsertId();
}
