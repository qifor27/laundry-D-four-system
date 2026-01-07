<?php
/**
 * Google Authentication API
 * 
 * Receives JWT token from frontend Google Sign-In,
 * verifies with Google, creates/updates user, and establishes session.
 * 
 * Endpoint: POST /api/google-auth.php
 * Body: { "credential": "JWT_TOKEN_FROM_GOOGLE" }
 */

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../config/google-oauth.php';
require_once __DIR__ . '/../includes/auth.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['credential'])) {
        throw new Exception('Missing credential token');
    }
    
    $idToken = $input['credential'];
    
    // Check if Google OAuth is configured
    if (!isGoogleOAuthConfigured()) {
        throw new Exception('Google OAuth is not configured. Please update config/google-oauth.php');
    }
    
    // Verify token with Google
    $userInfo = verifyGoogleToken($idToken);
    
    if (!$userInfo) {
        throw new Exception('Invalid token or token verification failed');
    }
    
    // Check if email is verified
    if (!$userInfo['email_verified']) {
        throw new Exception('Email not verified with Google');
    }
    
    // Get database connection
    $db = Database::getInstance()->getConnection();
    
    // Check if user exists by google_id or email
    $stmt = $db->prepare("
        SELECT * FROM users 
        WHERE google_id = :google_id OR email = :email 
        LIMIT 1
    ");
    $stmt->execute([
        ':google_id' => $userInfo['google_id'],
        ':email' => $userInfo['email']
    ]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        // Check if user is active
        if (!$existingUser['is_active']) {
            throw new Exception('Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }
        
        // Update existing user
        $stmt = $db->prepare("
            UPDATE users SET 
                google_id = :google_id,
                name = :name,
                profile_picture = :picture,
                last_login = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':google_id' => $userInfo['google_id'],
            ':name' => $userInfo['name'],
            ':picture' => $userInfo['picture'],
            ':id' => $existingUser['id']
        ]);
        
        // Merge updated data
        $userData = array_merge($existingUser, [
            'google_id' => $userInfo['google_id'],
            'name' => $userInfo['name'],
            'profile_picture' => $userInfo['picture']
        ]);
        
    } else {
        // Create new user
        $stmt = $db->prepare("
            INSERT INTO users (google_id, email, name, profile_picture, role, last_login)
            VALUES (:google_id, :email, :name, :picture, :role, NOW())
        ");
        $stmt->execute([
            ':google_id' => $userInfo['google_id'],
            ':email' => $userInfo['email'],
            ':name' => $userInfo['name'],
            ':picture' => $userInfo['picture'],
            ':role' => DEFAULT_USER_ROLE
        ]);
        
        $userData = [
            'id' => $db->lastInsertId(),
            'google_id' => $userInfo['google_id'],
            'email' => $userInfo['email'],
            'name' => $userInfo['name'],
            'profile_picture' => $userInfo['picture'],
            'role' => DEFAULT_USER_ROLE
        ];
    }
    
    // Create authenticated session
    loginUser($userData);
    if (empty($userData['phone'])){
    // Redirect ke halaman complete profilr
    echo json_encode([
        'success' => true,
        'needs_phone' => true,
        'redirect' => getBaseUrl(). '/pages/auth/complete-profile.php'
    ]);
    exit;
}
    
    // Determine redirect URL based on role
    $role = $userData['role'] ?? 'user';
    $baseUrl = getBaseUrl();
    
    // Admin roles go to admin dashboard, users go to customer area
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        $redirectUrl = $baseUrl . '/pages/dashboard.php';
    } else {
        $redirectUrl = $baseUrl . '/pages/customer-dashboard.php';
    }
    
    // Check if there's a stored redirect URL (e.g., from trying to access a protected page)
    $storedRedirect = getRedirectAfterLogin();
    if ($storedRedirect && $storedRedirect !== $baseUrl . '/pages/dashboard.php') {
        $redirectUrl = $storedRedirect;
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil!',
        'user' => [
            'id' => $userData['id'],
            'name' => $userData['name'],
            'email' => $userData['email'] ?? $userInfo['email'],
            'picture' => $userData['profile_picture'],
            'role' => $userData['role']
        ],
        'redirect' => $redirectUrl
    ]);
    
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

?>
