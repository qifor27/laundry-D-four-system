<?php
/**
 * Customer Login Page
 * Email/password login with Google OAuth option
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/google-oauth.php';

// If already logged in, redirect
if (isLoggedIn()) {
    $role = getUserData()['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl('pages/dashboard.php'));
    } else {
        header('Location: ' . baseUrl('pages/customer-dashboard.php'));
    }
    exit;
}

$pageTitle = "Login - D'four Laundry";
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
    
    <!-- Google Sign-In -->
    <?php if ($isGoogleConfigured): ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>
    
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
            background: white;
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
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }
        
        .divider-line {
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .divider-text {
            color: #9ca3af;
            font-size: 14px;
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
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit">
    
    <!-- Bubble Decorations -->
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
        <div class="bubble bubble-blue w-72 h-72 bottom-20 left-1/4"></div>
        <div class="bubble bubble-cyan w-64 h-64 bottom-10 right-1/3"></div>
    </div>
    
    <div class="auth-container relative z-10">
        <div class="auth-card">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="<?= baseUrl() ?>" class="inline-flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
            </div>
            
            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang!</h1>
                <p class="text-gray-600">Masuk untuk melanjutkan</p>
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
            
            <!-- Login Form -->
            <form id="loginForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" required
                           class="form-input" 
                           placeholder="contoh@email.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="form-input pr-12" 
                               placeholder="Masukkan password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="passwordEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="<?= baseUrl() ?>/pages/auth/forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700">
                        Lupa password?
                    </a>
                </div>
                
                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Login</span>
                    <span id="submitLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>
            
            <?php if ($isGoogleConfigured): ?>
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">atau</span>
                <div class="divider-line"></div>
            </div>
            
            <!-- Google Sign-In -->
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
                     data-width="300">
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Register Link -->
            <div class="text-center mt-8">
                <p class="text-gray-600">
                    Belum punya akun? 
                    <a href="<?= baseUrl() ?>/pages/auth/register.php" class="text-primary-600 hover:text-primary-700 font-medium">
                        Daftar sekarang
                    </a>
                </p>
            </div>
            
            <!-- Admin Login & Back -->
            <div class="flex justify-center gap-4 mt-4 text-sm">
                <a href="<?= baseUrl() ?>/pages/auth/admin-login.php" class="text-gray-500 hover:text-gray-700">
                    Login Admin
                </a>
                <span class="text-gray-300">|</span>
                <a href="<?= baseUrl() ?>" class="text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '<?= baseUrl() ?>/api/auth';
        
        function togglePassword() {
            const field = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
            } else {
                field.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }
        
        function showError(message) {
            const el = document.getElementById('alertError');
            document.getElementById('alertErrorText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');
        }
        
        function showSuccess(message) {
            const el = document.getElementById('alertSuccess');
            document.getElementById('alertSuccessText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertError').classList.remove('show');
        }
        
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitText').classList.add('hidden');
            document.getElementById('submitLoading').classList.remove('hidden');
            
            try {
                const response = await fetch(`${API_URL}/login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Login berhasil! Mengalihkan...');
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= baseUrl() ?>/pages/customer-dashboard.php';
                    }, 1000);
                } else {
                    showError(data.error || 'Login gagal');
                }
            } catch (error) {
                showError('Terjadi kesalahan. Silakan coba lagi.');
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
                body: JSON.stringify({ credential: response.credential })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Berhasil! Mengalihkan...');
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= baseUrl() ?>/pages/customer-dashboard.php';
                    }, 1000);
                } else {
                    showError(data.error || 'Login dengan Google gagal');
                }
            })
            .catch(() => {
                showError('Terjadi kesalahan. Silakan coba lagi.');
            });
        }
    </script>
</body>
</html>
