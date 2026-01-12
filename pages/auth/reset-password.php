<?php

/**
 * Reset Password Page
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database_mysql.php';

$pageTitle = "Reset Password - D'four Laundry";
$token = $_GET['token'] ?? '';
$validToken = false;
$email = '';

if ($token) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $validToken = true;
        $email = $result['email'];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

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
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
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
            <div class="text-center mb-8">
                <a href="<?= baseUrl() ?>" class="inline-flex items-center space-x-3">
                    <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-12 h-12">
                    <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
            </div>

            <?php if (!$validToken): ?>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Link Tidak Valid</h1>
                    <p class="text-gray-600 mb-6">Link reset password tidak valid atau sudah kadaluarsa.</p>
                    <a href="<?= baseUrl('pages/auth/forgot-password.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                        Minta link baru →
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-gray-600">Buat password baru untuk <strong><?= htmlspecialchars($email) ?></strong></p>
                </div>

                <div id="alertError" class="alert alert-error hidden"></div>
                <div id="alertSuccess" class="alert alert-success hidden"></div>

                <form id="resetForm" class="space-y-5">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" id="password" required class="form-input" placeholder="Minimal 8 karakter" minlength="8">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirm" id="password_confirm" required class="form-input" placeholder="Ulangi password">
                    </div>

                    <button type="submit" id="submitBtn" class="btn-primary">
                        <span id="submitText">Simpan Password Baru</span>
                        <span id="submitLoading" class="hidden">Menyimpan...</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if ($validToken): ?>
            document.getElementById('resetForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const password = document.getElementById('password').value;
                const confirm = document.getElementById('password_confirm').value;

                if (password !== confirm) {
                    document.getElementById('alertError').textContent = 'Password tidak cocok';
                    document.getElementById('alertError').classList.remove('hidden');
                    return;
                }

                const btn = document.getElementById('submitBtn');
                btn.disabled = true;

                try {
                    const res = await fetch('<?= baseUrl('api/auth/reset-password.php') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            token: '<?= $token ?>',
                            password
                        })
                    });
                    const data = await res.json();

                    if (data.success) {
                        document.getElementById('alertSuccess').textContent = 'Password berhasil direset! Mengalihkan ke login...';
                        document.getElementById('alertSuccess').classList.remove('hidden');
                        document.getElementById('alertError').classList.add('hidden');
                        setTimeout(() => window.location.href = '<?= baseUrl('pages/auth/login.php') ?>', 2000);
                    } else {
                        document.getElementById('alertError').textContent = data.error || 'Gagal reset password';
                        document.getElementById('alertError').classList.remove('hidden');
                    }
                } catch (e) {
                    document.getElementById('alertError').textContent = 'Terjadi kesalahan';
                    document.getElementById('alertError').classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>