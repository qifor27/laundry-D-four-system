<?php
/**
 * Test WhatsApp Helper
 * HAPUS FILE INI SETELAH TESTING!
 */

require_once 'includes/whatsapp-helper.php';

// Data dummy untuk test
$transaction = [
    'id' => 123,
    'service_type' => 'Cuci Setrika',
    'price' => 50000,
    'status' => 'done'
];

$customer = [
    'name' => 'Budi Santoso',
    'phone' => '08123456789' // Ganti dengan nomor HP Anda untuk test
];

echo "<h1>🧪 Test WhatsApp Helper</h1>";
echo "<p>Klik link di bawah untuk test. WhatsApp Web akan terbuka dengan pesan terisi.</p>";
echo "<hr>";

// Test 1: Order Created
$url1 = getOrderCreatedWAUrl($transaction, $customer);
echo "<h3>1️⃣ Order Created</h3>";
echo "<a href='{$url1}' target='_blank' style='background:#22c55e;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;'>📱 Test: Pesanan Diterima</a>";
echo "<br><br>";

// Test 2: Status Update
$url2 = getStatusUpdateWAUrl($transaction, $customer, 'washing');
echo "<h3>2️⃣ Status Update</h3>";
echo "<a href='{$url2}' target='_blank' style='background:#3b82f6;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;'>📱 Test: Update Status</a>";
echo "<br><br>";

// Test 3: Ready for Pickup
$url3 = getReadyForPickupWAUrl($transaction, $customer);
echo "<h3>3️⃣ Ready for Pickup</h3>";
echo "<a href='{$url3}' target='_blank' style='background:#f59e0b;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;'>📱 Test: Siap Diambil</a>";
echo "<br><br>";

// Test 4: Payment Reminder
$url4 = getPaymentReminderWAUrl($transaction, $customer);
echo "<h3>4️⃣ Payment Reminder</h3>";
echo "<a href='{$url4}' target='_blank' style='background:#ef4444;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;'>📱 Test: Reminder Bayar</a>";
echo "<br><br>";

echo "<hr>";
echo "<p><strong>✅ Jika tombol berfungsi, WhatsApp Web akan terbuka dengan pesan terisi!</strong></p>";
echo "<p style='color:red;'>⚠️ HAPUS FILE INI SETELAH TESTING!</p>";
?>
