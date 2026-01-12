<?php

/**
 * Email Verification Page
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database_mysql.php';

$pageTitle = "Verifikasi Email - D'four Laundry";
$token = $_GET['token'] ?? '';
$success = false;
$message = '';

if ($token) {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT id, name FROM users WHERE verification_token = ? AND verification_expires > NOW() AND email_verified_at IS NULL");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $db->prepare("UPDATE users SET email_verified_at = NOW(), verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $success = true;
        $message = "Email berhasil diverifikasi! Sekarang Anda dapat login.";
    } else {
        $message = "Link verifikasi tidak valid atau sudah kadaluarsa.";
    }
} else {
    $message = "Token verifikasi tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit">
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
    </div>

    <div class="auth-container relative z-10">
        <div class="auth-card">
            <a href="<?= baseUrl() ?>" class="inline-flex items-center space-x-3 mb-8">
                <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-12 h-12">
                <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
            </a>

            <?php if ($success): ?>
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Berhasil!</h1>
                <p class="text-gray-600 mb-6"><?= htmlspecialchars($message) ?></p>
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="inline-block bg-gradient-to-r from-primary-500 to-primary-600 text-white px-8 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                    Login Sekarang
                </a>
            <?php else: ?>
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Gagal</h1>
                <p class="text-gray-600 mb-6"><?= htmlspecialchars($message) ?></p>
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                    ← Kembali ke Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>