<?php
/**
 * Email Helper Functions
 * 
 * Functions for sending verification and password reset emails.
 * Uses PHP's native mail() function with fallback to SMTP.
 */

require_once __DIR__ . '/../config/email.php';

/**
 * Generate a secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Send an email
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $htmlBody HTML body
 * @param string $textBody Plain text body (optional)
 * @return bool Success status
 */
function sendEmail($to, $subject, $htmlBody, $textBody = '') {
    // If email not configured, log and return false (so auto-verify fallback will trigger)
    if (!isEmailConfigured()) {
        error_log("[EMAIL NOT CONFIGURED] Would send to: $to | Subject: $subject");
        return false; // Return false to trigger auto-verify fallback
    }
    
    // Use PHP mail() for simple setup
    $headers = [
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=utf-8',
        'From' => EMAIL_FROM_NAME . ' <' . EMAIL_FROM . '>',
        'Reply-To' => EMAIL_FROM,
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    
    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= "$key: $value\r\n";
    }
    
    return mail($to, $subject, $htmlBody, $headerString);
}

/**
 * Get email template wrapper
 */
function getEmailTemplate($title, $content, $buttonText = '', $buttonUrl = '') {
    $buttonHtml = '';
    if ($buttonText && $buttonUrl) {
        $buttonHtml = "
            <tr>
                <td style='padding: 30px 0;'>
                    <a href='$buttonUrl' style='display: inline-block; background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;'>
                        $buttonText
                    </a>
                </td>
            </tr>
        ";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6;'>
        <table role='presentation' style='width: 100%; border-collapse: collapse;'>
            <tr>
                <td style='padding: 40px 20px;'>
                    <table role='presentation' style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                        <!-- Header -->
                        <tr>
                            <td style='background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); padding: 30px; text-align: center;'>
                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>D'four Laundry</h1>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tr>
                            <td style='padding: 40px 30px; text-align: center;'>
                                <h2 style='color: #1f2937; margin: 0 0 20px; font-size: 22px;'>$title</h2>
                                <p style='color: #6b7280; font-size: 16px; line-height: 1.6; margin: 0 0 20px;'>
                                    $content
                                </p>
                                $buttonHtml
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style='background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;'>
                                <p style='color: #9ca3af; font-size: 12px; margin: 0;'>
                                    &copy; " . date('Y') . " D'four Laundry. All rights reserved.
                                </p>
                                <p style='color: #9ca3af; font-size: 12px; margin: 10px 0 0;'>
                                    Jika Anda tidak melakukan permintaan ini, abaikan email ini.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}

/**
 * Send verification email
 */
function sendVerificationEmail($email, $name, $token) {
    $verifyUrl = VERIFY_EMAIL_URL . '?token=' . urlencode($token);
    
    $content = "Halo <strong>$name</strong>,<br><br>
    Terima kasih telah mendaftar di D'four Laundry! 
    Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.";
    
    $html = getEmailTemplate(
        'Verifikasi Email Anda',
        $content,
        'Verifikasi Email',
        $verifyUrl
    );
    
    return sendEmail($email, "Verifikasi Email - D'four Laundry", $html);
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $name, $token) {
    $resetUrl = RESET_PASSWORD_URL . '?token=' . urlencode($token);
    
    $content = "Halo <strong>$name</strong>,<br><br>
    Kami menerima permintaan untuk mereset password akun Anda. 
    Klik tombol di bawah untuk membuat password baru.<br><br>
    <span style='color: #ef4444; font-size: 14px;'>Link ini akan kadaluarsa dalam 1 jam.</span>";
    
    $html = getEmailTemplate(
        'Reset Password',
        $content,
        'Reset Password',
        $resetUrl
    );
    
    return sendEmail($email, "Reset Password - D'four Laundry", $html);
}

/**
 * Send welcome email after registration
 */
function sendWelcomeEmail($email, $name) {
    $content = "Halo <strong>$name</strong>,<br><br>
    Selamat datang di D'four Laundry! 
    Akun Anda telah berhasil dibuat dan diverifikasi.<br><br>
    Sekarang Anda dapat menikmati kemudahan layanan laundry kami.";
    
    $html = getEmailTemplate(
        'Selamat Datang!',
        $content,
        'Kunjungi Dashboard',
        APP_URL . '/pages/auth/login.php'
    );
    
    return sendEmail($email, "Selamat Datang di D'four Laundry", $html);
}
?>
