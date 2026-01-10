<?php
/**
 * WhatsApp Helper Functions (Manual - Click to Chat)
 * 
 * Gratis 100% - Menggunakan WhatsApp Web Click-to-Chat
 * TERINTEGRASI dengan Payment Methods (data bank dari database)
 */

require_once __DIR__ . '/../config/database_mysql.php';

// ================================================
// CONFIGURATION
// ================================================

define('WA_BUSINESS_NAME', "D'four Laundry");
define('WA_BUSINESS_ADDRESS', "Jl. Contoh No. 123");
define('WA_BUSINESS_HOURS', "08.00 - 21.00 WIB");

// ================================================
// URL GENERATOR
// ================================================

/**
 * Generate WhatsApp Click-to-Chat URL
 */
function generateWhatsAppURL($phone, $message) {
    $phone = formatPhoneNumber($phone);
    $encodedMessage = urlencode($message);
    return "https://wa.me/{$phone}?text={$encodedMessage}";
}

/**
 * Format phone number to international format (tanpa +)
 */
function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }
    
    return $phone;
}

// ================================================
// DYNAMIC BANK LIST FROM DATABASE
// ================================================

/**
 * Get bank list from database for WhatsApp message
 * Returns formatted string with all active bank accounts
 */
function getBankListForWA() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT bank_name, account_number, account_holder 
            FROM payment_methods 
            WHERE type = 'bank_transfer' AND is_active = 1
            ORDER BY name
        ");
        $banks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($banks)) {
            return "   (Belum ada rekening terdaftar)";
        }
        
        $text = "";
        foreach ($banks as $bank) {
            $text .= "\n   🏦 {$bank['bank_name']} - {$bank['account_number']}";
            $text .= "\n      A/N: {$bank['account_holder']}";
        }
        return $text;
    } catch (Exception $e) {
        return "\n   🏦 (Error loading bank data)";
    }
}

// ================================================
// MESSAGE GENERATORS
// ================================================

/**
 * Generate Order Created Message
 */
function getOrderCreatedMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 👋

Pesanan Anda telah kami terima:
📦 No. Pesanan: #{$transaction['id']}
🧺 Layanan: {$transaction['service_type']}
💰 Total: Rp {$price}

Kami akan segera memproses pesanan Anda.

Terima kasih! 🙏";
}

/**
 * Generate Status Update Message
 */
function getStatusUpdateMessage($transaction, $customer, $status) {
    $statusLabels = [
        'pending' => 'Menunggu Proses',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai',
        'picked_up' => 'Sudah Diambil'
    ];
    
    $statusLabel = $statusLabels[$status] ?? $status;
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}!

Status pesanan Anda telah diupdate:
📦 No. Pesanan: #{$transaction['id']}
📊 Status: *{$statusLabel}*

Terima kasih telah menunggu! 🙏";
}

/**
 * Generate Ready for Pickup Message (dengan opsi pembayaran DINAMIS)
 */
function getReadyForPickupMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    $bankList = getBankListForWA();
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 🎉

Kabar baik! Pesanan Anda sudah selesai:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat (Cash)
2️⃣ Transfer Bank:{$bankList}

📍 Lokasi: " . WA_BUSINESS_ADDRESS . "
🕐 Jam: " . WA_BUSINESS_HOURS . "

Ditunggu kedatangannya! 😊";
}

/**
 * Generate Payment Reminder Message (DINAMIS)
 */
function getPaymentReminderMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    $bankList = getBankListForWA();
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 👋

Kami ingin mengingatkan bahwa pesanan Anda belum dibayar:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat saat ambil
2️⃣ Transfer Bank:{$bankList}

Jika sudah transfer, mohon konfirmasi ke admin.

Terima kasih! 🙏";
}

// ================================================
// URL GENERATORS (untuk tombol)
// ================================================

function getOrderCreatedWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getOrderCreatedMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

function getStatusUpdateWAUrl($transaction, $customer, $status) {
    if (empty($customer['phone'])) return null;
    $message = getStatusUpdateMessage($transaction, $customer, $status);
    return generateWhatsAppURL($customer['phone'], $message);
}

function getReadyForPickupWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getReadyForPickupMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

function getPaymentReminderWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getPaymentReminderMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get all WhatsApp URLs for a transaction
 */
function getAllWhatsAppUrls($transaction, $customer) {
    return [
        'order_created' => getOrderCreatedWAUrl($transaction, $customer),
        'status_update' => getStatusUpdateWAUrl($transaction, $customer, $transaction['status'] ?? 'pending'),
        'ready_pickup' => getReadyForPickupWAUrl($transaction, $customer),
        'payment_reminder' => getPaymentReminderWAUrl($transaction, $customer)
    ];
}
?>
