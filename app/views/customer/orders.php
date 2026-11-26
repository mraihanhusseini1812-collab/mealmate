<main class="orders-page py-4 py-lg-5">
    <div class="container orders-container px-3">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <h1 class="orders-title mb-1">Riwayat Pesanan</h1>
                <p class="orders-subtitle mb-0">Berikut adalah daftar pesanan catering Anda</p>
            </div>

            <form action="index.php" method="get" class="orders-search position-relative">
                <input type="hidden" name="page" value="orders">
                <i class="bi bi-search orders-search-icon"></i>
                <input
                    type="search"
                    name="q"
                    maxlength="100"
                    value="<?= escape($search) ?>"
                    class="form-control orders-search-input"
                    placeholder="Cari berdasarkan Order ID atau nama paket..."
                    aria-label="Cari pesanan"
                >
            </form>
        </div>

        <?php if (empty($orders)): ?>
            <div class="orders-empty text-center py-5">
                <?php if ($search !== ''): ?>
                    <i class="bi bi-search d-block mb-3"></i>
                    <h2 class="orders-empty-title">Pesanan tidak ditemukan</h2>
                    <p class="text-muted mb-4">Tidak ada pesanan yang cocok dengan "<?= escape($search) ?>".</p>
                    <a href="index.php?page=orders" class="btn orders-empty-btn">Reset Pencarian</a>
                <?php else: ?>
                    <i class="bi bi-bag d-block mb-3"></i>
                    <h2 class="orders-empty-title px-3">Belum ada pesanan</h2>
                    <p class="text-muted mb-4 px-4 px-md-0">Anda belum memiliki riwayat pesanan. Yuk, pilih paket langganan pertama Anda!</p>
                    <a href="index.php?page=menu#packages-section" class="btn orders-empty-btn">Lihat Paket Langganan</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="orders-list d-flex flex-column gap-3">
                <?php foreach ($orders as $ord): ?>
                    <article class="order-card">
                        <div class="order-card-img">
                            <?php if ($ord['image_url'] !== null): ?>
                                <img src="<?= escape($ord['image_url']) ?>" alt="<?= escape($ord['package_name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="order-card-img-fallback"><i class="bi bi-box-seam"></i></div>
                            <?php endif; ?>
                        </div>

                        <div class="order-card-col order-card-id">
                            <span class="order-card-label">Order ID</span>
                            <strong class="order-card-code"><?= escape($ord['order_code']) ?></strong>
                        </div>

                        <div class="order-card-col order-card-pkg">
                            <span class="order-card-label">Nama Paket</span>
                            <div class="order-card-pkgrow">
                                <strong class="order-card-pkgname" title="<?= escape($ord['package_name']) ?>"><?= escape($ord['package_name']) ?></strong>
                                <span class="order-card-badge"><?= (int) $ord['duration_days'] ?> hari</span>
                            </div>
                        </div>

                        <div class="order-card-col order-card-period">
                            <span class="order-card-label">Periode</span>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-calendar3"></i>
                                <span class="order-card-period-text">
                                    <?= escape($ord['period_text']) ?>
                                    <span class="d-lg-block d-inline">(<?= (int) $ord['calendar_days'] ?> hari)</span>
                                </span>
                            </div>
                        </div>

                        <div class="order-card-status">
                            <span class="order-status-pill <?= escape($ord['status_class']) ?>">
                                <span class="status-dot"></span><?= escape($ord['status']) ?>
                            </span>
                        </div>

                        <div class="order-card-action">
                            <a href="<?= escape($ord['detail_url']) ?>" class="btn order-detail-link">
                                <span>Lihat Detail</span>
                                <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 mt-4">
                <span class="orders-count text-center text-sm-start"><?= escape($countText) ?></span>

                <?php if ($pagination['show']): ?>
                    <nav class="d-flex align-items-center flex-wrap justify-content-center gap-1" aria-label="Paginasi pesanan">
                        <a href="<?= escape($pagination['prev_url']) ?>"
                           class="btn orders-page-btn <?= $pagination['has_prev'] ? '' : 'disabled' ?>"
                           aria-label="Halaman Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <?php foreach ($pagination['pages'] as $pg): ?>
                            <a href="<?= escape($pg['url']) ?>"
                               class="btn orders-page-btn <?= $pg['active'] ? 'active' : '' ?>"
                               <?= $pg['active'] ? 'aria-current="page"' : '' ?>>
                                <?= (int) $pg['number'] ?>
                            </a>
                        <?php endforeach; ?>
                        <a href="<?= escape($pagination['next_url']) ?>"
                           class="btn orders-page-btn <?= $pagination['has_next'] ? '' : 'disabled' ?>"
                           aria-label="Halaman Selanjutnya">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>