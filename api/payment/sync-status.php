<?php

/**
 * Sync Payment Status from Midtrans API
 * 
 * Endpoint: POST /api/payment/sync-status.php
 * Body: { "transaction_id": 123 }
 * 
 * This endpoint checks payment status directly from Midtrans API
 * and updates the local database. Useful for localhost testing
 * where webhook cannot be received.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/midtrans.php';
require_once __DIR__ . '/../../includes/auth.php';

try {
    if (!isLoggedIn()) {
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $transactionId = $input['transaction_id'] ?? $_GET['transaction_id'] ?? 0;

    if (!$transactionId) {
        throw new Exception('Transaction ID diperlukan');
    }

    $transactionId = (int) $transactionId;
    $db = Database::getInstance()->getConnection();

    // Get transaction with latest Midtrans order_id from notes
    $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception('Transaksi tidak ditemukan');
    }

    // Extract Midtrans order_id from notes - get the LAST one (most recent)
    preg_match_all('/\[Midtrans Order: (DFOUR-\d+-\d+)\]/', $transaction['notes'] ?? '', $matches);
    $orderIds = $matches[1] ?? [];
    $orderId = !empty($orderIds) ? end($orderIds) : null; // Get the last/most recent order_id

    if (!$orderId) {
        throw new Exception('Transaksi ini belum pernah diproses melalui Midtrans');
    }

    // Check status from Midtrans API
    $midtransStatus = checkMidtransStatus($orderId);

    if (!$midtransStatus) {
        throw new Exception('Gagal mengambil status dari Midtrans');
    }

    // Determine payment status
    $transactionStatus = $midtransStatus['transaction_status'] ?? 'unknown';
    $fraudStatus = $midtransStatus['fraud_status'] ?? 'accept';

    $paymentStatus = 'unpaid';
    $statusMessage = '';

    if ($transactionStatus == 'capture') {
        if ($fraudStatus == 'accept') {
            $paymentStatus = 'paid';
            $statusMessage = 'Pembayaran berhasil (capture)';
        }
    } else if ($transactionStatus == 'settlement') {
        $paymentStatus = 'paid';
        $statusMessage = 'Pembayaran berhasil (settlement)';
    } else if ($transactionStatus == 'pending') {
        $paymentStatus = 'pending';
        $statusMessage = 'Menunggu pembayaran';
    } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
        $paymentStatus = 'unpaid';
        $statusMessage = "Pembayaran $transactionStatus";
    } else {
        $statusMessage = "Status: $transactionStatus";
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

    // If paid, also insert/update payments table
    if ($paymentStatus === 'paid') {
        $grossAmount = $midtransStatus['gross_amount'] ?? $transaction['price'];
        $paidAt = date('Y-m-d H:i:s');

        // Get or create Midtrans payment method
        $stmt = $db->prepare("SELECT id FROM payment_methods WHERE name = 'Midtrans' LIMIT 1");
        $stmt->execute();
        $method = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$method) {
            $stmt = $db->prepare("INSERT INTO payment_methods (name, type, is_active) VALUES ('Midtrans', 'ewallet', 1)");
            $stmt->execute();
            $paymentMethodId = $db->lastInsertId();
        } else {
            $paymentMethodId = $method['id'];
        }

        // Insert payment record
        $stmt = $db->prepare("
            INSERT INTO payments (transaction_id, payment_method_id, amount, status, paid_at, notes)
            VALUES (?, ?, ?, 'paid', ?, ?)
            ON DUPLICATE KEY UPDATE 
                status = 'paid',
                paid_at = VALUES(paid_at),
                notes = VALUES(notes)
        ");
        $stmt->execute([
            $transactionId,
            $paymentMethodId,
            $grossAmount,
            $paidAt,
            "Synced from Midtrans: $orderId"
        ]);
    }

    echo json_encode([
        'success' => true,
        'transaction_id' => $transactionId,
        'order_id' => $orderId,
        'midtrans_status' => $transactionStatus,
        'payment_status' => $paymentStatus,
        'message' => $statusMessage
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Check payment status from Midtrans API
 */
function checkMidtransStatus($orderId)
{
    $url = MIDTRANS_IS_PRODUCTION
        ? "https://api.midtrans.com/v2/$orderId/status"
        : "https://api.sandbox.midtrans.com/v2/$orderId/status";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Midtrans Status Check Error: HTTP $httpCode - $response");
        return null;
    }

    return json_decode($response, true);
}
