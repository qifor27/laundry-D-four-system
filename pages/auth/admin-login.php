<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/google-oauth.php';

if (isLoggedIn()) {
    $role = getUserData()['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
}

$pageTitle = "Admin Login - D'four Laundry";
$isGoogleConfigured = isGoogleOAuthConfigured();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">
    <?php if ($isGoogleConfigured): ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
</head>

<body class="font-outfit">

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- LEFT: Branding Panel -->
        <div class="auth-branding flex">
            <!-- Glass Bubbles -->
            <div class="glass-bubble glass-bubble-1"></div>
            <div class="glass-bubble glass-bubble-2"></div>
            <div class="glass-bubble glass-bubble-3"></div>
            <div class="glass-bubble glass-bubble-4"></div>
            <div class="glass-bubble glass-bubble-5"></div>

            <div class="relative z-10">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-12">
                    <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-14 h-14">
                    <span class="text-2xl font-bold text-white">D'four<span class="text-purple-300">Laundry</span></span>
                </div>

                <!-- Text -->
                <h1 class="text-4xl font-bold text-white mb-4">Panel Admin</h1>
                <p class="text-purple-200 text-lg mb-8">
                    Kelola transaksi, pelanggan, dan operasional laundry Anda dengan mudah.
                </p>

                <!-- Features -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Dashboard real-time</span>
                    </div>
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Kelola transaksi</span>
                    </div>
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Data pelanggan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Login Form -->
        <div class="auth-form">
            <div class="auth-form-inner">
                <!-- Mobile Logo -->
                <div class="md:hidden flex items-center gap-3 mb-8">
                    <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-12 h-12">
                    <span class="text-xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </div>

                <!-- Title -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Login Admin</h2>
                    <p class="text-gray-600">Masuk ke panel admin</p>
                </div>

                <!-- Alert Error -->
                <div id="alertError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="alertErrorText"></span>
                </div>

                <!-- Alert Success -->
                <div id="alertSuccess" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="alertSuccessText"></span>
                </div>

                <!-- Form -->
                <form id="loginForm" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" required
                            class="auth-input" placeholder="admin@email.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="auth-input pr-12" placeholder="Masukkan password">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="passwordEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700">Lupa password?</a>
                    </div>

                    <button type="submit" id="submitBtn" class="auth-btn">
                        <span id="submitText">Login</span>
                        <span id="submitLoading" class="hidden inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </form>

                <?php if ($isGoogleConfigured): ?>
                    <div class="flex items-center gap-4 my-6">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-gray-400 text-sm">atau</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <div class="flex justify-center">
                        <div id="g_id_onload" data-client_id="<?= htmlspecialchars(GOOGLE_CLIENT_ID) ?>" data-callback="handleGoogleLogin" data-auto_prompt="false"></div>
                        <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="signin_with" data-shape="rectangular" data-logo_alignment="center" data-width="300"></div>
                    </div>
                <?php endif; ?>

                <!-- Links -->
                <div class="text-center mt-8">
                    <a href="login.php" class="text-primary-600 hover:text-primary-700">← Login sebagai Pelanggan</a>
                </div>
            </div><!-- /.auth-form-inner -->
        </div>
    </div>

    <script>
        const API_URL = '<?= baseUrl("api/auth") ?>';

        function togglePassword() {
            const f = document.getElementById('password');
            f.type = f.type === 'password' ? 'text' : 'password';
        }

        function showError(msg) {
            document.getElementById('alertErrorText').textContent = msg;
            document.getElementById('alertError').classList.remove('hidden');
            document.getElementById('alertSuccess').classList.add('hidden');
        }

        function showSuccess(msg) {
            document.getElementById('alertSuccessText').textContent = msg;
            document.getElementById('alertSuccess').classList.remove('hidden');
            document.getElementById('alertError').classList.add('hidden');
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitText').classList.add('hidden');
            document.getElementById('submitLoading').classList.remove('hidden');

            try {
                const res = await fetch(`${API_URL}/login.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value.trim(),
                        password: document.getElementById('password').value,
                        admin_login: true
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showSuccess('Login berhasil!');
                    setTimeout(() => window.location.href = data.redirect || '<?= baseUrl("pages/dashboard.php") ?>', 1000);
                } else {
                    showError(data.error || 'Login gagal');
                }
            } catch (e) {
                showError('Terjadi kesalahan');
            } finally {
                btn.disabled = false;
                document.getElementById('submitText').classList.remove('hidden');
                document.getElementById('submitLoading').classList.add('hidden');
            }
        });

        function handleGoogleLogin(response) {
            fetch('<?= baseUrl("api/google-auth.php") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        credential: response.credential,
                        admin_login: true
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (!['superadmin', 'admin', 'cashier'].includes(data.user?.role)) {
                            showError('Akun Anda tidak memiliki akses admin');
                            return;
                        }
                        showSuccess('Berhasil!');
                        setTimeout(() => window.location.href = data.redirect || '<?= baseUrl("pages/dashboard.php") ?>', 1000);
                    } else showError(data.error || 'Login gagal');
                })
                .catch(() => showError('Terjadi kesalahan'));
        }
    </script>
</body>

</html>