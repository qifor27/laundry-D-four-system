<?php
/**
 * Reset Password API
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database_mysql.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['token']) || empty($input['password'])) {
        throw new Exception('Token dan password wajib diisi');
    }
    
    $token = $input['token'];
    $password = $input['password'];
    
    if (strlen($password) < 8) {
        throw new Exception('Password minimal 8 karakter');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Validate token
    $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reset) {
        throw new Exception('Token tidak valid atau sudah kadaluarsa');
    }
    
    // Update password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->execute([$passwordHash, $reset['email']]);
    
    // Mark token as used
    $stmt = $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE token = ?");
    $stmt->execute([$token]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Password berhasil direset'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
