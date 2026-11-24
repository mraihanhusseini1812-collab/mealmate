<?php if (empty($hideFooter)): ?>
<footer>
    <div class="container">
        <div class="footer-container d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start gap-4 gap-md-0">

            <div class="logo-slogan-container text-center">
                <img src="./assets/img/logo.png" alt="mealmate-logo" class="footer-logo mb-2" loading="lazy">
                <p class="footer-slogan">Makan Enak, Hidup Lebih Mudah</p>
            </div>

            <div class="menu-footer-container d-grid d-md-flex justify-content-md-start">
                <div class="menu-footer">
                    <h5 class="mb-3">Menu</h5>
                    <p><a href="index.php?page=menu" class="text-decoration-none">Menu Hari Ini</a></p>
                    <p><a href="index.php?page=menu" class="text-decoration-none">Menu Mingguan</a></p>
                    <p><a href="index.php?page=menu#packages-section" class="text-decoration-none">Paket Langganan</a></p>
                </div>

                <div class="about-menu-footer">
                    <h5 class="mb-3">Tentang Kami</h5>
                    <p><a href="index.php?page=about" class="text-decoration-none">Tentang MealMate</a></p>
                    <p><a href="index.php?page=home#home-order-steps" class="text-decoration-none">Cara Pemesanan</a></p>
                </div>

                <div class="help-menu-footer">
                    <h5 class="mb-3">Bantuan</h5>
                    <p><a href="index.php?page=faq" class="text-decoration-none">FAQ</a></p>
                    <p><a href="index.php?page=about" class="text-decoration-none">Kontak Kami</a></p>
                </div>
            </div>

            <div class="social-media-container text-center">
                <h5 class="mb-3">Ikuti Kami</h5>
                <div class="social-icons justify-content-center">
                    <i class="bi bi-instagram"></i>
                    <i class="bi bi-facebook"></i>
                    <i class="bi bi-youtube"></i>
                </div>
            </div>
        </div>

        <div class="copyright-section d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center">
            <div>
                <i class="bi bi-c-circle"></i> <?= date('Y') ?> MealMate. Semua hak dilindungi.
            </div>
            <div class="copyright-slogan d-flex align-items-center gap-2">
                <i class="bi bi-leaf"></i>
                <span>Makan Sehat, Hidup Lebih Semangat</span>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/menu-toggle.js"></script>
    <?php if (($_GET['page'] ?? '') === 'checkout'): ?>
        <script src="./assets/js/checkout.js"></script>
    <?php endif; ?>

    <?php if (($_GET['page'] ?? '') === 'profile'): ?>
        <script src="./assets/js/flash-toast.js"></script>
        <script src="./assets/js/profile.js"></script>
    <?php endif; ?>
    <?php if (($_GET['page'] ?? '') === 'payment'): ?>
        <script src="./assets/js/payment.js"></script>
    <?php endif; ?>
</body>
</html>
