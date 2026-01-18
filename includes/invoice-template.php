<?php

/**
 * Invoice Template
 * Template HTML untuk nota/invoice yang bisa dicetak
 */

function renderInvoice($transaction, $customer, $paymentInfo = null)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Nota #' . $transaction['id'] . ' - D\'four Laundry</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: "Segoe UI", Tahoma, sans-serif; 
                background: #f5f5f5;
                padding: 20px;
            }
            .invoice {
                max-width: 400px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                border-bottom: 2px dashed #e5e7eb;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }
            .logo {
                font-size: 28px;
                font-weight: 700;
                background: linear-gradient(135deg, #8b5cf6, #ec4899);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .tagline {
                color: #6b7280;
                font-size: 12px;
                margin-top: 4px;
            }
            .invoice-number {
                background: #f3f4f6;
                padding: 8px 16px;
                border-radius: 20px;
                display: inline-block;
                margin-top: 12px;
                font-weight: 600;
                color: #374151;
            }
            .section {
                margin-bottom: 20px;
            }
            .section-title {
                font-size: 11px;
                text-transform: uppercase;
                color: #9ca3af;
                letter-spacing: 1px;
                margin-bottom: 8px;
            }
            .customer-name {
                font-weight: 600;
                font-size: 16px;
                color: #1f2937;
            }
            .customer-phone {
                color: #6b7280;
                font-size: 14px;
            }
            .detail-row {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f3f4f6;
            }
            .detail-label {
                color: #6b7280;
            }
            .detail-value {
                font-weight: 500;
                color: #1f2937;
            }
            .total-section {
                background: linear-gradient(135deg, #8b5cf6, #ec4899);
                color: white;
                padding: 20px;
                border-radius: 12px;
                margin-top: 20px;
            }
            .total-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .total-label {
                font-size: 14px;
                opacity: 0.9;
            }
            .total-amount {
                font-size: 24px;
                font-weight: 700;
            }
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                margin-top: 8px;
            }
            .status-unpaid { background: #fef3c7; color: #92400e; }
            .status-pending { background: #dbeafe; color: #1e40af; }
            .status-paid { background: #d1fae5; color: #065f46; }
            .footer {
                text-align: center;
                margin-top: 24px;
                padding-top: 20px;
                border-top: 2px dashed #e5e7eb;
            }
            .footer-text {
                color: #9ca3af;
                font-size: 12px;
            }
            .qr-section {
                text-align: center;
                margin-top: 20px;
                padding: 16px;
                background: #f9fafb;
                border-radius: 12px;
            }
            .qr-image {
                max-width: 150px;
                border-radius: 8px;
            }
            .print-btn {
                display: block;
                width: 100%;
                padding: 12px;
                background: #8b5cf6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 16px;
            }
            .print-btn:hover { background: #7c3aed; }
            @media print {
                body { background: white; padding: 0; }
                .invoice { box-shadow: none; max-width: 100%; }
                .print-btn, .no-print { display: none !important; }
            }
        </style>
    </head>
    <body>
        <div class="invoice">
            <div class="header">
                <div class="logo">D\'four Laundry</div>
                <div class="tagline">Smart Laundry Solution</div>
                <div class="invoice-number">NOTA #' . str_pad($transaction['id'], 4, '0', STR_PAD_LEFT) . '</div>
            </div>

            <div class="section">
                <div class="section-title">Pelanggan</div>
                <div class="customer-name">' . htmlspecialchars($customer['name']) . '</div>
                <div class="customer-phone">📱 ' . htmlspecialchars($customer['phone']) . '</div>
            </div>

            <div class="section">
                <div class="section-title">Detail Pesanan</div>
                <div class="detail-row">
                    <span class="detail-label">Layanan</span>
                    <span class="detail-value">' . htmlspecialchars($transaction['service_type']) . '</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah</span>
                    <span class="detail-value">' . ($transaction['weight'] > 0 ? $transaction['weight'] . ' kg' : $transaction['quantity'] . ' pcs') . '</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">' . date('d M Y, H:i', strtotime($transaction['created_at'])) . '</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">' . getStatusLabel($transaction['status']) . '</span>
                </div>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">Total Pembayaran</span>
                    <span class="total-amount">Rp ' . number_format($transaction['price'], 0, ',', '.') . '</span>
                </div>
                <div class="total-row" style="margin-top: 8px;">
                    <span class="total-label">Status Bayar</span>
                    <span class="status-badge status-' . $transaction['payment_status'] . '">' .
        ($transaction['payment_status'] == 'paid' ? '✓ Lunas' : ($transaction['payment_status'] == 'pending' ? '⏳ Menunggu' : '○ Belum Bayar')) .
        '</span>
                </div>
            </div>';

    // Show QRIS if unpaid
    if ($transaction['payment_status'] !== 'paid' && defined('QRIS_IMAGE_PATH')) {
        $qrisUrl = getBaseUrl() . '/' . QRIS_IMAGE_PATH;
        $html .= '
            <div class="qr-section no-print">
                <div class="section-title">Scan untuk Bayar</div>
                <img src="' . $qrisUrl . '" alt="QRIS" class="qr-image">
                <div style="font-size: 11px; color: #6b7280; margin-top: 8px;">' . QRIS_MERCHANT_NAME . '</div>
            </div>';
    }

    $html .= '
            <div class="footer">
                <div class="footer-text">Terima kasih telah menggunakan layanan kami! 🙏</div>
                <div class="footer-text" style="margin-top: 4px;">Hubungi: 0895-3372-52897</div>
            </div>

            <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak Nota</button>
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Get status label
 */
function getStatusLabel($status)
{
    $labels = [
        'pending' => 'Diterima',
        'washing' => 'Dicuci',
        'drying' => 'Dikeringkan',
        'ironing' => 'Disetrika',
        'done' => 'Selesai',
        'picked_up' => 'Diambil',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? ucfirst($status);
}

/**
 * Get base URL helper (if not already defined)
 */
if (!function_exists('getBaseUrl')) {
    function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname(dirname($_SERVER['SCRIPT_NAME']));
        return $protocol . '://' . $host . $path;
    }
}
