<?php
/**
 * Authentication Helper Functions
 * 
 * Provides session management, login/logout helpers,
 * and protected page middleware for Google OAuth authentication.
 */

// Start session with secure configuration
if (session_status() == PHP_SESSION_NONE) {
    // Secure session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Use secure cookies if HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// Load configuration
require_once __DIR__ . '/../config/google-oauth.php';

/**
 * Check if user is logged in
 * 
 * @return bool True if user has active session
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        // Check if user_id exists in session
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $inactiveTime = time() - $_SESSION['last_activity'];
            if ($inactiveTime > SESSION_TIMEOUT) {
                // Session expired, logout user
                logoutUser();
                return false;
            }
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        return true;
    }
}

/**
 * Require login - redirect to login page if not authenticated
 * 
 * @param string $redirectUrl Optional custom redirect URL after login
 * @return void
 */
function requireLogin($redirectUrl = null) {
    if (!isLoggedIn()) {
        // Store intended destination
        if ($redirectUrl) {
            $_SESSION['redirect_after_login'] = $redirectUrl;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        // Redirect to login page
        $loginUrl = getBaseUrl() . '/pages/login.php';
        header("Location: $loginUrl");
        exit;
    }
}

/**
 * Require specific role(s) - redirect to dashboard if not authorized
 * 
 * @param string|array $roles Required role(s)
 * @return void
 */
function requireRole($roles) {
    requireLogin();
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    $userRole = $_SESSION['user_role'] ?? 'user';
    
    if (!in_array($userRole, $roles)) {
        // Set error message
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Anda tidak memiliki akses ke halaman tersebut.'
        ];
        
        // Redirect to dashboard
        $dashboardUrl = getBaseUrl() . '/pages/dashboard.php';
        header("Location: $dashboardUrl");
        exit;
    }
}

/**
 * Get current user data from session
 * 
 * @return array|null User data or null if not logged in
 */
function getUserData() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'google_id' => $_SESSION['user_google_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'picture' => $_SESSION['user_picture'] ?? null,
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

/**
 * Login user and create session
 * 
 * @param array $userData User data from database
 * @return void
 */
function loginUser($userData) {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Store user data in session
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['user_google_id'] = $userData['google_id'] ?? null;
    $_SESSION['user_email'] = $userData['email'];
    $_SESSION['user_name'] = $userData['name'];
    $_SESSION['user_picture'] = $userData['profile_picture'] ?? null;
    $_SESSION['user_role'] = $userData['role'] ?? 'user';
    
    // Set login time and last activity
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}

/**
 * Logout user and destroy session
 * 
 * @return void
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Get the redirect URL after login
 * 
 * @return string URL to redirect to
 */
function getRedirectAfterLogin() {
    $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
    
    // Clear stored redirect
    unset($_SESSION['redirect_after_login']);
    
    // Default to dashboard
    if (!$redirectUrl) {
        return getBaseUrl() . '/pages/dashboard.php';
    }
    
    return $redirectUrl;
}

/**
 * Check if user has specific role
 * 
 * @param string|array $roles Role(s) to check
 * @return bool True if user has the role
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    $userRole = $_SESSION['user_role'] ?? 'user';
    
    return in_array($userRole, $roles);
}

/**
 * Check if current user is admin or superadmin
 * 
 * @return bool True if user is admin or superadmin
 */
function isAdmin() {
    return hasRole(['admin', 'superadmin']);
}

/**
 * Check if current user is superadmin
 * 
 * @return bool True if user is superadmin
 */
function isSuperAdmin() {
    return hasRole(['superadmin']);
}

/**
 * Get base URL of the application
 * 
 * @return string Base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Get script path and find project root
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove /pages, /api, /includes from path to get project root
    $baseDir = preg_replace('#/(pages|api|includes)$#', '', $scriptPath);
    $baseDir = rtrim($baseDir, '/');
    
    return $protocol . '://' . $host . $baseDir;
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token Token to validate
 * @return bool True if valid
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field HTML
 * 
 * @return string HTML input field
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>
