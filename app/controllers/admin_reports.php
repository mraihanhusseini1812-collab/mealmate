<?php
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/PackageModel.php';

requireRole('Admin');

$preset = trim((string) ($_GET['period'] ?? '12w'));
$customStart = isset($_GET['start_date']) ? trim((string) $_GET['start_date']) : null;
$customEnd = isset($_GET['end_date']) ? trim((string) $_GET['end_date']) : null;
$statusFilter = trim((string) ($_GET['status'] ?? ''));
$packageIdFilter = (int) ($_GET['package_id'] ?? 0);
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$exportFormat = strtolower(trim((string) ($_GET['export'] ?? '')));

$periodInfo = resolveReportPeriod($preset, $customStart, $customEnd);

if ($periodInfo['error'] !== null) {
    setFlash('danger', $periodInfo['error']);
    redirect('index.php?page=admin-reports');
}

$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];

if (in_array($exportFormat, ['csv', 'excel'], true)) {
    $allMatchingTransactions = getReportTransactions(
        $startDate,
        $endDate,
        $statusFilter,
        $packageIdFilter,
        $searchQuery,
        null,
        null
    );

    if (empty($allMatchingTransactions)) {
        setFlash('danger', 'Tidak ada data penjualan pada periode yang dipilih untuk diekspor.');
        $backQuery = http_build_query([
            'page' => 'admin-reports',
            'period' => $periodInfo['preset'],
            'start_date' => $periodInfo['preset'] === 'custom' ? $startDate : null,
            'end_date' => $periodInfo['preset'] === 'custom' ? $endDate : null,
            'status' => $statusFilter !== '' ? $statusFilter : null,
            'package_id' => $packageIdFilter > 0 ? $packageIdFilter : null,
            'q' => $searchQuery !== '' ? $searchQuery : null,
        ]);
        redirect('index.php?' . $backQuery);
    }

    $dateSlug = $startDate . '_sd_' . $endDate;
    if ($exportFormat === 'excel') {
        streamTransactionsXlsx($allMatchingTransactions, "laporan_penjualan_mealmate_{$dateSlug}.xlsx");
    }
    streamTransactionsCsv($allMatchingTransactions, "laporan_penjualan_mealmate_{$dateSlug}.csv");
}

$kpi = getReportKpiSummary($startDate, $endDate, $statusFilter, $packageIdFilter, $searchQuery);

$perPage = 10;
$totalTransactions = $kpi['total_transactions'];
$totalPages = max(1, (int) ceil($totalTransactions / $perPage));
$currentPageNum = max(1, min($totalPages, (int) ($_GET['p'] ?? 1)));
$offset = ($currentPageNum - 1) * $perPage;

$transactions = getReportTransactions(
    $startDate,
    $endDate,
    $statusFilter,
    $packageIdFilter,
    $searchQuery,
    $perPage,
    $offset
);

$allPackages = getAllPackages();

render('admin/reports', [
    'title' => 'Laporan Transaksi - MealMate',
    'periodInfo' => $periodInfo,
    'statusFilter' => $statusFilter,
    'packageIdFilter' => $packageIdFilter,
    'searchQuery' => $searchQuery,
    'kpi' => $kpi,
    'transactions' => $transactions,
    'allPackages' => $allPackages,
    'totalTransactions' => $totalTransactions,
    'totalPages' => $totalPages,
    'currentPageNum' => $currentPageNum,
    'perPage' => $perPage,
], 'admin');
