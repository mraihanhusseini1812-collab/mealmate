<?php
$currentPreset = $periodInfo['preset'];
$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];

$baseParams = [
    'page' => 'admin-reports',
    'period' => $currentPreset,
    'start_date' => $currentPreset === 'custom' ? $startDate : null,
    'end_date' => $currentPreset === 'custom' ? $endDate : null,
    'status' => $statusFilter !== '' ? $statusFilter : null,
    'package_id' => $packageIdFilter > 0 ? $packageIdFilter : null,
    'q' => $searchQuery !== '' ? $searchQuery : null,
];

$exportCsvUrl = 'index.php?' . http_build_query(array_merge($baseParams, ['export' => 'csv']));
$exportExcelUrl = 'index.php?' . http_build_query(array_merge($baseParams, ['export' => 'excel']));

$startRow = $totalTransactions > 0 ? (($currentPageNum - 1) * $perPage) + 1 : 0;
$endRow = min($totalTransactions, $currentPageNum * $perPage);

$statusBadgeMap = [
    'Selesai' => ['class' => 'report-badge-success', 'label' => 'Berhasil'],
    'Aktif' => ['class' => 'report-badge-success', 'label' => 'Berhasil'],
    'Menunggu Verifikasi' => ['class' => 'report-badge-warning', 'label' => 'Menunggu Verifikasi'],
    'Belum Dibayar' => ['class' => 'report-badge-unpaid', 'label' => 'Belum Dibayar'],
    'Dibatalkan' => ['class' => 'report-badge-danger', 'label' => 'Dibatalkan'],
    'Ditolak' => ['class' => 'report-badge-danger', 'label' => 'Ditolak'],
];
?>
<div class="d-flex flex-column flex-xl-row align-items-xl-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Laporan Transaksi</h1>
        <p class="admin-page-subtitle mb-0">Riwayat pesanan dan pembayaran pelanggan</p>
    </div>

    <div class="d-flex flex-wrap align-items-end gap-2">
        <form action="index.php" method="GET" class="d-flex flex-wrap align-items-end gap-2">
            <input type="hidden" name="page" value="admin-reports">
            <?php if ($statusFilter !== ''): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <?php endif; ?>
            <?php if ($packageIdFilter > 0): ?>
                <input type="hidden" name="package_id" value="<?= (int) $packageIdFilter ?>">
            <?php endif; ?>
            <?php if ($searchQuery !== ''): ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
            <?php endif; ?>

            <div class="<?= $currentPreset === 'custom' ? '' : 'd-none' ?> d-flex flex-wrap align-items-end gap-2" id="reportCustomDateGroup">
                <div>
                    <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control form-control-sm admin-period-date">
                </div>
                <div>
                    <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control form-control-sm admin-period-date">
                </div>
                <button type="submit" class="btn btn-sm admin-btn-primary px-3">Terapkan</button>
            </div>

            <div>
                <select name="period" class="form-select form-select-sm admin-period-select" data-custom-target="reportCustomDateGroup">
                    <option value="12w" <?= $currentPreset === '12w' ? 'selected' : '' ?>>12 minggu terakhir</option>
                    <option value="4w" <?= $currentPreset === '4w' ? 'selected' : '' ?>>4 minggu terakhir</option>
                    <option value="month" <?= $currentPreset === 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                    <option value="all" <?= $currentPreset === 'all' ? 'selected' : '' ?>>Semua Periode</option>
                    <option value="custom" <?= $currentPreset === 'custom' ? 'selected' : '' ?>>Rentang Kustom</option>
                </select>
            </div>
        </form>

        <div class="dropdown">
            <button class="btn btn-sm admin-export-btn dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
                <span>Export to Excel/CSV</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?= htmlspecialchars($exportExcelUrl) ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i>
                        <span>Export Excel (.xlsx)</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?= htmlspecialchars($exportCsvUrl) ?>">
                        <i class="bi bi-filetype-csv text-success"></i>
                        <span>Export CSV (.csv)</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Total Transaksi</div>
            <div class="admin-kpi-value"><?= (int) $kpi['total_transactions'] ?></div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Berhasil</div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="admin-kpi-value"><?= (int) $kpi['successful_count'] ?></span>
                <span class="text-muted small fw-medium">&middot; <?= number_format((float) $kpi['success_rate'], 1, ',', '.') ?>%</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Total Pendapatan</div>
            <div class="admin-kpi-value"><?= formatRupiah((int) $kpi['total_revenue']) ?></div>
        </div>
    </div>
</div>

<div class="admin-card p-3 p-lg-4">
    <form action="index.php" method="GET" class="row g-2 align-items-center mb-4">
        <input type="hidden" name="page" value="admin-reports">
        <input type="hidden" name="period" value="<?= htmlspecialchars($currentPreset) ?>">
        <?php if ($currentPreset === 'custom'): ?>
            <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
            <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
        <?php endif; ?>

        <div class="col-12 col-md-5 col-lg-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($searchQuery) ?>"
                    class="form-control border-start-0 ps-0"
                    placeholder="Cari nama pelanggan atau Order ID"
                >
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Berhasil" <?= $statusFilter === 'Berhasil' ? 'selected' : '' ?>>Berhasil (Aktif/Selesai)</option>
                <option value="Aktif" <?= $statusFilter === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Selesai" <?= $statusFilter === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="Menunggu Verifikasi" <?= $statusFilter === 'Menunggu Verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                <option value="Belum Dibayar" <?= $statusFilter === 'Belum Dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
                <option value="Dibatalkan" <?= $statusFilter === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                <option value="Ditolak" <?= $statusFilter === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
            <select name="package_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="0">Semua Paket</option>
                <?php foreach ($allPackages as $pkg): ?>
                    <option value="<?= (int) $pkg['package_id'] ?>" <?= $packageIdFilter === (int) $pkg['package_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pkg['package_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm admin-btn-primary px-3">Filter</button>
            <?php if ($statusFilter !== '' || $packageIdFilter > 0 || $searchQuery !== ''): ?>
                <a href="index.php?page=admin-reports&period=<?= urlencode($currentPreset) ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($transactions)): ?>
        <div class="text-center py-5">
            <div class="admin-empty-icon mx-auto mb-3">
                <i class="bi bi-receipt"></i>
            </div>
            <h5 class="fw-bold mb-1">Tidak ada data pada periode ini</h5>
            <p class="text-muted small mb-0">
                Belum ada riwayat transaksi yang sesuai dengan rentang tanggal atau filter yang dipilih.
            </p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-orders align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Order ID</th>
                        <th>Nama Pelanggan</th>
                        <th style="width: 150px;">Tanggal</th>
                        <th>Paket</th>
                        <th style="width: 160px;">Total Harga</th>
                        <th style="width: 180px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trx):
                        $ordCode = '#ORD-' . str_pad((string) $trx['order_id'], 3, '0', STR_PAD_LEFT);
                        $badgeInfo = $statusBadgeMap[$trx['status']] ?? ['class' => 'report-badge-unpaid', 'label' => $trx['status']];
                        $trxDate = formatDateIndo(date('Y-m-d', strtotime((string) $trx['created_at'])), true);
                    ?>
                        <tr>
                            <td class="admin-ord-id-cell fw-bold">
                                <a href="index.php?page=admin-order-detail&id=<?= (int) $trx['order_id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($ordCode) ?>
                                </a>
                            </td>
                            <td class="admin-ord-cust-cell" data-label="Nama Pelanggan"><?= htmlspecialchars($trx['customer_name']) ?></td>
                            <td class="admin-ord-date-cell" data-label="Tanggal"><?= htmlspecialchars($trxDate) ?></td>
                            <td class="admin-ord-pkg-cell" data-label="Paket"><?= htmlspecialchars($trx['package_name']) ?></td>
                            <td class="admin-ord-price-cell fw-semibold" data-label="Total Harga"><?= formatRupiah((int) $trx['total_price']) ?></td>
                            <td class="admin-ord-status-cell">
                                <span class="report-status-badge <?= htmlspecialchars($badgeInfo['class']) ?>">
                                    <?= htmlspecialchars($badgeInfo['label']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-3 border-top">
            <span class="text-muted small">
                Menampilkan <?= $startRow ?>–<?= $endRow ?> dari <?= $totalTransactions ?> transaksi
            </span>

            <?php if ($totalPages > 1): ?>
                <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                    <?php
                    $prevQuery = http_build_query(array_merge($baseParams, ['p' => max(1, $currentPageNum - 1)]));
                    $nextQuery = http_build_query(array_merge($baseParams, ['p' => min($totalPages, $currentPageNum + 1)]));
                    ?>
                    <a href="index.php?<?= htmlspecialchars($prevQuery) ?>" class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($p = 1; $p <= $totalPages; $p++):
                        $pQuery = http_build_query(array_merge($baseParams, ['p' => $p]));
                    ?>
                        <a href="index.php?<?= htmlspecialchars($pQuery) ?>" class="btn btn-sm admin-page-btn <?= $p === $currentPageNum ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                    <a href="index.php?<?= htmlspecialchars($nextQuery) ?>" class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
