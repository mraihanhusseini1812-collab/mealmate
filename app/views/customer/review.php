<main class="review-page py-4 py-lg-5">
    <div class="container">
        <a href="<?= escape($detailUrl) ?>" class="review-back text-decoration-none d-inline-flex align-items-center gap-3 mb-4">
            <i class="bi bi-chevron-left"></i>
            <h1 class="mb-0">Kembali ke Detail Pesanan</h1>
        </a>

        <div class="review-card mx-auto">
            <div class="review-hero text-center">
                <h2 class="review-hero-title"><?= escape($heroTitle) ?></h2>
                <p class="review-hero-sub"><?= escape($heroSub) ?></p>
            </div>

            <?php if (empty($reviewMenus)): ?>
                <div class="review-empty text-center">
                    <i class="bi bi-journal-x d-block mb-2"></i>
                    <p class="fw-semibold mb-1">Belum ada menu yang dapat diulas</p>
                    <p class="text-muted small mb-0">Jadwal menu untuk pesanan ini belum tersedia.</p>
                </div>
            <?php else: ?>
                <form action="<?= escape($formAction) ?>" method="POST" id="reviewForm" novalidate>
                    <input type="hidden" name="order_id" value="<?= (int) $orderId ?>">

                    <?php if ($generalError !== null): ?>
                        <div class="alert alert-danger rounded-3 py-2 px-3 small" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i><?= escape($generalError) ?>
                        </div>
                    <?php endif; ?>

                    <div class="review-list">
                        <?php foreach ($reviewMenus as $item): ?>
                            <section class="review-item">
                                <div class="review-item-head">
                                    <div class="review-thumb">
                                        <?php if ($item['image_url'] !== null): ?>
                                            <img src="<?= escape($item['image_url']) ?>" alt="<?= escape($item['name']) ?>" loading="lazy">
                                        <?php else: ?>
                                            <i class="bi bi-egg-fried"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="review-item-main">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <strong class="review-item-name"><?= escape($item['name']) ?></strong>
                                            <span class="review-item-meta"><?= escape($item['meta']) ?></span>
                                        </div>

                                        <?php if ($item['reviewed']): ?>
                                            <div class="review-stars-static"><?= $item['stars_html'] ?></div>
                                        <?php else: ?>
                                            <div class="review-stars" role="radiogroup" aria-label="Rating <?= escape($item['name']) ?>">
                                                <?php foreach ([5, 4, 3, 2, 1] as $value): ?>
                                                    <input
                                                        type="radio"
                                                        class="visually-hidden"
                                                        name="rating[<?= (int) $item['menu_id'] ?>]"
                                                        id="star-<?= (int) $item['menu_id'] ?>-<?= $value ?>"
                                                        value="<?= $value ?>"
                                                        <?= $item['rating'] === $value ? 'checked' : '' ?>
                                                    >
                                                    <label for="star-<?= (int) $item['menu_id'] ?>-<?= $value ?>" title="<?= $value ?> bintang">
                                                        <i class="bi bi-star-fill"></i>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($item['reviewed']): ?>
                                    <div class="review-done">
                                        <span class="review-done-badge"><i class="bi bi-check-circle-fill me-1"></i>Sudah diulas</span>
                                        <?php if ($item['comment'] !== ''): ?>
                                            <p class="review-done-comment"><?= nl2br(escape($item['comment'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="review-comment-wrap">
                                        <textarea
                                            name="comment[<?= (int) $item['menu_id'] ?>]"
                                            id="comment-<?= (int) $item['menu_id'] ?>"
                                            rows="2"
                                            maxlength="<?= (int) $maxComment ?>"
                                            class="form-control review-comment <?= $item['error'] !== null ? 'is-invalid' : '' ?>"
                                            placeholder="Ceritakan pengalaman Anda tentang menu ini (opsional)"
                                            aria-label="Komentar untuk <?= escape($item['name']) ?>"
                                        ><?= escape($item['comment']) ?></textarea>
                                        <span class="review-counter" data-for="comment-<?= (int) $item['menu_id'] ?>"><?= (int) $item['comment_length'] ?>/<?= (int) $maxComment ?></span>
                                    </div>
                                    <?php if ($item['error'] !== null): ?>
                                        <div class="invalid-feedback d-block"><?= escape($item['error']) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </section>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($pendingCount > 0): ?>
                        <button type="submit" id="reviewSubmit" class="btn review-submit-btn w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send"></i>
                            <span>Kirim Ulasan</span>
                        </button>   
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>