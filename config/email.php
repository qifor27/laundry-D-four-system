<?php
/**
 * Email Configuration
 * 
 * Copy file ini ke email.php dan isi dengan kredensial SMTP Anda.
 * File email.php sudah di-ignore oleh git untuk keamanan.
 * 
 * PENTING: Jangan commit file email.php ke repository!
 */

// ================================================
// SMTP CONFIGURATION
// ================================================

// SMTP Host (Gmail: smtp.gmail.com, Mailtrap: smtp.mailtrap.io)
define('SMTP_HOST', 'smtp.gmail.com');

// SMTP Port (Gmail: 587, SSL: 465)
define('SMTP_PORT', 587);

// SMTP Username (email address)
define('SMTP_USERNAME', 'reyhandwisaputra1234@gmail.com');

// SMTP Password (App Password for Gmail)
define('SMTP_PASSWORD', 'txmzvzforcvxsjgj');

// SMTP Encryption (tls atau ssl)
define('SMTP_ENCRYPTION', 'tls');

// ================================================
// SENDER CONFIGURATION
// ================================================

// From Email (email pengirim)
define('EMAIL_FROM', 'noreply@dfourlaundry.com');

// From Name (nama pengirim)
define('EMAIL_FROM_NAME', "D'four Laundry");

// Reply-To Email (email untuk reply)
define('EMAIL_REPLY_TO', 'cs@dfourlaundry.com');

// ================================================
// FEATURE FLAGS
// ================================================

// Aktifkan email notifications (set false untuk disable sementara)
define('EMAIL_ENABLED', false);

// Debug mode (tampilkan error detail)
define('EMAIL_DEBUG', false);

// ================================================
// HELPER FUNCTION
// ================================================

/**
 * Check if email is configured properly
 */
function isEmailConfigured() {
    if (!defined('SMTP_HOST') || SMTP_HOST === 'smtp.gmail.com') {
        // Cek apakah password sudah diganti dari default
        if (!defined('SMTP_PASSWORD') || SMTP_PASSWORD === 'your-app-password') {
            return false;
        }
    }
    return defined('EMAIL_ENABLED') && EMAIL_ENABLED === true;
}
?>