<?php
/**
 * Logout API
 * 
 * Destroys user session and clears authentication.
 * Supports both AJAX and regular requests.
 * 
 * Endpoint: POST /api/logout.php
 */

require_once __DIR__ . '/../includes/auth.php';

// Check if JSON response is requested
$wantsJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Logout user
logoutUser();

if ($wantsJson || $isAjax) {
    // JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logout berhasil',
        'redirect' => getBaseUrl() . '/pages/auth/login.php'
    ]);
} else {
    // Redirect for regular requests
    header('Location: ' . getBaseUrl() . '/pages/auth/login.php');
}
exit;
?>
