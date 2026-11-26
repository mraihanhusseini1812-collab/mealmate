<main class="main-menu">
    <section class="container-fluid m-0 p-0" id="hero-menus-section">
        <div class="hero-menu row mx-0 align-items-center justify-content-center overflow-hidden">
            <div class="hero-menu-content d-flex flex-column ">
                <p class="fw-semibold">MENU CATERING HARIAN</p>
                <h1 class="fw-bold m-0">Pilihan Menu</h1>
                <h1 class="fw-bold">Sehat & Lezat</h1>
                <p>
                    Nikmati berbagai menu rumahan yang dibuat dengan
                    bahan berkualitas dan penuh cita rasa. Cocok untuk
                    menemani aktivitas harianmu!
                </p>
            </div>
        </div>
    </section>

    <section class="container-fluid py-5 d-flex justify-content-center" id="daily-menus-section">
        <div class="daily-menus align-items-center d-flex flex-column">
            <!-- dmc-1 -->
            <div class="daily-menus-content dmc-1 row d-flex m-0 mb-3 p-0" id="header-container">
                <div class="col-md d-flex flex-column align-items-md-start align-items-center mb-2 mb-md-0">
                    <h3 class="fw-semibold m-0 p-0 text-center text-md-left" id="header-title">Menu Hari ini</h3>
                    <p class="m-0 text-center text-md-left" id="header-subtitle">Menu yang tersedia untuk hari ini</p>
                </div>

                <div class="col-md d-flex justify-content-md-end justify-content-center align-items-center">
                    <span class="badge-date text-center">
                        <i class="bi bi-calendar-week pe-2"></i><span id="header-date"><?= htmlspecialchars($tanggalHariIni); ?></span>
                    </span>
                </div>
            </div>

            <!-- dmc-2 -->
            <div class="daily-menus-content dmc-2 row d-flex flex-md-row align-items-center justify-content-center p-2 mb-2">
                <div class="col d-flex justify-content-center">
                    <button type="button" class="btn menu-mode-btn active p-2" id="btn-mode-daily" onclick="switchMode('daily')">
                        <i class="bi bi-calendar-week pe-2"></i>Menu Hari ini
                    </button>
                </div>

                <div class="col d-none d-md-flex justify-content-center">
                    <button type="button" class="btn menu-mode-btn non-active p-2" id="btn-mode-weekly" onclick="switchMode('weekly')">
                        <i class="bi bi-calendar-week pe-2"></i>Menu Minggu ini
                    </button>
                </div>

                <!-- weekly mobile (dropdown) -->
                <div class="col d-flex d-md-none justify-content-center dropdown">
                    <button class="btn menu-mode-btn non-active p-2 dropdown-toggle" type="button" id="btn-mode-weekly-mobile" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <i class="bi bi-calendar-week pe-2"></i><span id="mobile-dropdown-label">Menu Minggu ini</span>
                    </button>
                    <ul class="dropdown-menu dm-center flex-column justify-content-center shadow border-0" aria-labelledby="btn-mode-weekly-mobile">
                        <?php 
                        $days = array_keys($weeklyMenus);
                        foreach ($days as $day): 
                        ?>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="selectWeeklyDay('<?= $day ?>', true)"><?= $day ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <div id="weekly-selector-container" class="weekly-selector-collapse m-0">
                <div class="d-none d-md-flex justify-content-center gap-2 flex-wrap">
                    <?php foreach ($days as $index => $day): ?>
                        <input type="radio" class="btn-check" name="weekly-day-radio" id="day-<?= $day ?>" value="<?= $day ?>" <?= $index === 0 ? 'checked' : '' ?> onchange="selectWeeklyDay(this.value, false)">
                        <label class="btn fw-medium btn-outline-success px-3 py-2 rounded-pill" for="day-<?= $day ?>"><?= $day ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- dmc-3 atau card di render lewat js -->
            <div class="daily-menus-content dmc-3 row gap-xl-5 gap-lg-4 gap-md-3 flex-nowrap overflow-auto m-0 mt-4 p-0" id="menu-cards-container">
                
            </div>
        </div>
    </section>

    <section class="container-fluid mb-5" id="packages-section">
        <div class="packages-wrapper">
            <div class="packages-header d-flex align-items-end justify-content-between gap-3 mb-4 mx-4">
                <div >
                    <h3 class="packages-title fw-semibold m-0 mb-2 text-md-start text-center ">
                        Pilih Paket Langganan
                    </h3>
                    <p class="packages-subtitle m-0 text-center text-md-start">
                        Fleksibel dan hemat. Pilih paket katering yang paling sesuai dengan gaya hidup dan tujuan kesehatanmu.
                    </p>
                </div>

                <div class="packages-nav d-none gap-2" id="packages-nav">
                    <button type="button" class="btn-package-nav" id="package-prev" aria-label="Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="btn-package-nav" id="package-next" aria-label="Berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <?php if (empty($packages)): ?>
                <p class="text-center text-muted py-5">Belum ada paket langganan yang tersedia.</p>
            <?php else: ?>
                <div class="package-track ms-md-4" id="package-track">
                    <?php foreach ($packages as $package): ?>
                        <div class="package-card">
                            <div class="img-placeholder">
                                <img src="./assets/img/<?= htmlspecialchars($package['image']) ?>"
                                    alt="<?= htmlspecialchars($package['package_name']) ?>" loading="lazy">
                            </div>
                            <div class="package-info">
                                <p><?= htmlspecialchars($package['package_name']) ?></p>

                                <div class="package-duration-slot d-flex gap-2">
                                    <p><?= (int) $package['duration_days'] ?> hari</p>
                                    <p>|</p>
                                    <p><?= htmlspecialchars(str_replace('-', ' - ', $package['applicable_days'])) ?></p>
                                </div>

                                <p><?= htmlspecialchars($package['description']) ?></p>

                                <div class="package-price">
                                    <p>Mulai dari</p>
                                    <p><?= formatRupiah((int) $package['price']) ?> <span>/ paket</span></p>
                                </div>

                                <a href="index.php?page=checkout&package_id=<?= (int) $package['package_id'] ?>" class="btn">
                                    Pilih Paket
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<!-- Mengirim variabel dari render ke JS -->
    <script>
        const dailyData = <?= json_encode($dailyMenus); ?>;
        const weeklyData = <?= json_encode($weeklyMenus); ?>;
        const todayFormatted = <?= json_encode($tanggalHariIni); ?>;
    </script>
</main>