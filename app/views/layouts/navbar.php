<?php
$page = $_GET['page'] ?? 'home';
$loggedInUser = currentUser();
?>

<nav class="navbar navbar-expand-lg container-fluid py-3 px-4 px-lg-5 align-items-center">
    <!-- logo -->
    <a href="index.php?page=home" class="logo d-flex gap-2 align-items-center text-decoration-none text-dark">
        <img src="./assets/img/logo.png" alt="MealMate Logo">
        <h2 class="mb-0">Mealmate</h2>
    </a>

    <!-- hamburger -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
    data-bs-toggle="offcanvas" data-bs-target="#navMenu"
    aria-controls="navMenu" aria-label="Toggle navigation">
        <i class="bi bi-list fs-2"></i>
    </button>

    <!-- menu -->
    <div class="offcanvas offcanvas-end offcanvas-lg" tabindex="-1" id="navMenu" aria-labelledby="navMenuLabel">
        <!-- header offcanvas (tampil di mobile dan tablet) -->
        <div class="offcanvas-header align-items-center">
            <?php if ($loggedInUser): ?>
                <div class="dropdown" id="navMenuLabel">
                    <button class="btn user-greeting-btn dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent text-start p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar-sm d-inline-flex align-items-center justify-content-center rounded-circle">
                            <i class="bi bi-person"></i>
                        </span>
                        <div class="d-flex flex-column lh-sm">
                            <span class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">SELAMAT DATANG</span>
                            <span class="fw-semibold text-dark" style="font-size: 13px;">Halo, <?= htmlspecialchars(explode(' ', $loggedInUser['name'])[0]) ?></span>
                        </div>
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-3 mt-2">
                        <?php if ($loggedInUser['role'] === 'Admin'): ?>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?page=admin-dashboard">
                                    <i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard Admin
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?page=profile">
                                    <i class="bi bi-person-circle me-2 text-success"></i>Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?page=change-password">
                                    <i class="bi bi-key me-2 text-success d-inline-block" style="transform: rotate(90deg)"></i>Ubah Password
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?page=orders">
                                    <i class="bi bi-bag-check me-2 text-success"></i>Riwayat Pesanan
                                </a>
                            </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="index.php?page=logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="logo d-flex gap-2 align-items-center" id="navMenuLabel">
                    <img src="./assets/img/logo.png" alt="MealMate Logo" width="40" height="40" loading="lazy">
                    <h2 class="mb-0 fs-4">Mealmate</h2>
                </div>
            <?php endif; ?>
            <button type="button" class="btn-close shadow-none ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#navMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column flex-lg-row align-items-lg-center w-100">
            <ul class="navbar-nav mx-lg-auto gap-3 gap-lg-5 nav-link">
                <li class="nav-item">
                    <a href="index.php?page=home" class="text-decoration-none fw-semibold <?= ($page === 'home') ? 'active' : 'non-active'; ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=menu" class="text-decoration-none fw-semibold <?= ($page === 'menu') ? 'active' : 'non-active'; ?>">Menu</a>
                </li>
                <?php if ($loggedInUser && $loggedInUser['role'] === 'Pelanggan'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=orders" class="text-decoration-none fw-semibold <?= in_array($page, ['orders', 'order-detail', 'payment', 'review'], true) ? 'active' : 'non-active'; ?>">Pesanan Saya</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="index.php?page=about" class="text-decoration-none fw-semibold <?= ($page === 'about') ? 'active' : 'non-active'; ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=faq" class="text-decoration-none fw-semibold <?= ($page === 'faq') ? 'active' : 'non-active'; ?>">FAQ</a>
                </li>
            </ul>

            <div class="navbar-button <?= $loggedInUser ? 'd-none d-lg-flex' : 'd-flex navbar-button-guest' ?> flex-column flex-lg-row gap-2 gap-lg-4 align-items-stretch align-items-lg-center mt-auto mt-lg-0">
                <?php if ($loggedInUser): ?>
                    <div class="dropdown">
                        <button class="btn user-greeting-btn dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent text-start p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar-sm d-inline-flex align-items-center justify-content-center rounded-circle">
                                <i class="bi bi-person"></i>
                            </span>
                            <div class="d-flex flex-column lh-sm">
                                <span class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">SELAMAT DATANG</span>
                                <span class="fw-semibold text-dark" style="font-size: 13px;">Halo, <?= htmlspecialchars(explode(' ', $loggedInUser['name'])[0]) ?></span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <?php if ($loggedInUser['role'] === 'Admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=admin-dashboard">
                                        <i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard Admin
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=profile">
                                        <i class="bi bi-person-circle me-2 text-success"></i>Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=change-password">
                                        <i class="bi bi-key me-2 text-success d-inline-block" style="transform: rotate(90deg)"></i>Ubah Password
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="index.php?page=orders">
                                        <i class="bi bi-bag-check me-2 text-success"></i>Riwayat Pesanan
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="index.php?page=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="login-nav"><i class="bi bi-person d-none d-lg-inline-block"></i> Masuk</a>
                    <p class="d-lg-none text-center text-muted small mb-0 mt-1">
                        Belum punya akun? <a href="index.php?page=signup" class="fw-semibold text-decoration-none" style="color: #13523C;">Daftar</a>
                    </p>
                    <a href="index.php?page=menu#packages-section" class="order-nav btn">Mulai Pesan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
