<?php
/**
 * Login API
 * 
 * Endpoint: POST /api/auth/login.php
 * Body: { "email": "...", "password": "..." }
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../includes/auth.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['email']) || empty($input['password'])) {
        throw new Exception('Email dan password wajib diisi');
    }
    
    $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    $password = $input['password'];
    $isAdminLogin = isset($input['admin_login']) && $input['admin_login'] === true;
    
    if (!$email) {
        throw new Exception('Format email tidak valid');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Find user by email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('Email atau password salah');
    }
    
    // Check login method
    if ($user['login_method'] === 'google') {
        throw new Exception('Akun ini terdaftar dengan Google. Silakan login dengan Google.');
    }
    
    // Check password
    if (!$user['password_hash'] || !password_verify($password, $user['password_hash'])) {
        throw new Exception('Email atau password salah');
    }
    
    // Check if active
    if (!$user['is_active']) {
        throw new Exception('Akun Anda telah dinonaktifkan. Hubungi administrator.');
    }
    
    // Check email verification (optional - can skip for development)
    // if (!$user['email_verified_at']) {
    //     throw new Exception('Email belum diverifikasi. Silakan cek email Anda.');
    // }
    
    // For admin login, check role
    if ($isAdminLogin) {
        if (!in_array($user['role'], ['superadmin', 'admin', 'cashier'])) {
            throw new Exception('Anda tidak memiliki akses ke panel admin');
        }
    }
    
    // Update last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Create session
    $userData = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'profile_picture' => $user['profile_picture'],
        'role' => $user['role']
    ];
    loginUser($userData);
    
    // Determine redirect URL
    $baseUrl = getBaseUrl();
    if (in_array($user['role'], ['superadmin', 'admin', 'cashier'])) {
        $redirectUrl = $baseUrl . '/pages/dashboard.php';
    } else {
        $redirectUrl = $baseUrl . '/pages/customer-dashboard.php';
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil!',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
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
