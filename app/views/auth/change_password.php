<div class="auth-centered-wrapper d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="auth-card text-center p-4 p-sm-5">
        <?php if ($passwordChanged): ?>
            <svg class="success-check d-block mx-auto" viewBox="0 0 52 52" role="img" aria-label="Berhasil">
                <circle class="success-check-circle" cx="26" cy="26" r="24" fill="none"/>
                <path class="success-check-mark" d="M14 27l8 8 16-17" fill="none"/>
            </svg>
            <h2 class="auth-card-title fw-bold mt-3 mb-2">Password Berhasil Diubah</h2>
            <p class="text-muted small mb-4">
                Password Anda sudah diperbarui.<br>Gunakan password baru saat login berikutnya.
            </p>
            <a href="index.php?page=profile" class="btn auth-submit-btn w-100 py-3 fw-semibold">
                Kembali ke Profil
            </a>
        <?php else: ?>
            <a href="index.php?page=home" class="d-inline-flex flex-column align-items-center text-decoration-none text-dark mb-2">
                <img src="./assets/img/logo.png" alt="MealMate Logo" width="76" height="76" class="mb-2">
                <span class="auth-brand-text fs-2">Meal Mate</span>
            </a>
            
            <h2 class="auth-card-title fw-bold mb-2">Ubah Password</h2>
            <p class="text-muted small mb-4">
                Masukkan password lama Anda, lalu tentukan <br> password baru.
            </p>

            <?= renderFlash($flash, 'text-start small') ?>

            <form action="index.php?page=change-password" method="POST" class="text-start" novalidate>
                <div class="mb-3">
                    <div class="auth-input-group position-relative">
                        <i class="bi bi-lock auth-input-icon"></i>
                        <input
                            type="password"
                            name="old_password"
                            id="old-password"
                            autocomplete="current-password"
                            class="form-control auth-input pe-5 <?= !empty($errors['old_password']) ? 'is-invalid' : '' ?>"
                            placeholder="Password Lama"
                            aria-label="Password Lama"
                            required
                        >
                        <button type="button" class="btn auth-password-toggle" onclick="togglePasswordVisibility('old-password', this)" aria-label="Tampilkan Password Lama">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <?php if (!empty($errors['old_password'])): ?>
                            <div class="invalid-feedback"><?= escape($errors['old_password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="auth-input-group position-relative">
                        <i class="bi bi-lock auth-input-icon"></i>
                        <input
                            type="password"
                            name="new_password"
                            id="new-password"
                            autocomplete="new-password"
                            class="form-control auth-input pe-5 <?= !empty($errors['new_password']) ? 'is-invalid' : '' ?>"
                            placeholder="Password Baru (8-72 karakter)"
                            aria-label="Password Baru"
                            required
                        >
                        <button type="button" class="btn auth-password-toggle" onclick="togglePasswordVisibility('new-password', this)" aria-label="Tampilkan Password Baru">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <?php if (!empty($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?= escape($errors['new_password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="auth-input-group position-relative">
                        <i class="bi bi-lock auth-input-icon"></i>
                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm-password"
                            autocomplete="new-password"
                            class="form-control auth-input pe-5 <?= !empty($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            placeholder="Konfirmasi Password Baru"
                            aria-label="Konfirmasi Password Baru"
                            required
                        >
                        <button type="button" class="btn auth-password-toggle" onclick="togglePasswordVisibility('confirm-password', this)" aria-label="Tampilkan Konfirmasi Password">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?= escape($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <a href="index.php?page=profile" class="btn btn-light w-50 py-3 fw-semibold">Batal</a>
                    <button type="submit" class="btn auth-submit-btn w-50 py-3 fw-semibold">Simpan</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>