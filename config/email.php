<?php
/**
 * Email Configuration
 * 
 * Configure SMTP settings for sending emails.
 * For development, you can use Mailtrap.io or Gmail SMTP.
 * 
 * DO NOT COMMIT THIS FILE TO GIT WITH REAL CREDENTIALS!
 */

// ============================================
// SMTP CONFIGURATION
// ============================================

// For Gmail SMTP (enable "Less secure apps" or use App Password)
// For Mailtrap: Get credentials from mailtrap.io dashboard

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'

// ============================================
// EMAIL SETTINGS
// ============================================

define('EMAIL_FROM_ADDRESS', 'noreply@dfour-laundry.com');
define('EMAIL_FROM_NAME', "D'four Laundry");

// Verification email settings
define('EMAIL_VERIFICATION_EXPIRY', 24 * 60 * 60); // 24 hours in seconds

// Password reset settings
define('PASSWORD_RESET_EXPIRY', 60 * 60); // 1 hour in seconds

// ============================================
// APPLICATION URLs
// ============================================

// Base URL of the application (no trailing slash)
define('APP_URL', 'http://localhost/laundry-D-four');

// Auth page URLs
define('VERIFY_EMAIL_URL', APP_URL . '/pages/auth/verify-email.php');
define('RESET_PASSWORD_URL', APP_URL . '/pages/auth/reset-password.php');

/**
 * Check if email is properly configured
 */
function isEmailConfigured() {
    return SMTP_USERNAME !== 'your-email@gmail.com' 
        && SMTP_PASSWORD !== 'your-app-password';
}
?>
