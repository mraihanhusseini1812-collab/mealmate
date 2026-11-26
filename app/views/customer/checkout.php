<?php
$packageImg = !empty($package['image']) ? './assets/img/' . $package['image'] : './assets/img/home-package-card-placeholder.webp';
$formattedApplicableDays = str_replace('-', ' - ', $package['applicable_days']);
$addressLength = mb_strlen((string) $selectedAddress);
$slotOptions = [
    'Pagi' => 'Pagi (06.00 - 09.00 WIB)',
    'Siang' => 'Siang (11.00 - 13.00 WIB)',
    'Malam' => 'Malam (17.00 - 20.00 WIB)',
];
?>
<main class="checkout-page py-3">
    <div class="container">
        <div class="checkout-stepper d-flex align-items-center justify-content-center mb-3">
            <div class="step-item completed">
                <div class="step-circle"><i class="bi bi-check-lg"></i></div>
                <span class="step-label">Pilih Paket</span>
            </div>
            <div class="step-line completed"></div>
            <div class="step-item active">
                <div class="step-circle">2</div>
                <span class="step-label">Checkout</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-circle">3</div>
                <span class="step-label">Pembayaran</span>
            </div>
        </div>

        <form action="index.php?page=checkout&package_id=<?= (int) $package['package_id'] ?>" method="POST" id="checkoutForm" novalidate>
            <input type="hidden" name="package_id" value="<?= (int) $package['package_id'] ?>">

            <div class="row g-4 align-items-start">
                <div class="col-12 col-lg-7">
                    <div class="checkout-card p-3 p-md-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="checkout-header-icon">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <h2 class="checkout-section-title mb-1">Data Pengantaran</h2>
                                <p class="checkout-section-subtitle mb-0">
                                    Lengkapi informasi di bawah ini untuk mengatur jadwal dan alamat pengantaran pesanan Anda.
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="checkoutStartDate" class="checkout-field-label d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-event text-success"></i>
                                <span>Tanggal Mulai <span class="text-danger">*</span></span>
                            </label>
                            <p class="checkout-field-hint">
                                Pilih tanggal pertama Anda ingin menerima pesanan<?= $package['applicable_days'] === 'Senin-Jumat' ? ' (Hari kerja Senin - Jumat)' : '' ?>.
                            </p>
                            <input
                                type="date"
                                id="checkoutStartDate"
                                name="start_date"
                                class="form-control checkout-input <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($selectedStartDate) ?>"
                                min="<?= htmlspecialchars($minStartDate) ?>"
                                data-duration-days="<?= (int) $package['duration_days'] ?>"
                                data-applicable-days="<?= htmlspecialchars($package['applicable_days']) ?>"
                                required
                            >
                            <?php if (isset($errors['start_date'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['start_date']) ?></div>
                            <?php endif; ?>
                            <div id="weekendWarningText" class="text-danger small mt-1 d-none">
                                Paket Senin - Jumat tidak berlaku di hari Sabtu/Minggu. Silakan pilih hari Senin - Jumat.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="checkoutSlot" class="checkout-field-label d-flex align-items-center gap-2">
                                <i class="bi bi-clock text-success"></i>
                                <span>Slot Antar <span class="text-danger">*</span></span>
                            </label>
                            <p class="checkout-field-hint">Pilih waktu pengantaran harian</p>
                            <select
                                id="checkoutSlot"
                                name="slot"
                                class="form-select checkout-input <?= isset($errors['slot']) ? 'is-invalid' : '' ?>"
                                required
                            >
                                <?php foreach ($slotOptions as $slotVal => $slotLabel): ?>
                                    <option value="<?= htmlspecialchars($slotVal) ?>" <?= $selectedSlot === $slotVal ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($slotLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['slot'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['slot']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="checkoutAddress" class="checkout-field-label d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt text-success"></i>
                                <span>Alamat Pengantaran <span class="text-danger">*</span></span>
                            </label>
                            <p class="checkout-field-hint">Pastikan alamat lengkap beserta patokan atau nomor kamar/rumah</p>
                            <div class="position-relative">
                                <textarea
                                    id="checkoutAddress"
                                    name="delivery_address"
                                    rows="3"
                                    maxlength="500"
                                    class="form-control checkout-input checkout-textarea <?= isset($errors['delivery_address']) ? 'is-invalid' : '' ?>"
                                    placeholder="Contoh: Jl. Melati No. 12, RT 03/RW 04, Kec. Coblong, Kota Bandung (Pagar hitam)"
                                    required
                                ><?= htmlspecialchars($selectedAddress) ?></textarea>
                                <span class="checkout-char-counter" id="addressCharCounter"><?= (int) $addressLength ?>/500</span>
                            </div>
                            <?php if (isset($errors['delivery_address'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['delivery_address']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-0">
                            <label for="checkoutNotes" class="checkout-field-label d-flex align-items-center gap-2">
                                <i class="bi bi-journal-text text-success"></i>
                                <span>Catatan Tambahan <span class="text-muted fw-normal">(Opsional)</span></span>
                            </label>
                            <p class="checkout-field-hint">Misalnya alergi makanan, tingkat kepedasan, atau instruksi titip satpam</p>
                            <textarea
                                id="checkoutNotes"
                                name="notes"
                                rows="1"
                                maxlength="500"
                                class="form-control checkout-input <?= isset($errors['notes']) ? 'is-invalid' : '' ?>"
                                placeholder="Contoh: Tidak pakai sambal, titip di resepsionis lobi"
                            ><?= htmlspecialchars($selectedNotes) ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="checkout-summary-card checkout-sticky-card">
                        <div class="checkout-summary-header px-4 py-3">
                            <h3 class="mb-0">Order Summary</h3>
                        </div>

                        <div class="checkout-summary-body p-3 p-md-4">
                            <div class="d-flex gap-3 align-items-start mb-3">
                                <img
                                    src="<?= htmlspecialchars($packageImg) ?>"
                                    alt="<?= htmlspecialchars($package['package_name']) ?>"
                                    class="checkout-pkg-thumb"
                                >
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                        <h4 class="checkout-pkg-title mb-0"><?= htmlspecialchars($package['package_name']) ?></h4>
                                        <span class="checkout-pkg-badge flex-shrink-0"><?= (int) $package['duration_days'] ?> Hari</span>
                                    </div>
                                    <p class="checkout-pkg-desc mb-2"><?= htmlspecialchars($package['description']) ?></p>
                                    <div class="checkout-pkg-meta d-flex flex-column gap-1">
                                        <div><i class="bi bi-calendar2-week me-2 text-success"></i>Hari Berlaku: <strong><?= htmlspecialchars($formattedApplicableDays) ?></strong></div>
                                        <div><i class="bi bi-heart me-2 text-success"></i>Cocok untuk diet &amp; gaya hidup sehat</div>
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-enddate-box p-3 mb-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-calendar-check text-success"></i>
                                    <span class="enddate-label">Tanggal Selesai</span>
                                </div>
                                <div class="enddate-value" id="calculatedEndDateDisplay">
                                    <?= htmlspecialchars(formatDateIndo($estimatedEndDate)) ?>
                                </div>
                            </div>

                            <div class="checkout-price-breakdown mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Subtotal (<?= htmlspecialchars($package['package_name']) ?>)</span>
                                    <span class="fw-semibold text-dark"><?= formatRupiah((int) $package['price']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                    <span class="text-muted">Ongkos Kirim</span>
                                    <span class="fw-semibold text-success">Gratis (Rp 0)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2">
                                    <span class="checkout-total-label">Total Pembayaran</span>
                                    <span class="checkout-total-value"><?= formatRupiah((int) $package['price']) ?></span>
                                </div>
                            </div>

                            <button type="submit" class="btn checkout-submit-btn w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-credit-card"></i>
                                <span>Bayar &amp; Unggah Bukti</span>
                            </button>

                            <a href="index.php?page=menu#packages-section" class="btn btn-link text-muted text-decoration-none w-100 mt-1 pb-0 small">
                                Kembali ke Pilihan Paket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
