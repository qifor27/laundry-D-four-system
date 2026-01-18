<?php

/**
 * Create Midtrans Snap Token API
 * 
 * Endpoint: POST /api/payment/create-snap.php
 * Body: { "transaction_id": 123 }
 * 
 * Response: { "success": true, "snap_token": "xxx" }
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/midtrans.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

try {
    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('Silakan login terlebih dahulu');
    }

    // Check if Midtrans is configured
    if (!isMidtransConfigured()) {
        throw new Exception('Payment gateway belum dikonfigurasi');
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['transaction_id'])) {
        throw new Exception('Transaction ID diperlukan');
    }

    $transactionId = (int) $input['transaction_id'];
    $db = Database::getInstance()->getConnection();

    // Get transaction details
    $stmt = $db->prepare("
        SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.user_id
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE t.id = ?
    ");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception('Transaksi tidak ditemukan');
    }

    // Verify ownership - user can only pay their own transactions
    $userData = getUserData();
    if ($transaction['user_id'] != $userData['id']) {
        throw new Exception('Anda tidak memiliki akses ke transaksi ini');
    }

    // Check if already paid
    if ($transaction['payment_status'] === 'paid') {
        throw new Exception('Transaksi ini sudah dibayar');
    }

    // Generate unique order ID
    $orderId = 'DFOUR-' . $transactionId . '-' . time();

    // Prepare Midtrans parameters
    $params = [
        'transaction_details' => [
            'order_id' => $orderId,
            'gross_amount' => (int) $transaction['price'],
        ],
        'customer_details' => [
            'first_name' => $transaction['customer_name'],
            'phone' => $transaction['customer_phone'],
        ],
        'item_details' => [
            [
                'id' => 'TRX-' . $transactionId,
                'price' => (int) $transaction['price'],
                'quantity' => 1,
                'name' => $transaction['service_type'] . ' (' . ($transaction['weight'] > 0 ? $transaction['weight'] . ' kg' : $transaction['quantity'] . ' pcs') . ')',
            ]
        ],
        'callbacks' => [
            'finish' => baseUrl('pages/payment.php?status=finish&trx=' . $transactionId),
        ]
    ];

    // Call Midtrans Snap API
    $snapToken = createSnapToken($params);

    if (!$snapToken) {
        throw new Exception('Gagal membuat token pembayaran');
    }

    // Store order_id in session or database for verification later
    $stmt = $db->prepare("UPDATE transactions SET notes = CONCAT(IFNULL(notes, ''), '\n[Midtrans Order: $orderId]') WHERE id = ?");
    $stmt->execute([$transactionId]);

    echo json_encode([
        'success' => true,
        'snap_token' => $snapToken,
        'order_id' => $orderId,
        'redirect_url' => MIDTRANS_IS_PRODUCTION
            ? "https://app.midtrans.com/snap/v2/vtweb/$snapToken"
            : "https://app.sandbox.midtrans.com/snap/v2/vtweb/$snapToken"
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Create Midtrans Snap Token
 */
function createSnapToken($params)
{
    $url = MIDTRANS_IS_PRODUCTION
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For localhost testing
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Debug logging
    error_log("Midtrans Request URL: $url");
    error_log("Midtrans Request Params: " . json_encode($params));
    error_log("Midtrans Response Code: $httpCode");
    error_log("Midtrans Response: $response");
    if ($curlError) {
        error_log("Midtrans CURL Error: $curlError");
    }

    if ($httpCode !== 201) {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error_messages'][0] ?? $response;
        throw new Exception("Midtrans Error: $errorMessage");
    }

    $result = json_decode($response, true);
    return $result['token'] ?? null;
}
