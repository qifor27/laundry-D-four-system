<?php
/**
 * WhatsApp Helper Functions (Manual - Click to Chat)
 * 
 * Gratis 100% - Menggunakan WhatsApp Web Click-to-Chat
 * Tidak memerlukan API atau biaya bulanan.
 * 
 * Cara kerja:
 * 1. Generate URL WhatsApp dengan pesan terisi
 * 2. Admin klik tombol, WhatsApp Web terbuka
 * 3. Admin klik Send di WhatsApp Web
 */

// ================================================
// CONFIGURATION
// ================================================

define('WA_BUSINESS_NAME', "D'four Laundry");
define('WA_BUSINESS_ADDRESS', "Jl. Contoh No. 123");
define('WA_BUSINESS_HOURS', "08.00 - 21.00 WIB");

// Info Rekening Bank (sesuaikan dengan rekening Anda)
define('WA_BANK_NAME', 'BCA');
define('WA_BANK_ACCOUNT', '1234567890');
define('WA_BANK_HOLDER', "D'four Laundry");

// ================================================
// URL GENERATOR
// ================================================

/**
 * Generate WhatsApp Click-to-Chat URL
 * 
 * @param string $phone Nomor HP (08xxx atau 628xxx)
 * @param string $message Pesan
 * @return string WhatsApp Web URL
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
    // Remove non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert 08xxx to 628xxx
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    // Add 62 if not present
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }
    
    return $phone;
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
 * Generate Ready for Pickup Message (dengan opsi pembayaran)
 */
function getReadyForPickupMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 🎉

Kabar baik! Pesanan Anda sudah selesai:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat (Cash)
2️⃣ Transfer Bank:
   🏦 " . WA_BANK_NAME . " - " . WA_BANK_ACCOUNT . "
   👤 A/N: " . WA_BANK_HOLDER . "

📍 Lokasi: " . WA_BUSINESS_ADDRESS . "
🕐 Jam: " . WA_BUSINESS_HOURS . "

Ditunggu kedatangannya! 😊";
}

/**
 * Generate Payment Reminder Message
 */
function getPaymentReminderMessage($transaction, $customer) {
    $price = number_format($transaction['price'], 0, ',', '.');
    
    return "*" . WA_BUSINESS_NAME . "*

Halo {$customer['name']}! 👋

Kami ingin mengingatkan bahwa pesanan Anda belum dibayar:
📦 No. Pesanan: #{$transaction['id']}
💰 Total: Rp {$price}

💳 *Opsi Pembayaran:*
1️⃣ Bayar di tempat saat ambil
2️⃣ Transfer Bank:
   🏦 " . WA_BANK_NAME . " - " . WA_BANK_ACCOUNT . "
   👤 A/N: " . WA_BANK_HOLDER . "

Jika sudah transfer, mohon konfirmasi ke admin.

Terima kasih! 🙏";
}

// ================================================
// COMPLETE URL GENERATORS (untuk tombol)
// ================================================

/**
 * Get WhatsApp URL for Order Created
 */
function getOrderCreatedWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getOrderCreatedMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Status Update
 */
function getStatusUpdateWAUrl($transaction, $customer, $status) {
    if (empty($customer['phone'])) return null;
    $message = getStatusUpdateMessage($transaction, $customer, $status);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Ready Pickup
 */
function getReadyForPickupWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getReadyForPickupMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get WhatsApp URL for Payment Reminder
 */
function getPaymentReminderWAUrl($transaction, $customer) {
    if (empty($customer['phone'])) return null;
    $message = getPaymentReminderMessage($transaction, $customer);
    return generateWhatsAppURL($customer['phone'], $message);
}

/**
 * Get all WhatsApp URLs for a transaction
 * Returns array of URLs for different message types
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
