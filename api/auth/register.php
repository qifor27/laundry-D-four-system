<?php
/**
 * Registration API
 * 
 * Endpoint: POST /api/auth/register.php
 * Body: { "name": "...", "email": "...", "password": "..." }
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/email.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/email.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!$input || empty($input['name']) || empty($input['email']) || empty($input['password'])) {
        throw new Exception('Semua field wajib diisi');
    }
    
    $name = trim($input['name']);
    $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    $password = $input['password'];
    
    if (!$email) {
        throw new Exception('Format email tidak valid');
    }
    
    if (strlen($password) < 8) {
        throw new Exception('Password minimal 8 karakter');
    }
    
    if (strlen($name) < 2) {
        throw new Exception('Nama minimal 2 karakter');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id, login_method FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        if ($existingUser['login_method'] === 'google') {
            throw new Exception('Email ini sudah terdaftar dengan Google. Silakan login dengan Google.');
        } else {
            throw new Exception('Email sudah terdaftar. Silakan login atau gunakan email lain.');
        }
    }
    
    // Generate verification token
    $verificationToken = generateToken();
    $verificationExpires = date('Y-m-d H:i:s', time() + EMAIL_VERIFICATION_EXPIRY);
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // validasi dan simpan phone
   $phone = isset($input['phone']) ? trim($input['phone']) : '';
            
            if (empty($phone)) {
                throw new Exception('Nomor HP wajib diisi');
            }
            if (!preg_match('/^08[0-9]{8,12}$/', $phone)){
                throw new Exception('Nomor HP tidak valid');
            }
            
            $stmt = $db->prepare("select id from users where phone = ?");
            $stmt->execute([$phone]);
            if ($stmt->fetch()){
                throw new Exception("Nomor HP sudah terdaftar");
            }
    // Insert new user
    $stmt = $db->prepare("
        INSERT INTO users (email, name, phone, password_hash, role, login_method, verification_token, verification_expires, is_active)
        VALUES (?, ?, ?, ?, 'user', 'email', ?, ?, 1)
    ");
    $stmt->execute([$email, $name, $phone, $passwordHash, $verificationToken, $verificationExpires]);
    
    $userId = $db->lastInsertId();

    // cek apakah phone sudah ada di customers
    $stmt = $db->prepare("select id from customers where phone = ?");
    $stmt->execute([$phone]);
    $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingCustomer){
        // link user customer existing
        $stmt = $db->prepare("UPDATE customers SET user_id = ?,
        registered_at = NOW() WHERE id = ?");
        $stmt->execute([$userId, $existingCustomer['id']]);
    } else {
        // Buat customer baru
        $stmt = $db->prepare("INSERT INTO customers (user_id,name,phone,
        registered_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId,$name,$phone]);
    }
    
    // Send verification email
    $emailSent = sendVerificationEmail($email, $name, $verificationToken);
    
    if ($emailSent) {
        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.',
            'email_sent' => true
        ]);
    } else {
        // If email sending fails, still register but auto-verify for development
        $stmt = $db->prepare("UPDATE users SET email_verified_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil! Akun Anda sudah aktif.',
            'email_sent' => false,
            'redirect' => getBaseUrl() . '/pages/auth/login.php'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
