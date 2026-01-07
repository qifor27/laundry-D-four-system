<?php
/**
 * Forgot Password Page
 */

require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = "Lupa Password - D'four Laundry";
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
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .auth-card { background: rgba(255,255,255,0.9); backdrop-filter: blur(20px); border-radius: 24px; padding: 40px; width: 100%; max-width: 440px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1); }
        .form-input { width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; transition: all 0.2s; }
        .form-input:focus { outline: none; border-color: #9333ea; box-shadow: 0 0 0 3px rgba(147,51,234,0.1); }
        .btn-primary { width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(147,51,234,0.3); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .alert { padding: 14px 16px; border-radius: 12px; margin-bottom: 20px; display: none; }
        .alert.show { display: flex; align-items: flex-start; gap: 12px; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
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
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
            </div>
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h1>
                <p class="text-gray-600">Masukkan email Anda untuk reset password</p>
            </div>
            
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
            
            <form id="forgotForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" required class="form-input" placeholder="contoh@email.com">
                </div>
                
                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Kirim Link Reset</span>
                    <span id="submitLoading" class="hidden">Mengirim...</span>
                </button>
            </form>
            
            <div class="text-center mt-6">
                <a href="<?= baseUrl('pages/auth/login.php') ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                    ← Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitText').classList.add('hidden');
            document.getElementById('submitLoading').classList.remove('hidden');
            
            try {
                const res = await fetch('<?= baseUrl('api/auth/forgot-password.php') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: document.getElementById('email').value.trim() })
                });
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('alertSuccessText').textContent = data.message || 'Link reset password telah dikirim ke email Anda';
                    document.getElementById('alertSuccess').classList.add('show');
                    document.getElementById('alertError').classList.remove('show');
                    document.getElementById('forgotForm').reset();
                } else {
                    document.getElementById('alertErrorText').textContent = data.error || 'Gagal mengirim email';
                    document.getElementById('alertError').classList.add('show');
                    document.getElementById('alertSuccess').classList.remove('show');
                }
            } catch (e) {
                document.getElementById('alertErrorText').textContent = 'Terjadi kesalahan';
                document.getElementById('alertError').classList.add('show');
            } finally {
                btn.disabled = false;
                document.getElementById('submitText').classList.remove('hidden');
                document.getElementById('submitLoading').classList.add('hidden');
            }
        });
    </script>
</body>
</html>
