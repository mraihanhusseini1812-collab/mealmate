document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reviewForm');
    if (!form) return;

    // counter karakter per komentar
    form.querySelectorAll('.review-comment').forEach((textarea) => {
        const counter = form.querySelector('.review-counter[data-for="' + textarea.id + '"]');
        if (!counter) return;
        const max = parseInt(textarea.getAttribute('maxlength') || '500', 10);
        const update = () => {
            counter.textContent = textarea.value.length + '/' + max;
        };
        textarea.addEventListener('input', update);
        update();
    });

    // no no ya klik ganda (validasi sah tetap di server)
    const submitBtn = document.getElementById('reviewSubmit');
    if (submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
        });
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) submitBtn.disabled = false;
        });
    }
});