<?php



require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            // Create new transaction
            $customerId = $_POST['customer_id'] ?? 0;
            $serviceType = sanitize($_POST['service_type'] ?? '');
            $weight = floatval($_POST['weight'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            $price = floatval($_POST['price'] ?? 0);
            $notes = sanitize($_POST['notes'] ?? '');
            
            // Validation
            if (empty($customerId) || empty($serviceType) || $price <= 0) {
                throw new Exception('Data tidak lengkap');
            }
            
            $stmt = $db->prepare("
                INSERT INTO transactions (customer_id, service_type, weight, quantity, price, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            $stmt->execute([$customerId, $serviceType, $weight, $quantity, $price, $notes]);
            
            $response['success'] = true;
            $response['message'] = 'Transaksi berhasil dibuat';
            $response['transaction_id'] = $db->lastInsertId();
            break;
            
        case 'update_status':
            // Update transaction status
            $id = $_POST['id'] ?? 0;
            $status = $_POST['status'] ?? '';
            
            $allowedStatuses = ['pending', 'washing', 'drying', 'ironing', 'done', 'picked_up', 'cancelled'];
            
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('Status tidak valid');
            }
            
            $stmt = $db->prepare("
                UPDATE transactions 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            
            $stmt->execute([$status, $id]);
            
            $response['success'] = true;
            $response['message'] = 'Status berhasil diupdate';

            // Optional: Send email notification (wrapped in try-catch to not block status update)
            try {
                // Get transaction data first
                $txStmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
                $txStmt->execute([$id]);
                $transaction = $txStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transaction) {
                    require_once __DIR__ . '/../includes/email-helper.php';
                    
                    // Get customer data
                    $customerStmt = $db->prepare("
                        SELECT c.*, u.email 
                        FROM customers c 
                        LEFT JOIN users u ON c.user_id = u.id 
                        WHERE c.id = ?
                    ");
                    $customerStmt->execute([$transaction['customer_id']]);
                    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

                    if ($customer && !empty($customer['email'])) {
                        // Send email based on new status
                        if ($status === 'done') {
                            sendReadyForPickupEmail($transaction, $customer);
                        } else {
                            sendOrderStatusEmail($transaction, $customer, $status);
                        }
                    }
                }
            } catch (Exception $emailError) {
                // Log error but don't fail the status update
                error_log("Email notification failed: " . $emailError->getMessage());
            }
            break;
            
        case 'get_by_id':
            // Get single transaction
            $id = $_GET['id'] ?? 0;
            
            $stmt = $db->prepare("
                SELECT t.*, c.name as customer_name, c.phone, c.address
                FROM transactions t
                JOIN customers c ON t.customer_id = c.id
                WHERE t.id = ?
            ");
            
            $stmt->execute([$id]);
            $transaction = $stmt->fetch();
            
            if (!$transaction) {
                throw new Exception('Transaksi tidak ditemukan');
            }
            
            $response['success'] = true;
            $response['data'] = $transaction;
            break;
            
        case 'get_all':
            // Get all transactions
            $stmt = $db->query("
                SELECT t.*, c.name as customer_name, c.phone
                FROM transactions t
                JOIN customers c ON t.customer_id = c.id
                ORDER BY t.created_at DESC
            ");
            
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll();
            break;
            
        case 'delete':
            // Delete transaction
            $id = $_POST['id'] ?? 0;
            
            $stmt = $db->prepare("DELETE FROM transactions WHERE id = ?");
            $stmt->execute([$id]);
            
            $response['success'] = true;
            $response['message'] = 'Transaksi berhasil dihapus';
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
