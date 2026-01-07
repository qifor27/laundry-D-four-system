<?php
/**
 * Forgot Password API
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';
require_once __DIR__ . '/../../config/email.php';
require_once __DIR__ . '/../../includes/email.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['email'])) {
        throw new Exception('Email wajib diisi');
    }
    
    $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Format email tidak valid');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id, name, login_method FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Don't reveal that email doesn't exist
        echo json_encode(['success' => true, 'message' => 'Jika email terdaftar, link reset akan dikirim']);
        exit;
    }
    
    if ($user['login_method'] === 'google') {
        throw new Exception('Akun ini menggunakan login Google. Silakan login dengan Google.');
    }
    
    // Generate token
    $token = generateToken();
    $expiresAt = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);
    
    // Delete existing tokens for this email
    $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);
    
    // Insert new token
    $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $token, $expiresAt]);
    
    // Send email
    $emailSent = sendPasswordResetEmail($email, $user['name'], $token);
    
    echo json_encode([
        'success' => true,
        'message' => 'Link reset password telah dikirim ke email Anda',
        'email_sent' => $emailSent
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
