# 📚 Tutorial: Monthly Reports (Laporan Bulanan)

**Fitur:** Halaman dan API untuk laporan bulanan transaksi  
**Level:** Backend - Priority MEDIUM  
**Estimasi Waktu:** 3-4 jam  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Struktur Database](#2-struktur-database)
3. [File yang Akan Dibuat](#3-file-yang-akan-dibuat)
4. [Langkah 1: API Reports](#langkah-1-api-reports)
5. [Langkah 2: Halaman Reports](#langkah-2-halaman-reports)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa Fitur Ini Diperlukan?

Pemilik laundry perlu:
1. **Melihat performa bisnis** per bulan
2. **Membandingkan** dengan bulan sebelumnya
3. **Analisis layanan** paling populer
4. **Identifikasi pelanggan** terbaik
5. **Pemantauan** status transaksi

### 📊 Jenis Laporan

| Laporan | Deskripsi |
|---------|-----------|
| **Monthly Summary** | Total transaksi, pendapatan, rata-rata, growth |
| **Daily Breakdown** | Detail per hari dalam bulan |
| **Service Report** | Pendapatan per jenis layanan |
| **Customer Report** | Top customers, customer baru |
| **Status Report** | Breakdown per status transaksi |

---

## 2. Struktur Database

### Tabel `transactions`

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| id | INT | Primary key |
| customer_id | INT | FK ke customers |
| service_type | VARCHAR | Jenis layanan |
| weight | DECIMAL | Berat (kg) |
| quantity | INT | Jumlah item |
| price | DECIMAL | Total harga |
| notes | TEXT | Catatan |
| status | ENUM | pending, washing, drying, ironing, done, picked_up, cancelled |
| created_at | TIMESTAMP | Tanggal transaksi |

---

## 3. File yang Akan Dibuat

| No | File | Deskripsi |
|----|------|-----------|
| 1 | `api/reports-api.php` | API endpoint untuk semua laporan |
| 2 | `pages/reports.php` | Halaman UI laporan |

---

## Langkah 1: API Reports

### 📄 File: `api/reports-api.php`

Buat file baru di lokasi: `c:\xampp\htdocs\laundry-D-four-system\api\reports-api.php`

### Kode Lengkap:

```php
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

// Harus login sebagai admin
requireLogin();

$db = Database::getInstance()->getConnection();
$response = ['success' => false, 'data' => null];

try {
    $action = $_GET['action'] ?? '';
    
    // Default ke bulan dan tahun sekarang
    $month = intval($_GET['month'] ?? date('m'));
    $year = intval($_GET['year'] ?? date('Y'));
    
    // Validasi bulan dan tahun
    if ($month < 1 || $month > 12) {
        throw new Exception('Bulan tidak valid');
    }
    if ($year < 2020 || $year > 2100) {
        throw new Exception('Tahun tidak valid');
    }
    
    // Format untuk query: YYYY-MM
    $periodStart = sprintf('%04d-%02d-01', $year, $month);
    $periodEnd = date('Y-m-t', strtotime($periodStart)); // Last day of month
    
    switch ($action) {
        
        // ================================================
        // ACTION: monthly_summary
        // Ringkasan transaksi bulan ini
        // ================================================
        case 'monthly_summary':
            // Current month stats
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
            
            // Previous month stats for comparison
            $prevMonth = $month == 1 ? 12 : $month - 1;
            $prevYear = $month == 1 ? $year - 1 : $year;
            $prevPeriodStart = sprintf('%04d-%02d-01', $prevYear, $prevMonth);
            $prevPeriodEnd = date('Y-m-t', strtotime($prevPeriodStart));
            
            $stmt->execute([$prevPeriodStart, $prevPeriodEnd]);
            $previous = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate growth percentage
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
                    'month' => $month,
                    'year' => $year,
                    'month_name' => getIndonesianMonth($month),
                    'start_date' => $periodStart,
                    'end_date' => $periodEnd
                ],
                'current' => [
                    'total_transactions' => (int)$current['total_transactions'],
                    'total_revenue' => (float)$current['total_revenue'],
                    'average_per_transaction' => round($current['average_per_transaction'], 2),
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
                    COALESCE(SUM(price), 0) as revenue
                FROM transactions 
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $dailyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fill missing dates with zero
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
                            'transactions' => (int)$row['transactions'],
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
            
            // Calculate percentage
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
            // Top 10 customers by spending
            $stmt = $db->prepare("
                SELECT 
                    c.id,
                    c.name,
                    c.phone,
                    COUNT(t.id) as transactions,
                    COALESCE(SUM(t.price), 0) as total_spending
                FROM customers c
                JOIN transactions t ON c.id = t.customer_id
                WHERE DATE(t.created_at) BETWEEN ? AND ?
                AND t.status != 'cancelled'
                GROUP BY c.id, c.name, c.phone
                ORDER BY total_spending DESC
                LIMIT 10
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // New customers this month
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
```

### 📝 Penjelasan per Action:

#### A. monthly_summary
- Total transaksi dan pendapatan bulan ini
- Bandingkan dengan bulan sebelumnya
- Hitung growth percentage

#### B. daily_breakdown
- Detail per hari dalam bulan
- Isi tanggal kosong dengan 0
- Cocok untuk chart trend

#### C. service_report
- Pendapatan per jenis layanan
- Persentase kontribusi
- Layanan paling populer

#### D. customer_report
- Top 10 pelanggan
- Customer baru bulan ini
- Customer aktif

#### E. status_report
- Breakdown per status
- Transaksi pending, done, cancelled, dll

---

## Langkah 2: Halaman Reports

### 📄 File: `pages/reports.php`

Buat file baru di lokasi: `c:\xampp\htdocs\laundry-D-four-system\pages\reports.php`

### Kode Lengkap:

```php
<?php
/**
 * Reports Page
 * Halaman untuk melihat laporan bulanan
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Laporan Bulanan - D'four Laundry";

// Default month/year
$selectedMonth = intval($_GET['month'] ?? date('m'));
$selectedYear = intval($_GET['year'] ?? date('Y'));

include __DIR__ . '/../includes/header-admin.php';
?>

<!-- Reports Content -->
<div>
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📊 Laporan Bulanan</h1>
            <p class="text-gray-600">Statistik dan analisis transaksi</p>
        </div>
        
        <!-- Period Filter -->
        <form method="GET" class="flex gap-3 mt-4 md:mt-0">
            <select name="month" class="form-select rounded-lg border-gray-300">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $selectedMonth ? 'selected' : '' ?>>
                        <?= getIndonesianMonth($m) ?>
                    </option>
                <?php endfor; ?>
            </select>
            
            <select name="year" class="form-select rounded-lg border-gray-300">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>
            
            <button type="submit" class="btn-primary px-6">
                Tampilkan
            </button>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Cards akan di-load via AJAX -->
        <div class="card animate-pulse">
            <div class="h-20 bg-gray-200 rounded"></div>
        </div>
        <div class="card animate-pulse">
            <div class="h-20 bg-gray-200 rounded"></div>
        </div>
        <div class="card animate-pulse">
            <div class="h-20 bg-gray-200 rounded"></div>
        </div>
        <div class="card animate-pulse">
            <div class="h-20 bg-gray-200 rounded"></div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Trend Chart -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Trend Harian</h3>
            <canvas id="dailyChart" height="250"></canvas>
        </div>
        
        <!-- Service Breakdown -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 mb-4">🧺 Per Layanan</h3>
            <canvas id="serviceChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Customers -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 mb-4">🏆 Top Pelanggan</h3>
            <div id="topCustomersTable">
                <div class="animate-pulse space-y-3">
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        
        <!-- Status Breakdown -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Status Transaksi</h3>
            <div id="statusTable">
                <div class="animate-pulse space-y-3">
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const API_BASE = '<?= baseUrl() ?>/api/reports-api.php';
const MONTH = <?= $selectedMonth ?>;
const YEAR = <?= $selectedYear ?>;

// Format Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Load Monthly Summary
async function loadMonthlySummary() {
    try {
        const res = await fetch(`${API_BASE}?action=monthly_summary&month=${MONTH}&year=${YEAR}`);
        const data = await res.json();
        
        if (data.success) {
            const d = data.data;
            const growthClass = d.growth.revenue_percentage >= 0 ? 'text-green-600' : 'text-red-600';
            const growthIcon = d.growth.revenue_percentage >= 0 ? '↑' : '↓';
            
            document.getElementById('summaryCards').innerHTML = `
                <div class="card-gradient">
                    <p class="text-sm font-medium opacity-90">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold mt-2">${formatRupiah(d.current.total_revenue)}</h3>
                    <p class="text-xs mt-1 ${growthClass}">${growthIcon} ${Math.abs(d.growth.revenue_percentage)}% dari bulan lalu</p>
                </div>
                <div class="bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl p-6 text-white shadow-xl">
                    <p class="text-sm font-medium opacity-90">Total Transaksi</p>
                    <h3 class="text-2xl font-bold mt-2">${d.current.total_transactions}</h3>
                    <p class="text-xs opacity-75 mt-1">${d.growth.transaction_percentage >= 0 ? '↑' : '↓'} ${Math.abs(d.growth.transaction_percentage)}% dari bulan lalu</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                    <p class="text-sm font-medium opacity-90">Rata-rata Transaksi</p>
                    <h3 class="text-2xl font-bold mt-2">${formatRupiah(d.current.average_per_transaction)}</h3>
                    <p class="text-xs opacity-75 mt-1">Per transaksi</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
                    <p class="text-sm font-medium opacity-90">Total Berat</p>
                    <h3 class="text-2xl font-bold mt-2">${d.current.total_weight} Kg</h3>
                    <p class="text-xs opacity-75 mt-1">${d.period.month_name} ${d.period.year}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading summary:', error);
    }
}

// Load Daily Chart
async function loadDailyChart() {
    try {
        const res = await fetch(`${API_BASE}?action=daily_breakdown&month=${MONTH}&year=${YEAR}`);
        const data = await res.json();
        
        if (data.success) {
            const labels = data.data.map(d => d.date.substring(8)); // Just day number
            const revenues = data.data.map(d => d.revenue);
            
            new Chart(document.getElementById('dailyChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: revenues,
                        borderColor: '#9333ea',
                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: value => formatRupiah(value)
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading daily chart:', error);
    }
}

// Load Service Chart
async function loadServiceChart() {
    try {
        const res = await fetch(`${API_BASE}?action=service_report&month=${MONTH}&year=${YEAR}`);
        const data = await res.json();
        
        if (data.success) {
            const services = data.data.services;
            
            new Chart(document.getElementById('serviceChart'), {
                type: 'doughnut',
                data: {
                    labels: services.map(s => s.service_type),
                    datasets: [{
                        data: services.map(s => s.revenue),
                        backgroundColor: [
                            '#9333ea', '#ec4899', '#f97316', '#22c55e', '#3b82f6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading service chart:', error);
    }
}

// Load Top Customers
async function loadTopCustomers() {
    try {
        const res = await fetch(`${API_BASE}?action=customer_report&month=${MONTH}&year=${YEAR}`);
        const data = await res.json();
        
        if (data.success) {
            const customers = data.data.top_customers;
            
            if (customers.length === 0) {
                document.getElementById('topCustomersTable').innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada data</p>';
                return;
            }
            
            let html = '<table class="w-full"><tbody>';
            customers.forEach((c, i) => {
                html += `
                    <tr class="border-b">
                        <td class="py-2 font-bold text-primary-600">#${i + 1}</td>
                        <td class="py-2"><div class="font-medium">${c.name}</div><div class="text-xs text-gray-500">${c.phone}</div></td>
                        <td class="py-2 text-right font-semibold">${formatRupiah(c.total_spending)}</td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            html += `<p class="text-sm text-gray-500 mt-4">Customer aktif: ${data.data.active_customers} | Baru: ${data.data.new_customers}</p>`;
            
            document.getElementById('topCustomersTable').innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading customers:', error);
    }
}

// Load Status Table
async function loadStatusTable() {
    try {
        const res = await fetch(`${API_BASE}?action=status_report&month=${MONTH}&year=${YEAR}`);
        const data = await res.json();
        
        if (data.success) {
            const statuses = data.data.statuses;
            
            if (statuses.length === 0) {
                document.getElementById('statusTable').innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada data</p>';
                return;
            }
            
            let html = '<div class="space-y-3">';
            statuses.forEach(s => {
                html += `
                    <div class="flex items-center justify-between">
                        <span class="text-sm">${s.label}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-primary-500" style="width: ${s.percentage}%"></div>
                            </div>
                            <span class="text-sm font-medium w-16 text-right">${s.count} (${s.percentage}%)</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('statusTable').innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading status:', error);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadMonthlySummary();
    loadDailyChart();
    loadServiceChart();
    loadTopCustomers();
    loadStatusTable();
});
</script>

<?php 
// Helper function
function getIndonesianMonth($month) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[$month] ?? '';
}

include __DIR__ . '/../includes/footer-admin.php'; 
?>
```

---

## Testing

### A. Test API Manual

1. **Monthly Summary:**
   ```
   http://localhost/laundry-D-four-system/api/reports-api.php?action=monthly_summary&month=1&year=2026
   ```

2. **Daily Breakdown:**
   ```
   http://localhost/laundry-D-four-system/api/reports-api.php?action=daily_breakdown&month=1&year=2026
   ```

3. **Service Report:**
   ```
   http://localhost/laundry-D-four-system/api/reports-api.php?action=service_report&month=1&year=2026
   ```

### B. Test Halaman UI

1. Buka: `http://localhost/laundry-D-four-system/pages/reports.php`
2. Pilih bulan dan tahun
3. Klik "Tampilkan"
4. Verifikasi:
   - Summary cards tampil
   - Chart trend harian tampil
   - Chart layanan tampil
   - Top customers tampil
   - Status breakdown tampil

---

## Troubleshooting

### ❌ Error: "Anda harus login"

**Solusi:** Login terlebih dahulu sebagai admin

### ❌ Data tidak tampil

**Solusi:** Pastikan ada data transaksi di database untuk periode yang dipilih

### ❌ Chart tidak muncul

**Solusi:** Cek console browser untuk error JavaScript. Pastikan Chart.js ter-load.

---

## ✅ Checklist Implementasi

- [ ] Buat file `api/reports-api.php`
- [ ] Buat file `pages/reports.php`
- [ ] Test API monthly_summary
- [ ] Test API daily_breakdown
- [ ] Test API service_report
- [ ] Test API customer_report
- [ ] Test API status_report
- [ ] Test halaman reports.php
- [ ] Verifikasi data sesuai database

---

**Selamat Coding! 📊**
