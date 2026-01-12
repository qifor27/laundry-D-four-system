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
        $mail->Timeout    = 5;  // 5 second timeout to prevent slow loading

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
 * Order Status Update Template (Mobile Responsive)
 */
function getOrderStatusEmailTemplate($transaction, $customer, $newStatus) {
    $statusLabel = getEmailStatusLabel($newStatus);
    $statusColor = $newStatus === 'done' ? '#22c55e' : '#9333ea';
    $formattedPrice = number_format($transaction['price'], 0, ',', '.');
    $currentYear = date('Y');
    
    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Update Status Pesanan - D'four Laundry</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;-webkit-font-smoothing:antialiased;">
    <!-- Wrapper Table -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#9333ea,#7c3aed);padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:26px;font-family:'Segoe UI',Arial,sans-serif;font-weight:bold;">D'four Laundry</h1>
                            <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;font-size:16px;font-family:'Segoe UI',Arial,sans-serif;">Update Status Pesanan</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px 20px;font-family:'Segoe UI',Arial,sans-serif;">
                            <p style="color:#374151;font-size:17px;margin:0 0 20px;line-height:1.5;">
                                Halo <strong>{$customer['name']}</strong>,
                            </p>
                            
                            <p style="color:#374151;font-size:17px;margin:0 0 25px;line-height:1.5;">
                                Status pesanan Anda telah diperbarui:
                            </p>
                            
                            <!-- Status Badge -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:25px 0;">
                                        <span style="display:inline-block;background-color:{$statusColor};color:#ffffff;padding:14px 28px;border-radius:50px;font-weight:bold;font-size:18px;font-family:'Segoe UI',Arial,sans-serif;">
                                            {$statusLabel}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Order Details Box -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f9fafb;border-radius:12px;margin:20px 0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <h3 style="color:#374151;margin:0 0 15px;font-size:13px;text-transform:uppercase;font-family:'Segoe UI',Arial,sans-serif;letter-spacing:0.5px;">Detail Pesanan</h3>
                                        
                                        <!-- Detail Row 1 -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-bottom:1px solid #e5e7eb;">
                                            <tr>
                                                <td style="padding:12px 0;color:#6b7280;font-size:15px;font-family:'Segoe UI',Arial,sans-serif;">No. Pesanan</td>
                                                <td style="padding:12px 0;color:#111827;font-weight:bold;text-align:right;font-size:15px;font-family:'Segoe UI',Arial,sans-serif;">#{$transaction['id']}</td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Detail Row 2 -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-bottom:1px solid #e5e7eb;">
                                            <tr>
                                                <td style="padding:12px 0;color:#6b7280;font-size:15px;font-family:'Segoe UI',Arial,sans-serif;">Layanan</td>
                                                <td style="padding:12px 0;color:#111827;text-align:right;font-size:15px;font-family:'Segoe UI',Arial,sans-serif;">{$transaction['service_type']}</td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Detail Row 3 -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding:12px 0;color:#6b7280;font-size:15px;font-family:'Segoe UI',Arial,sans-serif;">Total</td>
                                                <td style="padding:12px 0;color:#111827;font-weight:bold;text-align:right;font-size:17px;font-family:'Segoe UI',Arial,sans-serif;">Rp {$formattedPrice}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:25px 0;">
                                        <a href="#" style="display:inline-block;background-color:#9333ea;color:#ffffff;padding:16px 36px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px;font-family:'Segoe UI',Arial,sans-serif;">
                                            Cek Status Pesanan
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:25px 20px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="color:#6b7280;font-size:13px;margin:0;font-family:'Segoe UI',Arial,sans-serif;">
                                © {$currentYear} D'four Laundry. All rights reserved.
                            </p>
                            <p style="color:#9ca3af;font-size:12px;margin:10px 0 0;font-family:'Segoe UI',Arial,sans-serif;">
                                Email ini dikirim otomatis, mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

/**
 * Order Created Template (Mobile Responsive)
 */
function getOrderCreatedEmailTemplate($transaction, $customer) {
    $currentYear = date('Y');
    
    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pesanan Diterima - D'four Laundry</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;-webkit-font-smoothing:antialiased;">
    <!-- Wrapper Table -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:28px;font-family:'Segoe UI',Arial,sans-serif;font-weight:bold;">✓ Pesanan Diterima!</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px 20px;font-family:'Segoe UI',Arial,sans-serif;">
                            <p style="color:#374151;font-size:17px;margin:0 0 15px;line-height:1.5;">
                                Halo <strong>{$customer['name']}</strong>,
                            </p>
                            <p style="color:#374151;font-size:17px;margin:0 0 25px;line-height:1.5;">
                                Terima kasih telah menggunakan layanan D'four Laundry. Pesanan Anda telah kami terima.
                            </p>
                            
                            <!-- Order Number Box -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f0fdf4;border-left:4px solid #22c55e;margin:20px 0;">
                                <tr>
                                    <td style="padding:18px;">
                                        <strong style="color:#166534;font-size:18px;font-family:'Segoe UI',Arial,sans-serif;">No. Pesanan: #{$transaction['id']}</strong>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color:#6b7280;font-size:15px;margin:20px 0 0;line-height:1.6;">
                                Kami akan segera memproses pesanan Anda. Anda akan menerima notifikasi email saat status berubah.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:25px 20px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="color:#6b7280;font-size:13px;margin:0;font-family:'Segoe UI',Arial,sans-serif;">
                                © {$currentYear} D'four Laundry
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

/**
 * Ready for Pickup Template (Mobile Responsive)
 */
function getReadyForPickupEmailTemplate($transaction, $customer) {
    $formattedPrice = number_format($transaction['price'], 0, ',', '.');
    $currentYear = date('Y');
    
    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pesanan Siap Diambil - D'four Laundry</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;-webkit-font-smoothing:antialiased;">
    <!-- Wrapper Table -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:30px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:26px;font-family:'Segoe UI',Arial,sans-serif;font-weight:bold;">🎉 Pesanan Siap Diambil!</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px 20px;font-family:'Segoe UI',Arial,sans-serif;">
                            <p style="color:#374151;font-size:17px;margin:0 0 15px;line-height:1.5;">
                                Halo <strong>{$customer['name']}</strong>,
                            </p>
                            <p style="color:#374151;font-size:17px;margin:0 0 25px;line-height:1.5;">
                                Kabar baik! Pesanan laundry Anda sudah selesai dan siap untuk diambil.
                            </p>
                            
                            <!-- Order Number Box -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#fffbeb;border:2px dashed #f59e0b;border-radius:12px;margin:20px 0;">
                                <tr>
                                    <td align="center" style="padding:25px 20px;">
                                        <p style="color:#92400e;font-size:14px;margin:0 0 10px;font-family:'Segoe UI',Arial,sans-serif;">No. Pesanan</p>
                                        <p style="color:#78350f;font-size:32px;font-weight:bold;margin:0;font-family:'Segoe UI',Arial,sans-serif;">#{$transaction['id']}</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color:#374151;font-size:17px;margin:20px 0;line-height:1.5;">
                                <strong>Total Pembayaran:</strong> Rp {$formattedPrice}
                            </p>
                            
                            <!-- Pickup Address -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f9fafb;border-radius:8px;margin:20px 0;">
                                <tr>
                                    <td style="padding:18px;">
                                        <p style="color:#374151;font-size:15px;margin:0;line-height:1.6;font-family:'Segoe UI',Arial,sans-serif;">
                                            📍 <strong>Alamat Pickup:</strong><br>
                                            D'four Laundry<br>
                                            Jl. Contoh No. 123
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color:#6b7280;font-size:15px;margin:15px 0 0;line-height:1.5;">
                                ⏰ Jam Operasional: 08.00 - 21.00 WIB
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:25px 20px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="color:#6b7280;font-size:13px;margin:0;font-family:'Segoe UI',Arial,sans-serif;">
                                © {$currentYear} D'four Laundry - Bersih dan Wangi!
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}


?>