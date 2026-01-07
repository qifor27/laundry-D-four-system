<?php
// set response header sebagai JSON
header('Content-Type: application/json');

// hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

require_once '/../../config/database_mysql.php';
require_once '/../../includes/auth.php';

try {
    // step 1:check apakah user sudah login
    if (!isLoggedIn()) {
        http_response_code(401);
        throw new Exception('Anda harus login terlebih dahulu');
    }

    // ambil data user dari session
    $userData = getUserData();
    $userId = $userData['id'];

    // step 2: ambil dan validasi input JSON

    // Baca raw body request (JSON)
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    // Cek validasi input
    if (!$input || empty($input['phone'])) {
        throw new Exception('Nomor HP wajib diisi');
    }

    // bersihkan input dari spasi
    $phone = trim($input['phone']);

    // step 3: validasi format nomor HP indonesia

    // format nomor dimulai dengan 08, diikuti 8-12 digit angka
    if(!preg_match('/^08[0-9]{8,12}$/', $phone)){
        throw new Exception('Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx');
    }
    
    //step 4: koneksi database
    // singleton pattern - ambil instance database
    $db = Database::getInstance()->getConnection();

    //step 5: cek apakah nomor HP sudah digunakan user lain

    // prepared statement untuk mencegah SQL injection
    $stmt = $db->prepare("
        SELECT id FROM users
        WHERE phone = ? AND id != ?
    ");
    $stmt->execute([$phone, $userId]);

    if ($stmt->fetch()) {
        //nomor sudah digunakan user lain
        throw new Exception('Nomor HP sudah terdaftar oleh akun lain');
    }

    // step 6: update nomor HP di table users

    $stmt = $dp->prepare("
        UPDATE users
        SET phone = ?, updated_at = NOW()
        WHERE id = ?
    ");

    // update session dengan phone baru 
    $_SESSION['user_phone'] = $phone;

    // step 7: link dengan customer atau buat baru

    // sek apakah ada customer dengan phone ini
    $stmt = $db->prepare("
        SELECT id, user_id FROM customers
        WHERE phone = ?
    ");
    $stmt->execute([$phone]);
    $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingCustomer) {
        // customer dengan phone ini sudah ada
        // update untuk link dengan user account
        $stmt = $db->prepare("
            UPDATE customers
            SET user_id = ?, registered_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$userId, $existingCustomer['id']]);

        $message = 'profil berhasil dilengkapi! Data transaksi Anda sebelumnya sudah terhubung.';
    }else {
        // belum ada customer dengan phome ini
        //buat customer baru 
        $stmt = $db->prepare("
            INSERT INTO customers (user_id, name, phone, registered_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $userData['name'], $phone]);
        
        $message = 'profil berhasil dilengkapi!';
    }

    // step 8: return success response

    // tentukan redirect berdasarkan role
    $baseUrl = getBaseUrl();
    $role = $userData['role'] ?? 'user';

    if (in_array($role, ['superadmin','admin', 'cashier'])) {
        $redirectUrl = $baseUrl . '/pages/dashboard.php';
    }else {
        $redirectUrl = $baseUrl . '/pages/customer-dashboard.php';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'redirect' => $redirectUrl
    ]);

}catch (Exception $e) {
    // handle semua error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

?>