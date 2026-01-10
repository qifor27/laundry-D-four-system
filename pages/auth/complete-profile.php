<?php
 
 require_once __DIR__ . '/../../includes/functions.php';
 require_once __DIR__ . '/../../includes/auth.php';

 // step 1: cek apakah user sudah login

 if (!isLoggedIn()) {
    header('Location: '. baseUrl() . '/pages/auth/login.php');
    exit;
 }

 // ambil data user dari session
 $userData = getUserData();

 // step 2: cek apakah phone sudah ada 
 // jika phone sudah ada, redirect ke dashboard sesuai role
 if (!empty($_SESSION['user_phone'])) {
    $role = $userData['role'] ?? 'user';
    if(in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
 }

 // cek dari database untuk memastikan 
 require_once __DIR__ . '/../../config/database_mysql.php';
 $db = Database::getInstance()->getConnection();
 $stmt = $db->prepare("SELECT phone FROM users WHERE id = ?");
 $stmt->execute([$userData['id']]);
 $userFromDb = $stmt->fetch(PDO::FETCH_ASSOC);

 if (!empty($userFromDb['phone'])) {
    // update session dan redirect
    $_SESSION['user_phone'] = $userFromDb['phone'];
    $role = $userData['role'] ?? 'user';
    if (in_array($role, ['superadmin', 'admin', 'cashier'])) {
        header('Location: ' . baseUrl() . '/pages/dashboard.php');
    } else {
        header('Location: ' . baseUrl() . '/pages/customer-dashboard.php');
    }
    exit;
 }

 // set page title
 $pageTitle = "Lengkapi Profil - D'four Laundry";
 ?>

 <!DOCTYPE html>
 <html lang="en">
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

    <style>
        /* Container utama - full screen centered */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card dengan glassmorphism effect */
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        /* Style input form */
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
        }

        /* Button primary dengan gradient */
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
            transition: all 0.2s ease;
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

        /* Alert styles */
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

        /* Avatar user */
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .info-box p {
            color: #0369a1;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
 </head>

 <body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 font-outfit">
    
    <!-- Bubble Decorations - Element dekoratif background -->
    <div class="bubble-decoration">
        <div class="bubble bubble-pink w-96 h-96 -top-20 -left-20"></div>
        <div class="bubble bubble-purple w-80 h-80 top-40 right-10"></div>
        <div class="bubble bubble-blue w-72 h-72 bottom-20 left-1/4"></div>
        <div class="bubble bubble-cyan w-64 h-64 bottom-10 right-1/3"></div>
    </div>

    <div class="auth-container relative z-10">
        <div class="auth-card">
            <!-- Logo -->
            <div class="text-center mb-6">
                <a href="<?= baseUrl() ?>" class="inline-flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">D</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">D'four<span class="text-primary-600">Laundry</span></span>
                </a>
            </div>

            <!-- User Avatar & Welcome -->
            <div class="text-center mb-6">
                <?php if (!empty($userData['picture'])): ?>
                    <img src="<?= htmlspecialchars($userData['picture']) ?>" 
                         alt="Profile" 
                         class="user-avatar mx-auto mb-4">
                <?php else: ?>
                    <!-- Default avatar jika tidak ada picture -->
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white font-bold text-2xl">
                            <?= strtoupper(substr($userData['name'] ?? 'U', 0, 1)) ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <h1 class="text-xl font-bold text-gray-900 mb-1">
                    Halo, <?= htmlspecialchars($userData['name'] ?? 'User') ?>! 👋
                </h1>
                <p class="text-gray-600">Satu langkah lagi untuk melengkapi profil Anda</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <strong>📱 Mengapa perlu nomor HP?</strong><br>
                    Nomor HP digunakan untuk menghubungkan akun Anda dengan 
                    data transaksi laundry dan notifikasi status pesanan.
                </p>
            </div>

            <!-- Alerts -->
            <div id="alertError" class="alert alert-error">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="alertErrorText"></span>
            </div>

            <div id="alertSuccess" class="alert alert-success">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="alertSuccessText"></span>
            </div>

            <!-- Form -->
            <form id="completeProfileForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           required
                           class="form-input"
                           placeholder="Contoh: 081234567890"
                           pattern="^08[0-9]{8,12}$"
                           maxlength="14">
                    <p class="text-xs text-gray-500 mt-2">
                        Format: 08xxxxxxxxxx (10-14 digit)
                    </p>
                </div>

                <button type="submit" id="submitBtn" class="btn-primary">
                    <span id="submitText">Simpan & Lanjutkan</span>
                    <span id="submitLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" 
                                    stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" 
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </form>

            <!-- Logout Link -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-500 text-sm mb-2">Bukan <?= htmlspecialchars($userData['name'] ?? 'Anda') ?>?</p>
                <a href="<?= baseUrl() ?>/api/logout.php" 
                   class="text-red-500 hover:text-red-600 text-sm font-medium">
                    Logout dari akun ini
                </a>
            </div>
        </div>
    </div>

    <script>
        // java script form handling

        const API_URL = "<?= baseUrl() ?>/api/auth/update-phone.php";

        /**
         * Tampilkan pesan error
         * @param {string} message - Pesan error
         */

        function showError(message) {
            const el = document.getElementById('alertError');
            document.getElementById('alertErrorText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');

            // scroll ke alert
            el.scrollIntoView({
                behavior: 'smooth', 
                block: 'center'
            });
        }

        /**
         * Tampilkan pesan sukses
         * @param {string} message - Pesan sukses
         */
        function showSuccess(message) {
            const el = document.getElementById('alertSuccess');
            document.getElementById('alertSuccessText').textContent = message;
            el.classList.add('show');
            document.getElementById('alertError').classList.remove('show');
        }

        /**
         * Sembunyikan semua alert
         */
        function hideAlerts() {
            document.getElementById('alertError').classList.remove('show');
            document.getElementById('alertSuccess').classList.remove('show');
        }

        /**
         * Set button ke state loading
         * @param {boolean} isLoading - True jika loading
         */
        function setLoading(isLoading) {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('submitText');
            const btnLoading = document.getElementById('submitLoading');
            
            btn.disabled = isLoading;
            
            if (isLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            } else {
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        }

        /**
         * Validasi format nomor HP
         * @param {string} phone - Nomor HP
         * @returns {boolean} True jika valid
         */
        function validatePhone(phone) {
            // Regex: 08 diikuti 8-12 digit angka
            const regex = /^08[0-9]{8,12}$/;
            return regex.test(phone);
        }

        // ============================================
        // Event Listener - Form Submit
        // ============================================
        
        document.getElementById('completeProfileForm').addEventListener('submit', async (e) => {
            // Cegah form submit default (reload page)
            e.preventDefault();
            
            // Sembunyikan alert sebelumnya
            hideAlerts();
            
            // Ambil nilai input
            const phone = document.getElementById('phone').value.trim();
            
            // Validasi client-side
            if (!phone) {
                showError('Nomor HP wajib diisi');
                return;
            }
            
            if (!validatePhone(phone)) {
                showError('Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx');
                return;
            }
            
            // Set loading state
            setLoading(true);
            
            try {
                // Kirim request ke API
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ phone: phone })
                });
                
                // Parse response JSON
                const data = await response.json();
                
                if (data.success) {
                    // Sukses - tampilkan pesan dan redirect
                    showSuccess(data.message || 'Profil berhasil dilengkapi!');
                    
                    // Redirect setelah 1.5 detik
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= baseUrl() ?>/pages/customer-dashboard.php';
                    }, 1500);
                } else {
                    // Error dari API
                    showError(data.error || 'Terjadi kesalahan');
                    setLoading(false);
                }
            } catch (error) {
                // Network error atau error lainnya
                console.error('Error:', error);
                showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
                setLoading(false);
            }
        });

        // ============================================
        // Auto-format input phone
        // ============================================
        
        document.getElementById('phone').addEventListener('input', function(e) {
            // Hapus karakter non-angka
            let value = this.value.replace(/\D/g, '');
            
            // Pastikan dimulai dengan 08
            if (value.length >= 2 && !value.startsWith('08')) {
                // Jika user mulai dengan 8, tambahkan 0 di depan
                if (value.startsWith('8')) {
                    value = '0' + value;
                }
            }
            
            // Batasi maksimal 14 karakter
            if (value.length > 14) {
                value = value.substring(0, 14);
            }
            
            this.value = value;
        });
    </script>
 </body>
 </html>