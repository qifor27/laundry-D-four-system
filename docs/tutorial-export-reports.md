# 📚 Tutorial: Export Reports (PDF & Excel)

**Fitur:** Export laporan ke format PDF dan Excel  
**Level:** Backend - Priority MEDIUM  
**Dependency:** Butuh fitur `monthly-reports` selesai dulu  
**Estimasi Waktu:** 2-3 jam  
**Last Updated:** 2026-01-10

---

## 📋 Daftar Isi

1. [Penjelasan Fitur](#1-penjelasan-fitur)
2. [Library yang Digunakan](#2-library-yang-digunakan)
3. [Instalasi Dependencies](#3-instalasi-dependencies)
4. [Langkah 1: API Export](#langkah-1-api-export)
5. [Langkah 2: Update Halaman Reports](#langkah-2-update-halaman-reports)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## 1. Penjelasan Fitur

### 🤔 Mengapa Fitur Ini Diperlukan?

Pemilik laundry perlu:
1. **Download laporan** untuk arsip fisik
2. **Share laporan** ke investor/stakeholder
3. **Print laporan** untuk keperluan akuntansi
4. **Backup data** dalam format standar

### 📄 Format Export

| Format | Library | Kegunaan |
|--------|---------|----------|
| **Excel (.xlsx)** | PhpSpreadsheet | Data mentah, analisis lanjutan |
| **PDF** | TCPDF / FPDF | Print-ready, formal report |
| **CSV** | Native PHP | Simple, universal compatibility |

---

## 2. Library yang Digunakan

### A. Untuk Excel: PhpSpreadsheet

```
composer require phpoffice/phpspreadsheet
```

**Kenapa PhpSpreadsheet?**
- Standar industri untuk PHP + Excel
- Support format xlsx, xls, csv
- Bisa styling cells, charts, dll

### B. Untuk PDF: TCPDF (atau FPDF)

```
composer require tecnickcom/tcpdf
```

**Kenapa TCPDF?**
- Gratis dan open source
- Support Unicode (bahasa Indonesia)
- Tidak butuh external dependencies

### C. Alternatif Tanpa Composer (Simple CSV)

Jika tidak mau pakai Composer, kita bisa export ke **CSV** menggunakan native PHP saja. Ini yang akan kita pakai di tutorial ini untuk kemudahan.

---

## 3. Instalasi Dependencies

### Opsi A: Dengan Composer (Recommended)

```bash
cd c:\xampp\htdocs\laundry-D-four-system
composer require phpoffice/phpspreadsheet tecnickcom/tcpdf
```

### Opsi B: Tanpa Composer (Simple)

Tutorial ini akan menggunakan pendekatan **tanpa Composer**:
- **CSV** untuk "Excel" export (bisa dibuka di Excel)
- **HTML to Print** untuk "PDF" (print browser)

Ini lebih simple dan tidak butuh setup tambahan.

---

## Langkah 1: API Export

### 📄 File: `api/export-api.php`

Buat file baru di lokasi: `c:\xampp\htdocs\laundry-D-four-system\api\export-api.php`

### Kode Lengkap:

```php
<?php
/**
 * Export API
 * 
 * API untuk mengexport laporan ke berbagai format.
 * 
 * Endpoint: GET /api/export-api.php?type=xxx&month=MM&year=YYYY&format=csv|pdf
 * 
 * Types:
 * - monthly_summary: Ringkasan bulanan
 * - transactions: Detail semua transaksi
 * - customers: Data pelanggan
 */

require_once __DIR__ . '/../config/database_mysql.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Harus login sebagai admin
requireLogin();

$db = Database::getInstance()->getConnection();

try {
    $type = $_GET['type'] ?? '';
    $format = $_GET['format'] ?? 'csv';
    
    // Default ke bulan dan tahun sekarang
    $month = intval($_GET['month'] ?? date('m'));
    $year = intval($_GET['year'] ?? date('Y'));
    
    // Validasi
    if ($month < 1 || $month > 12) {
        throw new Exception('Bulan tidak valid');
    }
    if ($year < 2020 || $year > 2100) {
        throw new Exception('Tahun tidak valid');
    }
    
    // Format untuk query
    $periodStart = sprintf('%04d-%02d-01', $year, $month);
    $periodEnd = date('Y-m-t', strtotime($periodStart));
    $monthName = getIndonesianMonth($month);
    
    switch ($type) {
        
        // ================================================
        // TYPE: monthly_summary
        // Export ringkasan bulanan
        // ================================================
        case 'monthly_summary':
            $filename = "laporan_bulanan_{$monthName}_{$year}";
            
            // Get summary data
            $stmt = $db->prepare("
                SELECT 
                    DATE(created_at) as tanggal,
                    COUNT(*) as jumlah_transaksi,
                    SUM(price) as total_pendapatan,
                    SUM(weight) as total_berat
                FROM transactions 
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
                GROUP BY DATE(created_at)
                ORDER BY tanggal ASC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Headers for CSV
            $headers = ['Tanggal', 'Jumlah Transaksi', 'Total Pendapatan (Rp)', 'Total Berat (Kg)'];
            
            // Format data
            $rows = [];
            $totalPendapatan = 0;
            $totalTransaksi = 0;
            $totalBerat = 0;
            
            foreach ($data as $row) {
                $rows[] = [
                    date('d/m/Y', strtotime($row['tanggal'])),
                    $row['jumlah_transaksi'],
                    number_format($row['total_pendapatan'], 0, ',', '.'),
                    number_format($row['total_berat'], 2, ',', '.')
                ];
                $totalPendapatan += $row['total_pendapatan'];
                $totalTransaksi += $row['jumlah_transaksi'];
                $totalBerat += $row['total_berat'];
            }
            
            // Add total row
            $rows[] = [
                'TOTAL',
                $totalTransaksi,
                number_format($totalPendapatan, 0, ',', '.'),
                number_format($totalBerat, 2, ',', '.')
            ];
            
            if ($format === 'csv') {
                exportCSV($filename, $headers, $rows);
            } else {
                exportPrintView($filename, "Laporan Bulanan - $monthName $year", $headers, $rows);
            }
            break;
            
        // ================================================
        // TYPE: transactions
        // Export detail transaksi
        // ================================================
        case 'transactions':
            $filename = "transaksi_{$monthName}_{$year}";
            
            $stmt = $db->prepare("
                SELECT 
                    t.id,
                    c.name as pelanggan,
                    c.phone as telepon,
                    t.service_type as layanan,
                    t.weight as berat,
                    t.quantity as jumlah,
                    t.price as harga,
                    t.status,
                    t.created_at as tanggal
                FROM transactions t
                JOIN customers c ON t.customer_id = c.id
                WHERE DATE(t.created_at) BETWEEN ? AND ?
                ORDER BY t.created_at DESC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'Pelanggan', 'Telepon', 'Layanan', 'Berat (Kg)', 'Jumlah', 'Harga (Rp)', 'Status', 'Tanggal'];
            
            $rows = [];
            foreach ($data as $row) {
                $rows[] = [
                    $row['id'],
                    $row['pelanggan'],
                    $row['telepon'],
                    $row['layanan'],
                    $row['berat'],
                    $row['jumlah'],
                    number_format($row['harga'], 0, ',', '.'),
                    getStatusLabel($row['status']),
                    date('d/m/Y H:i', strtotime($row['tanggal']))
                ];
            }
            
            if ($format === 'csv') {
                exportCSV($filename, $headers, $rows);
            } else {
                exportPrintView($filename, "Detail Transaksi - $monthName $year", $headers, $rows);
            }
            break;
            
        // ================================================
        // TYPE: customers
        // Export data pelanggan
        // ================================================
        case 'customers':
            $filename = "pelanggan_{$monthName}_{$year}";
            
            $stmt = $db->prepare("
                SELECT 
                    c.id,
                    c.name as nama,
                    c.phone as telepon,
                    c.address as alamat,
                    COUNT(t.id) as total_transaksi,
                    COALESCE(SUM(t.price), 0) as total_belanja
                FROM customers c
                LEFT JOIN transactions t ON c.id = t.customer_id 
                    AND DATE(t.created_at) BETWEEN ? AND ?
                    AND t.status != 'cancelled'
                GROUP BY c.id, c.name, c.phone, c.address
                ORDER BY total_belanja DESC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'Nama', 'Telepon', 'Alamat', 'Total Transaksi', 'Total Belanja (Rp)'];
            
            $rows = [];
            foreach ($data as $row) {
                $rows[] = [
                    $row['id'],
                    $row['nama'],
                    $row['telepon'],
                    $row['alamat'] ?? '-',
                    $row['total_transaksi'],
                    number_format($row['total_belanja'], 0, ',', '.')
                ];
            }
            
            if ($format === 'csv') {
                exportCSV($filename, $headers, $rows);
            } else {
                exportPrintView($filename, "Data Pelanggan - $monthName $year", $headers, $rows);
            }
            break;
            
        // ================================================
        // TYPE: service_report
        // Export laporan per layanan
        // ================================================
        case 'service_report':
            $filename = "laporan_layanan_{$monthName}_{$year}";
            
            $stmt = $db->prepare("
                SELECT 
                    service_type as layanan,
                    COUNT(*) as jumlah_transaksi,
                    SUM(weight) as total_berat,
                    SUM(price) as total_pendapatan
                FROM transactions 
                WHERE DATE(created_at) BETWEEN ? AND ?
                AND status != 'cancelled'
                GROUP BY service_type
                ORDER BY total_pendapatan DESC
            ");
            $stmt->execute([$periodStart, $periodEnd]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['Layanan', 'Jumlah Transaksi', 'Total Berat (Kg)', 'Total Pendapatan (Rp)'];
            
            $rows = [];
            $grandTotal = 0;
            foreach ($data as $row) {
                $rows[] = [
                    $row['layanan'],
                    $row['jumlah_transaksi'],
                    number_format($row['total_berat'], 2, ',', '.'),
                    number_format($row['total_pendapatan'], 0, ',', '.')
                ];
                $grandTotal += $row['total_pendapatan'];
            }
            
            // Add total
            $rows[] = ['TOTAL', '-', '-', number_format($grandTotal, 0, ',', '.')];
            
            if ($format === 'csv') {
                exportCSV($filename, $headers, $rows);
            } else {
                exportPrintView($filename, "Laporan Per Layanan - $monthName $year", $headers, $rows);
            }
            break;
            
        default:
            throw new Exception('Type tidak valid');
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ================================================
// HELPER FUNCTIONS
// ================================================

/**
 * Export to CSV (can be opened in Excel)
 */
function exportCSV($filename, $headers, $rows) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add BOM for Excel UTF-8 compatibility
    echo "\xEF\xBB\xBF";
    
    // Output to php://output
    $output = fopen('php://output', 'w');
    
    // Write headers
    fputcsv($output, $headers, ';');
    
    // Write data rows
    foreach ($rows as $row) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}

/**
 * Export to Print View (HTML that can be printed to PDF)
 */
function exportPrintView($filename, $title, $headers, $rows) {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #9333ea;
            }
            .header h1 { 
                color: #9333ea;
                font-size: 24px;
                margin-bottom: 5px;
            }
            .header p { color: #666; font-size: 14px; }
            .meta {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
                font-size: 12px;
                color: #666;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px 8px;
                text-align: left;
                font-size: 12px;
            }
            th {
                background: #9333ea;
                color: white;
                font-weight: 600;
            }
            tr:nth-child(even) { background: #f9f9f9; }
            tr:last-child { font-weight: bold; background: #f0f0f0; }
            .actions {
                text-align: center;
                margin-top: 20px;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: #9333ea;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                margin: 0 5px;
            }
            .btn:hover { background: #7e22ce; }
            .btn-secondary { background: #6b7280; }
            .btn-secondary:hover { background: #4b5563; }
            
            @media print {
                body { background: white; padding: 0; }
                .container { box-shadow: none; padding: 0; }
                .actions { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>D'four Laundry</h1>
                <p><?= htmlspecialchars($title) ?></p>
            </div>
            
            <div class="meta">
                <span>Dicetak: <?= date('d/m/Y H:i') ?></span>
                <span>Total: <?= count($rows) ?> baris</span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <?php foreach ($headers as $header): ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="actions">
                <button class="btn" onclick="window.print()">🖨️ Print / Save PDF</button>
                <button class="btn btn-secondary" onclick="window.close()">✕ Tutup</button>
            </div>
        </div>
        
        <script>
            // Auto print dialog (optional)
            // window.onload = function() { window.print(); }
        </script>
    </body>
    </html>
    <?php
    exit;
}

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
?>
```

### 📝 Penjelasan Export Types:

| Type | Deskripsi | Data yang di-export |
|------|-----------|---------------------|
| `monthly_summary` | Ringkasan bulanan | Per hari: transaksi, pendapatan, berat |
| `transactions` | Detail transaksi | Semua transaksi bulan tersebut |
| `customers` | Data pelanggan | List customer dengan total belanja |
| `service_report` | Per layanan | Pendapatan per jenis layanan |

### 📝 Penjelasan Format:

| Format | Output |
|--------|--------|
| `csv` | File .csv yang bisa dibuka di Excel |
| `pdf` | Halaman HTML yang bisa di-print/save as PDF |

---

## Langkah 2: Update Halaman Reports

Tambahkan tombol export di `pages/reports.php`.

### Kode yang Perlu Ditambahkan:

Setelah `<!-- Page Header -->`, tambahkan section export buttons:

```php
<!-- Export Buttons -->
<div class="card mb-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">📤 Export Laporan</h3>
    <div class="flex flex-wrap gap-3">
        <!-- Export Monthly Summary -->
        <div class="flex gap-2">
            <a href="<?= baseUrl() ?>/api/export-api.php?type=monthly_summary&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=csv" 
               class="btn-secondary text-sm">
                📊 Ringkasan (Excel)
            </a>
            <a href="<?= baseUrl() ?>/api/export-api.php?type=monthly_summary&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=pdf" 
               target="_blank"
               class="btn-secondary text-sm">
                📊 Ringkasan (Print)
            </a>
        </div>
        
        <!-- Export Transactions -->
        <div class="flex gap-2">
            <a href="<?= baseUrl() ?>/api/export-api.php?type=transactions&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=csv" 
               class="btn-secondary text-sm">
                📋 Transaksi (Excel)
            </a>
            <a href="<?= baseUrl() ?>/api/export-api.php?type=transactions&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=pdf" 
               target="_blank"
               class="btn-secondary text-sm">
                📋 Transaksi (Print)
            </a>
        </div>
        
        <!-- Export Service Report -->
        <div class="flex gap-2">
            <a href="<?= baseUrl() ?>/api/export-api.php?type=service_report&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=csv" 
               class="btn-secondary text-sm">
                🧺 Per Layanan (Excel)
            </a>
            <a href="<?= baseUrl() ?>/api/export-api.php?type=service_report&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=pdf" 
               target="_blank"
               class="btn-secondary text-sm">
                🧺 Per Layanan (Print)
            </a>
        </div>
        
        <!-- Export Customers -->
        <div class="flex gap-2">
            <a href="<?= baseUrl() ?>/api/export-api.php?type=customers&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=csv" 
               class="btn-secondary text-sm">
                👥 Pelanggan (Excel)
            </a>
            <a href="<?= baseUrl() ?>/api/export-api.php?type=customers&month=<?= $selectedMonth ?>&year=<?= $selectedYear ?>&format=pdf" 
               target="_blank"
               class="btn-secondary text-sm">
                👥 Pelanggan (Print)
            </a>
        </div>
    </div>
</div>
```

---

## Testing

### A. Test Export CSV

1. Buka browser dan login sebagai admin
2. Akses URL:
   ```
   http://localhost/laundry-D-four-system/api/export-api.php?type=monthly_summary&month=1&year=2026&format=csv
   ```
3. File CSV harus ter-download
4. Buka file di Excel, pastikan data tampil dengan benar

### B. Test Export Print/PDF

1. Akses URL:
   ```
   http://localhost/laundry-D-four-system/api/export-api.php?type=monthly_summary&month=1&year=2026&format=pdf
   ```
2. Halaman print view akan muncul
3. Klik tombol "Print / Save PDF"
4. Pilih "Save as PDF" untuk menyimpan

### C. Test via Halaman Reports

1. Buka: `http://localhost/laundry-D-four-system/pages/reports.php`
2. Klik tombol-tombol export
3. Verifikasi file ter-download dengan benar

---

## Troubleshooting

### ❌ CSV tidak bisa dibuka di Excel

**Gejala:** Semua data dalam satu kolom

**Solusi:** File menggunakan delimiter `;` (semicolon). Di Excel, pilih "Text to Columns" atau ubah delimiter di `fputcsv()` ke `,`

### ❌ Karakter Indonesia tidak tampil

**Gejala:** Karakter seperti "é" menjadi kotak

**Solusi:** Sudah ditangani dengan BOM (`\xEF\xBB\xBF`) di awal file CSV

### ❌ Print tidak berfungsi

**Gejala:** Tombol print tidak merespons

**Solusi:** Pastikan popup tidak di-block. Gunakan `target="_blank"` untuk membuka di tab baru.

---

## ✅ Checklist Implementasi

- [ ] Buat file `api/export-api.php`
- [ ] Update `pages/reports.php` dengan tombol export
- [ ] Test export CSV monthly_summary
- [ ] Test export CSV transactions
- [ ] Test export CSV customers
- [ ] Test export CSV service_report
- [ ] Test print view monthly_summary
- [ ] Test print view transactions
- [ ] Verifikasi file bisa dibuka di Excel
- [ ] Verifikasi print/save as PDF berfungsi

---

## 🚀 Upgrade ke PhpSpreadsheet (Opsional)

Jika ingin fitur Excel yang lebih advanced (styling, multiple sheets, charts), instal PhpSpreadsheet:

```bash
composer require phpoffice/phpspreadsheet
```

Lalu gunakan kode berikut untuk export:

```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$sheet->fromArray($headers, null, 'A1');

// Set data
$sheet->fromArray($rows, null, 'A2');

// Style header row
$sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')
    ->getFont()->setBold(true);

// Output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
```

---

**Selamat Coding! 📤**
