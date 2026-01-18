<?php

/**
 * Payment Info Configuration
 * 
 * Konfigurasi informasi pembayaran manual (QRIS dan Rekening Bank)
 * untuk pelanggan yang tidak menggunakan Midtrans
 */

// ================================================
// QRIS CONFIGURATION
// ================================================

// Path ke gambar QRIS (relatif dari root project)
define('QRIS_IMAGE_PATH', 'assets/images/qris.jpg');

// Informasi QRIS
define('QRIS_MERCHANT_NAME', 'MUHAMMAD ROFIQUL ISLAM Y, DIGITAL & KREATIF');
define('QRIS_NMID', 'ID1026473007627');

// ================================================
// BANK ACCOUNT CONFIGURATION
// ================================================

// Rekening Bank (array untuk multiple bank)
$PAYMENT_BANK_ACCOUNTS = [
    [
        'bank' => 'BNI',
        'account_number' => '2019082060',  // GANTI dengan nomor rekening asli
        'account_name' => 'MUHAMMAD ROFIQUL ISLAM ',
    ],
    // Tambah bank lain jika perlu
    // [
    //     'bank' => 'BRI',
    //     'account_number' => '0987654321',
    //     'account_name' => 'MUHAMMAD ROFIQUL ISLAM YUSUF',
    // ],
];

// ================================================
// WHATSAPP CONFIGURATION
// ================================================

// Nomor WhatsApp untuk konfirmasi pembayaran (format: 62xxx tanpa +)
define('PAYMENT_WHATSAPP_NUMBER', '62895337252897'); // GANTI dengan nomor WA asli

// ================================================
// HELPER FUNCTIONS
// ================================================

/**
 * Get QRIS image URL
 */
function getQrisImageUrl()
{
    return baseUrl(QRIS_IMAGE_PATH);
}

/**
 * Get bank accounts for payment
 */
function getPaymentBankAccounts()
{
    global $PAYMENT_BANK_ACCOUNTS;
    return $PAYMENT_BANK_ACCOUNTS;
}

/**
 * Get WhatsApp link for payment confirmation
 * @param float $amount Total pembayaran
 * @param string $transactionId ID transaksi
 */
function getPaymentWhatsAppLink($amount, $transactionId)
{
    $message = "Halo, saya ingin konfirmasi pembayaran:\n\n";
    $message .= "📋 ID Transaksi: #$transactionId\n";
    $message .= "💰 Total: Rp " . number_format($amount, 0, ',', '.') . "\n\n";
    $message .= "Saya sudah transfer ke rekening/QRIS D'four Laundry.";

    return 'https://wa.me/' . PAYMENT_WHATSAPP_NUMBER . '?text=' . urlencode($message);
}

/**
 * Format payment info for WhatsApp reminder
 */
function formatPaymentInfoForWhatsApp($amount)
{
    global $PAYMENT_BANK_ACCOUNTS;

    $text = "\n\n💳 *INFORMASI PEMBAYARAN*\n";
    $text .= "━━━━━━━━━━━━━━━\n";
    $text .= "💰 Total: *Rp " . number_format($amount, 0, ',', '.') . "*\n\n";

    // QRIS
    $text .= "📱 *QRIS*\n";
    $text .= "Nama: " . QRIS_MERCHANT_NAME . "\n";
    $text .= "NMID: " . QRIS_NMID . "\n\n";

    // Bank Transfer
    if (!empty($PAYMENT_BANK_ACCOUNTS)) {
        $text .= "🏦 *TRANSFER BANK*\n";
        foreach ($PAYMENT_BANK_ACCOUNTS as $bank) {
            $text .= "• {$bank['bank']}: {$bank['account_number']}\n";
            $text .= "  a.n. {$bank['account_name']}\n";
        }
    }

    $text .= "\n📞 Konfirmasi pembayaran via WA ini ya!";

    return $text;
}
