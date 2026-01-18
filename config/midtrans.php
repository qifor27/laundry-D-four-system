<?php

/**
 * Midtrans Configuration
 * 
 * PANDUAN:
 * 1. Daftar di https://midtrans.com/ atau https://dashboard.sandbox.midtrans.com/
 * 2. Dapatkan Server Key dan Client Key dari Settings > Access Keys
 * 3. Copy .env.example ke .env dan isi dengan key Anda
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Set as environment variable if not already set
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// ================================================
// MIDTRANS ENVIRONMENT
// ================================================

// Set ke TRUE untuk production, FALSE untuk testing sandbox
define('MIDTRANS_IS_PRODUCTION', getenv('MIDTRANS_IS_PRODUCTION') === 'true');

// ================================================
// MIDTRANS API KEYS
// ================================================

// Server Key (digunakan di backend)
define('MIDTRANS_SERVER_KEY', getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxx');

// Client Key (digunakan di frontend)
define('MIDTRANS_CLIENT_KEY', getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxxx');

// Merchant ID
define('MIDTRANS_MERCHANT_ID', getenv('MIDTRANS_MERCHANT_ID') ?: 'G000000000');

// ================================================
// MIDTRANS URLS
// ================================================

// Snap URL based on environment
define(
    'MIDTRANS_SNAP_URL',
    MIDTRANS_IS_PRODUCTION
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js'
);

// API URL based on environment
define(
    'MIDTRANS_API_URL',
    MIDTRANS_IS_PRODUCTION
        ? 'https://api.midtrans.com/v2'
        : 'https://api.sandbox.midtrans.com/v2'
);

// ================================================
// MERCHANT INFO
// ================================================

define('MIDTRANS_MERCHANT_NAME', "D'four Laundry");

// ================================================
// HELPER FUNCTIONS
// ================================================

/**
 * Check if Midtrans is configured
 */
function isMidtransConfigured()
{
    return defined('MIDTRANS_SERVER_KEY')
        && MIDTRANS_SERVER_KEY !== 'SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxx'
        && !empty(MIDTRANS_SERVER_KEY);
}

/**
 * Get Midtrans Snap script URL
 */
function getMidtransSnapUrl()
{
    return MIDTRANS_SNAP_URL;
}

/**
 * Get Midtrans Client Key
 */
function getMidtransClientKey()
{
    return MIDTRANS_CLIENT_KEY;
}
