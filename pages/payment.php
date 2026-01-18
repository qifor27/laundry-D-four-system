<?php

/**
 * Payment Page
 * 
 * Halaman pembayaran untuk pelanggan terdaftar
 * Menggunakan layout yang sama dengan customer-dashboard (ada sidebar)
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../config/midtrans.php';
require_once __DIR__ . '/../config/payment-info.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Pembayaran - D'four Laundry";
$db = Database::getInstance()->getConnection();
$userData = getUserData();

// Get transaction ID from URL
$transactionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$status = $_GET['status'] ?? null;

// Get user's phone number first
$stmt = $db->prepare("SELECT phone FROM users WHERE id = ?");
$stmt->execute([$userData['id']]);
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
$userPhone = $userRow['phone'] ?? '';

// Get user's customer data - try by user_id first, then by phone
$customer = null;

// Try finding by user_id first
$stmt = $db->prepare("SELECT id FROM customers WHERE user_id = ?");
$stmt->execute([$userData['id']]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// If not found by user_id, try finding by phone number
if (!$customer && $userPhone) {
    $stmt = $db->prepare("SELECT id FROM customers WHERE phone = ?");
    $stmt->execute([$userPhone]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // If found, update customer to link with user_id for future lookups
    if ($customer) {
        $updateStmt = $db->prepare("UPDATE customers SET user_id = ? WHERE id = ?");
        $updateStmt->execute([$userData['id'], $customer['id']]);
    }
}

if (!$customer) {
    setFlashMessage('error', 'Data pelanggan tidak ditemukan. Pastikan nomor HP Anda terdaftar.');
    redirect('customer-dashboard.php');
}

// Get transaction if ID provided
$transaction = null;
if ($transactionId > 0) {
    $stmt = $db->prepare("
        SELECT t.*, c.name as customer_name, c.phone as customer_phone
        FROM transactions t
        JOIN customers c ON t.customer_id = c.id
        WHERE t.id = ? AND c.id = ?
    ");
    $stmt->execute([$transactionId, $customer['id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        setFlashMessage('error', 'Transaksi tidak ditemukan');
        redirect('customer-dashboard.php');
    }
}

// Get unpaid transactions
$stmt = $db->prepare("
    SELECT t.*, 
           CASE 
               WHEN t.payment_status = 'unpaid' THEN 'Belum Bayar'
               WHEN t.payment_status = 'pending' THEN 'Menunggu Konfirmasi'
               ELSE 'Lunas'
           END as payment_status_label
    FROM transactions t
    WHERE t.customer_id = ? AND t.payment_status != 'paid'
    ORDER BY t.created_at DESC
");
$stmt->execute([$customer['id']]);
$unpaidTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .payment-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 20px;
        }

        .payment-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .qris-image {
            max-width: 200px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .bank-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            font-family: monospace;
        }

        .price-tag {
            font-size: 1.75rem;
            font-weight: 700;
            color: #8b5cf6;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 via-purple-100/50 to-fuchsia-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar - Sama dengan Dashboard -->
        <aside class="w-20 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 shadow-2xl flex-shrink-0 rounded-r-3xl fixed h-full">
            <div class="h-full flex flex-col items-center py-8">
                <!-- Logo -->
                <div class="mb-10">
                    <a href="<?= baseUrl() ?>">
                        <img src="<?= baseUrl('assets/images/logo.png') ?>" alt="D'four Laundry" class="w-12 h-12">
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 flex flex-col items-center space-y-4">
                    <a href="<?= baseUrl('pages/customer-dashboard.php') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl text-white/60 hover:bg-white/10 hover:text-white transition-all" title="Dashboard">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </a>
                    <!-- PEMBAYARAN - AKTIF -->
                    <a href="<?= baseUrl('pages/payment.php') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/20 text-white shadow-lg" title="Pembayaran">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </a>
                    <a href="<?= baseUrl('pages/customer-dashboard.php#riwayat-pesanan') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl text-white/60 hover:bg-white/10 hover:text-white transition-all" title="Riwayat Pesanan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </a>
                    <a href="<?= baseUrl() ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl text-white/60 hover:bg-white/10 hover:text-white transition-all" title="Beranda">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </a>
                </nav>

                <!-- Logout -->
                <div class="pt-6 border-t border-white/10">
                    <a href="<?= baseUrl('api/auth/logout.php') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all" title="Logout">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-20">
            <!-- Header - Sama dengan Dashboard -->
            <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-40">
                <div class="px-8 py-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                💳 Pembayaran
                            </h1>
                            <p class="text-gray-600 mt-1">Pilih metode pembayaran yang Anda inginkan</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-semibold"><?= strtoupper(substr($userData['name'], 0, 1)) ?></span>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($userData['name']) ?></p>
                                <p class="text-xs text-gray-500">Pelanggan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="px-8 py-8">
                <?php if ($status === 'finish'): ?>
                    <div class="payment-card bg-green-50 border-2 border-green-200 text-center">
                        <div class="text-5xl mb-4">✅</div>
                        <h2 class="text-2xl font-bold text-green-800 mb-2">Pembayaran Berhasil!</h2>
                        <p class="text-green-600 mb-4">Terima kasih, pembayaran Anda sudah diterima.</p>
                        <a href="<?= baseUrl('pages/customer-dashboard.php') ?>" class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Kembali ke Dashboard
                        </a>
                    </div>

                <?php elseif ($status === 'pending'): ?>
                    <div class="payment-card bg-yellow-50 border-2 border-yellow-200 text-center">
                        <div class="text-5xl mb-4">⏳</div>
                        <h2 class="text-2xl font-bold text-yellow-800 mb-2">Menunggu Pembayaran</h2>
                        <p class="text-yellow-700 mb-4">Silakan selesaikan pembayaran Anda sesuai instruksi dari Midtrans.</p>
                        <p class="text-sm text-yellow-600 mb-4">Status akan otomatis terupdate setelah pembayaran dikonfirmasi.</p>
                        <div class="flex justify-center gap-3">
                            <a href="<?= baseUrl('pages/customer-dashboard.php') ?>" class="inline-block px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                Kembali ke Dashboard
                            </a>
                            <a href="?id=<?= $transactionId ?>" class="inline-block px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cek Status
                            </a>
                        </div>
                    </div>

                <?php elseif ($transaction): ?>
                    <!-- Single Transaction Payment -->
                    <div class="payment-card">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="text-sm text-gray-500">Transaksi #<?= $transaction['id'] ?></span>
                                <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($transaction['service_type']) ?></h2>
                                <p class="text-gray-600">
                                    <?= $transaction['weight'] > 0 ? $transaction['weight'] . ' kg' : $transaction['quantity'] . ' pcs' ?>
                                    • <?= formatDate($transaction['created_at']) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="price-tag">Rp <?= number_format($transaction['price'], 0, ',', '.') ?></div>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                                    <?= $transaction['payment_status'] === 'pending' ? 'Menunggu' : 'Belum Bayar' ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($transaction['payment_status'] !== 'paid'): ?>
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Midtrans -->
                                <?php if (isMidtransConfigured()): ?>
                                    <div class="payment-option">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">💳</div>
                                            <div>
                                                <h3 class="font-semibold">Bayar Online</h3>
                                                <p class="text-xs text-gray-500">QRIS, VA, E-Wallet</p>
                                            </div>
                                        </div>
                                        <button onclick="payWithMidtrans(<?= $transaction['id'] ?>)"
                                            class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-bold hover:opacity-90 transition">
                                            Bayar Sekarang
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <!-- QRIS -->
                                <div class="payment-option text-center">
                                    <h3 class="font-semibold mb-3">📱 Scan QRIS</h3>
                                    <img src="<?= getQrisImageUrl() ?>" alt="QRIS" class="qris-image mx-auto mb-2">
                                    <p class="text-xs text-gray-500"><?= QRIS_MERCHANT_NAME ?></p>
                                </div>

                                <!-- Transfer Bank -->
                                <div class="payment-option md:col-span-2">
                                    <h3 class="font-semibold mb-3">🏦 Transfer Bank</h3>
                                    <div class="grid md:grid-cols-2 gap-3">
                                        <?php foreach (getPaymentBankAccounts() as $bank): ?>
                                            <div class="bank-info">
                                                <div class="flex justify-between">
                                                    <span class="font-semibold"><?= $bank['bank'] ?></span>
                                                    <button onclick="copyToClipboard('<?= $bank['account_number'] ?>')" class="text-purple-600 text-xs">📋 Copy</button>
                                                </div>
                                                <div class="text-lg font-bold"><?= $bank['account_number'] ?></div>
                                                <div class="text-xs text-gray-600">a.n. <?= $bank['account_name'] ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- WhatsApp Confirm -->
                            <div class="text-center mt-6 pt-6 border-t">
                                <p class="text-gray-600 mb-3">Sudah transfer? Konfirmasi via WhatsApp:</p>
                                <a href="<?= getPaymentWhatsAppLink($transaction['price'], $transaction['id']) ?>" target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                    Konfirmasi via WhatsApp
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <div class="text-5xl mb-4">✅</div>
                                <h3 class="text-xl font-semibold text-green-600">Pembayaran Lunas</h3>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif (count($unpaidTransactions) > 0): ?>
                    <!-- List of Unpaid Transactions -->
                    <div class="payment-card">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Transaksi Belum Dibayar</h2>
                        <div class="space-y-3">
                            <?php foreach ($unpaidTransactions as $trx): ?>
                                <a href="?id=<?= $trx['id'] ?>" class="block p-4 border-2 border-gray-200 rounded-xl hover:border-purple-400 hover:bg-purple-50 transition">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-sm text-gray-500">#<?= $trx['id'] ?></span>
                                            <h3 class="font-semibold"><?= htmlspecialchars($trx['service_type']) ?></h3>
                                            <p class="text-sm text-gray-500"><?= formatDate($trx['created_at']) ?></p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-purple-600">Rp <?= number_format($trx['price'], 0, ',', '.') ?></div>
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-semibold">
                                                <?= $trx['payment_status_label'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- No Unpaid Transactions -->
                    <div class="payment-card text-center py-12">
                        <div class="text-5xl mb-4">🎉</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Tidak Ada Tagihan</h2>
                        <p class="text-gray-600 mb-6">Semua transaksi Anda sudah lunas!</p>
                        <a href="<?= baseUrl('pages/customer-dashboard.php') ?>" class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Kembali ke Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <?php if (isMidtransConfigured()): ?>
        <script src="<?= getMidtransSnapUrl() ?>" data-client-key="<?= getMidtransClientKey() ?>"></script>
        <script>
            function payWithMidtrans(transactionId) {
                const btn = event.target;
                btn.disabled = true;
                btn.innerHTML = 'Memproses...';

                fetch('<?= baseUrl('api/payment/create-snap.php') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    // Sync status to database before redirect
                                    syncPaymentStatus(transactionId).then(() => {
                                        window.location.href = '?id=' + transactionId + '&status=finish';
                                    });
                                },
                                onPending: function(result) {
                                    window.location.href = '?id=' + transactionId + '&status=pending';
                                },
                                onError: function(result) {
                                    alert('Pembayaran gagal. Silakan coba lagi.');
                                    btn.disabled = false;
                                    btn.innerHTML = 'Bayar Sekarang';
                                },
                                onClose: function() {
                                    // Check if payment was completed before closing
                                    syncPaymentStatus(transactionId).then(response => {
                                        if (response && response.payment_status === 'paid') {
                                            window.location.href = '?id=' + transactionId + '&status=finish';
                                        } else {
                                            btn.disabled = false;
                                            btn.innerHTML = 'Bayar Sekarang';
                                        }
                                    });
                                }
                            });
                        } else {
                            alert(data.error || 'Gagal memproses pembayaran');
                            btn.disabled = false;
                            btn.innerHTML = 'Bayar Sekarang';
                        }
                    })
                    .catch(error => {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        btn.disabled = false;
                        btn.innerHTML = 'Bayar Sekarang';
                    });
            }

            // Sync payment status from Midtrans to database
            async function syncPaymentStatus(transactionId) {
                console.log('Syncing payment status for transaction:', transactionId);
                try {
                    const response = await fetch('<?= baseUrl('api/payment/sync-status.php') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        credentials: 'include', // Important: include cookies for session
                        body: JSON.stringify({
                            transaction_id: transactionId
                        })
                    });
                    const result = await response.json();
                    console.log('Sync result:', result);

                    if (result.success) {
                        console.log('Payment status synced:', result.payment_status);
                    } else {
                        console.error('Sync failed:', result.error);
                    }

                    return result;
                } catch (error) {
                    console.error('Sync error:', error);
                    alert('Error syncing: ' + error.message);
                    return null;
                }
            }

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => alert('Nomor rekening disalin!'));
            }
        </script>
    <?php endif; ?>
</body>

</html>