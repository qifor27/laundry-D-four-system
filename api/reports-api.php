<?php
/**
 * Reports API
 * 
 * API untuk mengambil data laporan bulanan.
 * 
 * Endpoint: GET /api/reports-api.php?action=xxx&month=MM&year=YYYY
 * 
 * Actions:
 * - monthly_summary: Ringkasan bulanan
 * - daily_breakdown: Detail per hari
 * - service_report: Laporan per layanan
 * - customer_report: Laporan pelanggan
 * - status_report: Laporan status
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// harus login sebagai admin
requireLogin();

$db = Database::getInstance()->getConnection();
$response = ['success' => false, 'data' => null];

try {
    $action = $_GET['action'] ?? '';

    // default ke bulan dan tahun sekarang
    $month = intval($_GET['month'] ?? date('m'));
    $year = intval($_GET['year'] ?? date('Y'));

    // validasi bulan dan tahun
    if ($month < 1 || $month > 12) {
        throw new Exception('Bulan tidak valid');
    }
    if ($year < 2020 || $year > 2100) {
        throw new Exception('Tahun tidak valid');
    }

    // format untuk query: YYYY-MM
    $periodStart = sprintf('%04d-%02d-01', $year, $month);
    $periodEnd = date('Y-m-t', strtotime($periodStart)); // Last day of month

    switch ($action) {
        // ================================================
        // ACTION: monthly_summary
        // Ringkasan transaksi bulan ini
        // ================================================
        case 'monthly_summary':
            // current month stats
            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_transactions,
                    COALESCE(SUM(price), 0) as total_revenue,
                    COALESCE(AVG(price), 0) as average_per_transaction,
                    COALESCE(SUM(weight), 0) as total_weight
                FROM transactions
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // previous month stats for comparison
            $prevMonth = $month == 1 ? 12 : $month - 1;
            $prevYear = $month == 1 ? $year -1 : $year;
            $prevPeriodStart = sprintf('%04d-%02d-01', $prevYear, $prevMonth);
            $prevPeriodEnd = date('Y-m-t', strtotime($prevPeriodStart));

            $stmt->execute([$prevPeriodStart, $prevPeriodEnd]);
            $previous = $stmt->fetch(PDO::FETCH_ASSOC);

            // calculate growth percentage
            $revenueGrowth = 0;
            if ($previous['total_revenue'] > 0) {
                $revenueGrowth = (($current['total_revenue'] - $previous['total_revenue']) / $previous['total_revenue']) * 100;
            }

            $transactionGrowth = 0;
            if ($previous['total_transactions'] > 0) {
                $transactionGrowth = (($current['total_transactions'] - $previous['total_transactions']) / $previous['total_transactions']) * 100;
            }

            $response['data'] = [
                'period' => [
                'month' =>$month,
                'year' => $year,
                'month_name' => getIndonesianMonth($month),
                'start_date' => $periodStart,
                'end_date' => $periodEnd
                ],
                'current' => [
                    'total_transactions' => (int)$current['total_transactions'],
                    'total_revenue' => (float)$current['total_revenue'],
                    'average_per_transactions' => round($current['average_per_transaction'], 2),
                    'total_weight' => (float)$current['total_weight']
                ],
                'previous' => [
                    'total_transactions' => (int)$previous['total_transactions'],
                    'total_revenue' => (float)$previous['total_revenue']
                ],
                'growth' => [
                    'revenue_percentage' => round($revenueGrowth, 1),
                    'transaction_percentage' => round($transactionGrowth, 1)
                ]
            ];
            break;

        // ================================================
        // ACTION: daily_breakdown
        // Detail transaksi per hari
        // ================================================
        case 'daily_breakdown':
            $stmt = $db->prepare("
                SELECT
                    DATE(created_at) as date,
                    COUNT(*) as transactions,
                    COALESCE(SUM(price),0) as revenue
                FROM transactions
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $dailyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // fill missing dates with zero
            $filledData = [];
            $currentDate = new DateTime($periodStart);
            $endDate = new DateTime($periodEnd);

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $found = false;

                foreach ($dailyData as $row) {
                    if ($row['date'] === $dateStr) {
                        $filledData[] = [
                            'date' => $dateStr,
                            'day_name' => getIndonesianDay($currentDate->format('N')),
                            'transactions' => (int) $row['transactions'],
                            'revenue' => (float)$row['revenue']
                        ];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $filledData[] = [
                        'date' => $dateStr,
                        'day_name' => getIndonesianDay($currentDate->format('N')),
                        'transactions' => 0,
                        'revenue' => 0
                    ];
                }

                $currentDate->modify('+1 day');
            }

            $response['data'] = $filledData;
            break;

        // ================================================
        // ACTION: service_report
        // Laporan per jenis layanan
        // ================================================
        case 'service_report':
            $stmt = $db->prepare("
                SELECT
                    service_type,
                    COUNT(*) as transactions,
                    COALESCE(SUM(price), 0) as revenue,
                    COALESCE(SUM(weight), 0) as total_weight
                FROM transactions
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
                GROUP BY service_type
                ORDER BY revenue DESC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // calculate percentage
            $totalRevenue = array_sum(array_column($services, 'revenue'));

            foreach ($services as &$service) {

                $service['transactions'] = (int)$service['transactions'];
                $service['revenue'] = (float)$service['revenue'];
                $service['total_weight'] = (float)$service['total_weight'];
                $service['percentage'] = $totalRevenue > 0
                    ? round(($service['revenue'] / $totalRevenue) * 100, 1)
                    : 0;
            }

            $response['data'] = [
                'services' => $services,
                'total_revenue' => $totalRevenue
            ];
            break;

        // ================================================
        // ACTION: customer_report
        // Laporan pelanggan
        // ================================================
        case 'customer_report':
            // top 10 custommer by spending
            $stmt = $db->prepare("
                SELECT 
                    c.id,
                    c.name,
                    c.phone,
                    COUNT(t.id) as transactions,
                    COALESCE(SUM(t.price),0) as total_spending
                FROM customers c
                JOIN transactions t on c.id = t.customer_id
                WHERE DATE(t.created_at) BETWEEN ? AND ?
                AND t.status != 'cancelled'
                GROUP BY c.id, c.name, c.phone
                ORDER BY total_spending DESC
                LIMIT 10
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // new customers this month
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM customers 
                WHERE DATE(created_at) BETWEEN ? AND ?
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $newCustomers = $stmt->fetch()['count'];

            // Total unique customers this month
            $stmt = $db->prepare("
                SELECT COUNT(DISTINCT customer_id) as count
                FROM transactions 
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $activeCustomers = $stmt->fetch()['count'];
            
            $response['data'] = [
                'top_customers' => $topCustomers,
                'new_customers' => (int)$newCustomers,
                'active_customers' => (int)$activeCustomers
            ];
            break;
            
        // ================================================
        // ACTION: status_report
        // Laporan per status transaksi
        // ================================================
        case 'status_report':
            $stmt = $db->prepare("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM transactions 
                WHERE DATE(created_at) BETWEEN ? AND ?
                GROUP BY status
                ORDER BY 
                    CASE status
                        WHEN 'pending' THEN 1
                        WHEN 'washing' THEN 2
                        WHEN 'drying' THEN 3
                        WHEN 'ironing' THEN 4
                        WHEN 'done' THEN 5
                        WHEN 'picked_up' THEN 6
                        WHEN 'cancelled' THEN 7
                    END
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCount = array_sum(array_column($statusData, 'count'));
            
            foreach ($statusData as &$status) {
                $status['count'] = (int)$status['count'];
                $status['label'] = getStatusLabel($status['status']);
                $status['percentage'] = $totalCount > 0 
                    ? round(($status['count'] / $totalCount) * 100, 1) 
                    : 0;
            }
            
            $response['data'] = [
                'statuses' => $statusData,
                'total' => $totalCount
            ];
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

echo json_encode($response);

// ================================================
// HELPER FUNCTIONS
// ================================================

/**
 * Get Indonesian month name
 */
function getIndonesianMonth($month) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[$month] ?? '';
}

/**
 * Get Indonesian day name
 */
function getIndonesianDay($dayOfWeek) {
    $days = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
    ];
    return $days[$dayOfWeek] ?? '';
}

?>
