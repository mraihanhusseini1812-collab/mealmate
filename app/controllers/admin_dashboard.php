<?php
require_once __DIR__ . '/../models/ReportModel.php';

requireRole('Admin');

$preset = trim((string) ($_GET['period'] ?? '12w'));
$customStart = isset($_GET['start_date']) ? trim((string) $_GET['start_date']) : null;
$customEnd = isset($_GET['end_date']) ? trim((string) $_GET['end_date']) : null;

if ($customStart !== null && $customStart !== '' && $customEnd !== null && $customEnd !== '' && !isset($_GET['period'])) {
    $preset = 'custom';
}

$periodInfo = resolveReportPeriod($preset, $customStart, $customEnd);

if ($periodInfo['error'] !== null) {
    setFlash('danger', $periodInfo['error']);
    redirect('index.php?page=admin-dashboard');
}

$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];

$summary = getDashboardSummary($startDate, $endDate);
$salesTrend = getWeeklySalesTrend($startDate, $endDate);
$reviewsSummary = getLatestReviewsSummary(4);
$pendingOrders = getPendingVerificationOrders(5);

$daysIndo = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];
$todayLabel = ($daysIndo[(int) date('N')] ?? '') . ', ' . formatDateIndo(date('Y-m-d')) . '.';

render('admin/dashboard', [
    'title' => 'Dashboard Admin - MealMate',
    'todayLabel' => $todayLabel,
    'periodInfo' => $periodInfo,
    'summary' => $summary,
    'salesTrend' => $salesTrend,
    'reviewsSummary' => $reviewsSummary,
    'pendingOrders' => $pendingOrders,
], 'admin');
