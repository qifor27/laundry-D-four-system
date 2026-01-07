<?php
/**
 * Helper Functions
 * Fungsi-fungsi utility yang sering digunakan
 */

// Start session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Format angka ke format Rupiah
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 */
function formatDate($date, $includeTime = true) {
    if (empty($date)) return '-';
    
    $timestamp = strtotime($date);
    
    if ($includeTime) {
        return date('d M Y, H:i', $timestamp);
    } else {
        return date('d M Y', $timestamp);
    }
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate phone number (Indonesia format)
 */
function validatePhone($phone) {
    // Remove non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it starts with 08 or 628 or +628
    if (preg_match('/^(08|628|\+628)/', $phone)) {
        return $phone;
    }
    
    return false;
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'washing' => 'bg-blue-100 text-blue-800',
        'drying' => 'bg-cyan-100 text-cyan-800',
        'ironing' => 'bg-purple-100 text-purple-800',
        'done' => 'bg-green-100 text-green-800',
        'picked_up' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800'
    ];
    
    return $badges[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get status label (Indonesia)
 */
function getStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu',
        'washing' => 'Sedang Dicuci',
        'drying' => 'Sedang Dikeringkan',
        'ironing' => 'Sedang Disetrika',
        'done' => 'Selesai',
        'picked_up' => 'Sudah Diambil',
        'cancelled' => 'Dibatalkan'
    ];
    
    return $labels[$status] ?? ucfirst($status);
}

/**
 * Generate transaction code
 */
function generateTransactionCode($customerId) {
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    return "TRX-{$date}-{$customerId}-{$random}";
}

/**
 * Calculate estimated completion date
 */
function estimateCompletionDate($serviceType, $createdDate = null) {
    // Default completion time in hours
    $completionHours = 24; // 1 hari untuk cuci kering
    
    // Adjust based on service type
    if (stripos($serviceType, 'setrika') !== false) {
        $completionHours = 48; // 2 hari untuk cuci setrika
    } elseif (stripos($serviceType, 'bed cover') !== false || stripos($serviceType, 'selimut') !== false) {
        $completionHours = 72; // 3 hari untuk item besar
    }
    
    $startDate = $createdDate ? strtotime($createdDate) : time();
    $completionDate = $startDate + ($completionHours * 3600);
    
    return date('Y-m-d H:i:s', $completionDate);
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Check if user is logged in (optional untuk auth)
 * Note: This is a simple version, auth.php has a more complete implementation
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

/**
 * Redirect to a page
 */
function redirect($path) {
    $baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    header("Location: {$baseUrl}/{$path}");
    exit;
}

/**
 * Get base URL
 */
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['PHP_SELF']);
    
    // Remove /pages/*, /api/*, /includes/* from path to get project root
    // This handles nested folders like /pages/auth
    $baseDir = preg_replace('#/(pages|api|includes)(/.*)?$#', '', $scriptPath);
    $baseDir = rtrim($baseDir, '/');
    
    return $protocol . '://' . $host . $baseDir . '/' . ltrim($path, '/');
}

/**
 * JSON response untuk API
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Validate required fields
 */
function validateRequired($fields, $data) {
    $errors = [];
    
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = "Field {$field} wajib diisi";
        }
    }
    
    return $errors;
}
?>
