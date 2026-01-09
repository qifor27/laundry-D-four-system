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
                    <h3 class="text-2xl font-bold mt-2">${d.current.total_transactions > 0 ? formatRupiah(d.current.average_per_transactions || 0) : 'Rp 0'}</h3>
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