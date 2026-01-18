<?php

/**
 * Invoice Page
 * 
 * Halaman nota digital untuk transaksi
 * URL: /pages/invoice.php?id={transaction_id}
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../config/payment-info.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/invoice-template.php';

// Get transaction ID
$transactionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($transactionId <= 0) {
    die('ID transaksi tidak valid');
}

$db = Database::getInstance()->getConnection();

// Get transaction with customer info
$stmt = $db->prepare("
    SELECT t.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address
    FROM transactions t
    JOIN customers c ON t.customer_id = c.id
    WHERE t.id = ?
");
$stmt->execute([$transactionId]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die('Transaksi tidak ditemukan');
}

$customer = [
    'name' => $transaction['customer_name'],
    'phone' => $transaction['customer_phone'],
    'address' => $transaction['customer_address']
];

// Render and output the invoice
echo renderInvoice($transaction, $customer);
