<?php

/**
 * Email Helper Functions (PHPMailer)
 * 
 * Fungsi-fungsi untuk mengirim email menggunakan PHPMailer + Gmail SMTP.
 */

// load composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// load email config
$emailConfigPath = __DIR__ . '/../config/email.php';
if (file_exists($emailConfigPath)) {
    require_once $emailConfigPath;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email via PHPMailer + Gmail SMTP
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param array $options Optional settings
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $options = []) {
    // check if email is enabled
    if (!defined('EMAIL_ENABLED') || !EMAIL_ENABLED) {
        return ['success' => false, 'message' => 'Email notifications are disabled'];
    }

    // Check configuration
    if (!isEmailConfigured()) {
        return ['success' => false, 'message' => 'Email not configured properly'];
    }

    $mail = new PHPMailer(true);

    try {
        // server setting
        if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // recipients
        $mail->setFrom(SMTP_USERNAME, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(EMAIL_REPLY_TO ?? SMTP_USERNAME, EMAIL_FROM_NAME);

        // content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // plain text version

        $mail->send();

        logEmail($to, $subject, 'sent');
        return ['success' => true, 'message' => 'Email berhasil terkirim!'];

        } catch (Exception $e) {
        $errorMsg = $mail->ErrorInfo;
        logEmail($to, $subject, 'failed: ' . $errorMsg);
        return ['success' => false, 'message' => 'Gagal kirim email: ' . $errorMsg];
    }
}

/**
 * Send Order Status Email
 */
function sendOrderStatusEmail($transaction, $customer, $newStatus) {
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Update Status Pesanan #" . $transaction['id'] . " - D'four Laundry";
    $body = getOrderStatusEmailTemplate($transaction, $customer, $newStatus);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Send Order Created Email
 */
function sendOrderCreatedEmail($transaction, $customer) {
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Pesanan Baru #" . $transaction['id'] . " - D'four Laundry";
    $body = getOrderCreatedEmailTemplate($transaction, $customer);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Send Ready for Pickup Email
 */
function sendReadyForPickupEmail($transaction, $customer) {
    if (empty($customer['email'])) {
        return ['success' => false, 'message' => 'Customer has no email'];
    }
    
    $subject = "Pesanan #" . $transaction['id'] . " Siap Diambil! - D'four Laundry";
    $body = getReadyForPickupEmailTemplate($transaction, $customer);
    
    return sendEmail($customer['email'], $subject, $body);
}

/**
 * Log email activity
 */
function logEmail($to, $subject, $status) {
    $logFile = __DIR__ . '/../logs/email.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject | Status: $status\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Get status label in Indonesian
 */
function getEmailStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu Proses',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai - Siap Diambil',
        'picked_up' => 'Sudah Diambil',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? $status;
}


// ================================================
// EMAIL TEMPLATES
// ================================================


/**
 * Order Status Update Template
 */
function getOrderStatusEmailTemplate($transaction, $customer, $newStatus) {
    $statusLabel = getEmailStatusLabel($newStatus);
    $statusColor = $newStatus === 'done' ? '#22c55e' : '#9333ea';
    $formattedPrice = number_format($transaction['price'], 0, ',', '.');
    $currentYear = date('Y');
    
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#9333ea,#7c3aed);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">D'four Laundry</h1>
                <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;">Update Status Pesanan</p>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;margin:0 0 20px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                
                <p style="color:#374151;font-size:16px;margin:0 0 20px;">
                    Status pesanan Anda telah diperbarui:
                </p>
                
                <!-- Status Badge -->
                <div style="text-align:center;margin:30px 0;">
                    <span style="background:{$statusColor};color:#fff;padding:12px 24px;border-radius:50px;font-weight:bold;font-size:18px;">
                        {$statusLabel}
                    </span>
                </div>
                
                <!-- Order Details -->
                <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:20px 0;">
                    <h3 style="color:#374151;margin:0 0 15px;font-size:14px;text-transform:uppercase;">Detail Pesanan</h3>
                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">No. Pesanan</td>
                            <td style="padding:8px 0;color:#111827;font-weight:bold;text-align:right;">#{$transaction['id']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">Layanan</td>
                            <td style="padding:8px 0;color:#111827;text-align:right;">{$transaction['service_type']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;">Total</td>
                                                        <td style="padding:8px 0;color:#111827;font-weight:bold;text-align:right;">Rp {$formattedPrice}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- CTA Button -->
                <div style="text-align:center;margin:30px 0;">
                    <a href="#" style="display:inline-block;background:#9333ea;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;">
                        Cek Status Pesanan
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
                <p style="color:#6b7280;font-size:12px;margin:0;">
                                        © {$currentYear} D'four Laundry. All rights reserved.
                </p>
                <p style="color:#9ca3af;font-size:11px;margin:10px 0 0;">
                    Email ini dikirim otomatis, mohon tidak membalas email ini.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Order Created Template
 */
function getOrderCreatedEmailTemplate($transaction, $customer) {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">✓ Pesanan Diterima!</h1>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                <p style="color:#374151;font-size:16px;">
                    Terima kasih telah menggunakan layanan D'four Laundry. Pesanan Anda telah kami terima.
                </p>
                
                <div style="background:#f0fdf4;border-left:4px solid #22c55e;padding:15px;margin:20px 0;">
                    <strong style="color:#166534;">No. Pesanan: #{$transaction['id']}</strong>
                </div>
                
                <p style="color:#6b7280;font-size:14px;">
                    Kami akan segera memproses pesanan Anda. Anda akan menerima notifikasi email saat status berubah.
                </p>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;">
                <p style="color:#6b7280;font-size:12px;margin:0;">D'four Laundry</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Ready for Pickup Template
 */
function getReadyForPickupEmailTemplate($transaction, $customer) {
    $formattedPrice = number_format($transaction['price'], 0, ',', '.');
    
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:30px;text-align:center;">
                <h1 style="color:#fff;margin:0;font-size:24px;">🎉 Pesanan Siap Diambil!</h1>
            </div>
            
            <!-- Content -->
            <div style="padding:30px;">
                <p style="color:#374151;font-size:16px;">
                    Halo <strong>{$customer['name']}</strong>,
                </p>
                <p style="color:#374151;font-size:16px;">
                    Kabar baik! Pesanan laundry Anda sudah selesai dan siap untuk diambil.
                </p>
                
                <div style="background:#fffbeb;border:2px dashed #f59e0b;border-radius:12px;padding:20px;margin:20px 0;text-align:center;">
                    <p style="color:#92400e;font-size:14px;margin:0 0 10px;">No. Pesanan</p>
                    <p style="color:#78350f;font-size:28px;font-weight:bold;margin:0;">#{$transaction['id']}</p>
                </div>
                
                <p style="color:#374151;font-size:16px;">
                    <strong>Total Pembayaran:</strong> Rp {$formattedPrice}
                </p>
                
                <div style="background:#f9fafb;border-radius:8px;padding:15px;margin:20px 0;">
                    <p style="color:#374151;font-size:14px;margin:0;">
                        📍 <strong>Alamat Pickup:</strong><br>
                        D'four Laundry<br>
                        Jl. Contoh No. 123
                    </p>
                </div>
                
                <p style="color:#6b7280;font-size:14px;">
                    Jam Operasional: 08.00 - 21.00 WIB
                </p>
            </div>
            
            <!-- Footer -->
            <div style="background:#f9fafb;padding:20px;text-align:center;">
                <p style="color:#6b7280;font-size:12px;margin:0;">D'four Laundry - Bersih dan Wangi!</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}


?>