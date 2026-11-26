<main class="faq-page">
    <section id="faq-section" class="py-4 py-lg-5">
        <div class="container">
            <div class="faq-card mx-auto">
                <div class="accordion faq-accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq):
                        $number = $index + 1;
                        $isFirst = ($index === 0);
                    ?>
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="faq-heading-<?= $number ?>">
                                <button
                                    class="accordion-button faq-btn <?= $isFirst ? '' : 'collapsed' ?>"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq-collapse-<?= $number ?>"
                                    aria-expanded="<?= $isFirst ? 'true' : 'false' ?>"
                                    aria-controls="faq-collapse-<?= $number ?>"
                                >
                                    <span class="faq-number"><?= $number ?></span>
                                    <span class="faq-question"><?= escape($faq['question']) ?></span>
                                    <i class="bi bi-chevron-down faq-chevron" aria-hidden="true"></i>
                                </button>
                            </h2>
                            <div
                                id="faq-collapse-<?= $number ?>"
                                class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>"
                                aria-labelledby="faq-heading-<?= $number ?>"
                                data-bs-parent="#faqAccordion"
                            >
                                <div class="accordion-body faq-body">
                                    <div class="faq-answer"><?= $faq['answer'] ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>