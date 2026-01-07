<?php
/**
 * Admin Login Page
 * Separate login page for admin/cashier/superadmin
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/google-oauth.php';

// If already logged in, redirect
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
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?= baseUrl() ?>/assets/css/style.css" rel="stylesheet">
    
    <?php if ($isGoogleConfigured): ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
    
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
        }
        
        .auth-branding {
            flex: 1;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-branding::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .auth-form {
            flex: 1;
            background: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 500px;
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
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
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
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
        }
        
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert.show {
            display: flex;
            align-items: flex-start;
            gap: 12px;
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
        
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            .auth-branding {
                padding: 40px 24px;
            }
            .auth-form {
                padding: 40px 24px;
                max-width: none;
            }
        }
    </style>
</head>
<body class="font-outfit">
    <div class="auth-container">
        <!-- Branding Side -->
        <div class="auth-branding hidden md:flex">
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-12">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-2xl">D</span>
                    </div>
                    <span class="text-2xl font-bold text-white">D'four<span class="text-purple-300">Laundry</span></span>
                </div>
                
                <h1 class="text-4xl font-bold text-white mb-4">Panel Admin</h1>
                <p class="text-purple-200 text-lg mb-8">
                    Kelola transaksi, pelanggan, dan operasional laundry Anda dengan mudah.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Dashboard real-time</span>
                    </div>
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kelola transaksi</span>
                    </div>
                    <div class="flex items-center gap-3 text-purple-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Data pelanggan</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Login Form Side -->
        <div class="auth-form">
            <!-- Mobile Logo -->
            <div class="md:hidden flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-xl">D</span>
                </div>
                <span class="text-xl font-bold text-gray-900">D'four<span class="text-purple-600">Laundry</span></span>
            </div>
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Login Admin</h2>
                <p class="text-gray-600">Masuk ke panel admin</p>
            </div>
            
            <!-- Alerts -->
            <div id="alertError" class="alert alert-error">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="alertErrorText"></span>
            </div>
            
            <div id="alertSuccess" class="alert alert-success">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="alertSuccessText"></span>
            </div>
            
            <form id="loginForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" required class="form-input" placeholder="admin@email.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="form-input pr-12" placeholder="Masukkan password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="passwordEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="text-right">
                    <a href="<?= baseUrl() ?>/pages/auth/forgot-password.php" class="text-sm text-purple-600 hover:text-purple-700">
                        Lupa password?
                    </a>
                </div>
                
                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Login</span>
                    <span id="submitLoading" class="hidden">Memproses...</span>
                </button>
            </form>
            
            <?php if ($isGoogleConfigured): ?>
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-gray-400 text-sm">atau</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>
            
            <div class="flex justify-center">
                <div id="g_id_onload"
                     data-client_id="<?= htmlspecialchars(GOOGLE_CLIENT_ID) ?>"
                     data-callback="handleGoogleLogin"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-size="large"
                     data-theme="outline"
                     data-text="signin_with"
                     data-shape="rectangular"
                     data-logo_alignment="center"
                     data-width="280">
                </div>
            </div>
            <?php endif; ?>
            
            <div class="mt-8 text-center text-sm text-gray-500">
                <a href="<?= baseUrl() ?>/pages/auth/login.php" class="text-purple-600 hover:text-purple-700">
                    ← Login sebagai Pelanggan
                </a>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '<?= baseUrl() ?>/api/auth';
        
        function togglePassword() {
            const field = document.getElementById('password');
            field.type = field.type === 'password' ? 'text' : 'password';
        }
        
        function showError(msg) {
            document.getElementById('alertErrorText').textContent = msg;
            document.getElementById('alertError').classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');
        }
        
        function showSuccess(msg) {
            document.getElementById('alertSuccessText').textContent = msg;
            document.getElementById('alertSuccess').classList.add('show');
            document.getElementById('alertError').classList.remove('show');
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
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value.trim(),
                        password: document.getElementById('password').value,
                        admin_login: true
                    })
                });
                const data = await res.json();
                
                if (data.success) {
                    showSuccess('Login berhasil!');
                    setTimeout(() => window.location.href = data.redirect || '<?= baseUrl() ?>/pages/dashboard.php', 1000);
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
            fetch('<?= baseUrl() ?>/api/google-auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: response.credential, admin_login: true })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (!['superadmin', 'admin', 'cashier'].includes(data.user?.role)) {
                        showError('Akun Anda tidak memiliki akses admin');
                        return;
                    }
                    showSuccess('Berhasil!');
                    setTimeout(() => window.location.href = data.redirect || '<?= baseUrl() ?>/pages/dashboard.php', 1000);
                } else {
                    showError(data.error || 'Login gagal');
                }
            });
        }
    </script>
</body>
</html>
