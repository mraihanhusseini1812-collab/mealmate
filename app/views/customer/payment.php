<main class="payment-page payment-main py-3">
    <div class="container">
        <div class="payment-card mx-auto p-3 p-md-4 px-md-5">

            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <span class="payment-badge d-inline-flex align-items-center gap-1 mb-2">
                        <i class="bi bi-credit-card"></i>
                        <span>Pembayaran</span>
                    </span>
                    <h1 class="payment-title mb-1">Selesaikan Pembayaran</h1>
                    <p class="payment-subtitle mb-1">
                        Lakukan pembayaran sesuai detail di bawah ini,<br class="d-none d-sm-inline">
                        lalu unggah bukti pembayaran Anda.
                    </p>
                    <span class="payment-order-code"><?= escape($orderCode) ?> &middot; <?= escape($order['package_name']) ?></span>
                </div>
                <img src="./assets/svg/payment-illustration.svg" alt="" class="payment-illustration-img flex-shrink-0 d-none d-sm-block">
            </div>

            <div class="payment-total-box mb-3">
                <div class="payment-total-label">Total Pembayaran</div>
                <div class="payment-total-value"><?= formatRupiah((int) $order['total_price']) ?></div>
            </div>

            <section class="mb-3">
                <h2 class="payment-section-title mb-1">Rekening Tujuan</h2>
                <p class="payment-section-hint mb-2">Silakan transfer ke rekening berikut</p>

                <div class="payment-bank-card d-flex flex-column flex-md-row align-items-md-center gap-3">
                    <div class="payment-bank-logo d-flex align-items-center justify-content-center flex-shrink-0">
                        <?php if (!empty($bankLogoUrl)): ?>
                            <img src="<?= escape($bankLogoUrl) ?>" alt="<?= escape($bankAccount['bank']) ?>">
                        <?php else: ?>
                            <span class="payment-bank-logo-text"><?= escape($bankAccount['bank']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="payment-bank-details flex-grow-1">
                        <div>
                            <span class="payment-bank-label">Bank</span>
                            <span class="payment-bank-value"><?= escape($bankAccount['bank']) ?></span>
                        </div>
                        <div>
                            <span class="payment-bank-label">Nomor Rekening</span>
                            <span class="payment-bank-value"><?= escape($bankAccount['account_number']) ?></span>
                        </div>
                        <div>
                            <span class="payment-bank-label">Atas Nama</span>
                            <span class="payment-bank-value"><?= escape($bankAccount['account_name']) ?></span>
                        </div>
                        <div>
                            <span class="payment-bank-label">Cabang</span>
                            <span class="payment-bank-value"><?= escape($bankAccount['branch']) ?></span>
                        </div>
                    </div>

                    <button type="button" class="btn payment-copy-btn flex-shrink-0" data-copy="<?= escape($accountDigits) ?>">
                        <i class="bi bi-copy"></i>
                        <span class="payment-copy-label">Salin</span>
                    </button>
                </div>
            </section>

            <form id="paymentForm"
                  action="index.php?page=payment&order_id=<?= (int) $order['order_id'] ?>"
                  method="POST"
                  enctype="multipart/form-data"
                  novalidate>
                <input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>">

                <h2 class="payment-section-title mb-1">Upload Bukti Pembayaran</h2>
                <p class="payment-section-hint mb-2">
                    Unggah foto atau screenshot bukti transfer Anda. (JPG, PNG, maks. <?= escape((string) $maxUploadMb) ?> MB)
                </p>

                <div id="paymentDropzone"
                     class="payment-dropzone <?= !empty($errors['payment_proof']) ? 'has-error' : '' ?>"
                     data-max-bytes="<?= (int) $maxUploadBytes ?>">
                    <input type="file"
                           name="payment_proof"
                           id="paymentProof"
                           class="visually-hidden"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png">

                    <div id="paymentDropzoneIdle" class="text-center">
                        <i class="bi bi-cloud-arrow-up payment-dropzone-icon"></i>
                        <p class="payment-dropzone-title mb-1">Drag &amp; drop file di sini</p>
                        <p class="payment-dropzone-or mb-2">atau</p>
                        <label for="paymentProof" class="payment-choose-btn d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-file-earmark"></i>
                            <span>Choose File</span>
                        </label>
                    </div>

                    <div id="paymentFileInfo" class="payment-file-info d-none align-items-center gap-3">
                        <i class="bi bi-file-earmark-image payment-file-icon"></i>
                        <div class="min-w-0 flex-grow-1">
                            <div id="paymentFileName" class="payment-file-name text-truncate"></div>
                            <div id="paymentFileSize" class="payment-file-size"></div>
                        </div>
                        <button type="button" id="paymentFileRemove" class="btn payment-file-remove" aria-label="Hapus berkas terpilih">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <div id="paymentClientError" class="payment-error d-none" role="alert"></div>
                <?php if (!empty($errors['payment_proof'])): ?>
                    <div class="payment-error" role="alert"><?= escape($errors['payment_proof']) ?></div>
                <?php endif; ?>

                <button type="submit" id="paymentSubmit" class="btn payment-submit-btn w-100 d-none d-flex align-items-center justify-content-center gap-2 mt-3">
                    <i class="bi bi-send"></i>
                    <span>Kirim Bukti Pembayaran</span>
                </button>
            </form>
        </div>

        <div class="payment-trust d-flex flex-wrap justify-content-center gap-3 gap-md-5 mt-3">
            <span><i class="bi bi-fork-knife"></i> Bahan Berkualitas</span>
            <span><i class="bi bi-shield-check"></i> Aman &amp; Terpercaya</span>
            <span><i class="bi bi-heart"></i> Dikemas dengan Cinta</span>
        </div>
    </div>
</main>
