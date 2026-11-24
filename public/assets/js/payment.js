document.addEventListener('DOMContentLoaded', () => {
    initCopyButtons();
    initPaymentDropzone();
});

// cegah browser buka file di tab baru
['dragover', 'drop'].forEach((evt) => {
    window.addEventListener(evt, (e) => {
        if (document.getElementById('paymentDropzone')) {
            e.preventDefault();
        }
    });
});

function initCopyButtons() {
    document.querySelectorAll('.payment-copy-btn[data-copy]').forEach((btn) => {
        const label = btn.querySelector('.payment-copy-label');
        const icon = btn.querySelector('i');
        let resetTimer = null;

        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy || '';
            let ok = false;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                    ok = true;
                }
            } catch (err) {
                ok = false;
            }

            if (!ok) {
                // fallback untuk koneksi non-HTTPS (akses lewat IP lokal)
                const temp = document.createElement('textarea');
                temp.value = text;
                temp.setAttribute('readonly', '');
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.select();
                try {
                    ok = document.execCommand('copy');
                } catch (err) {
                    ok = false;
                }
                document.body.removeChild(temp);
            }

            if (label) label.textContent = ok ? 'Tersalin' : 'Gagal';
            if (icon) icon.className = ok ? 'bi bi-check2' : 'bi bi-x';
            btn.classList.toggle('copied', ok);

            clearTimeout(resetTimer);
            resetTimer = setTimeout(() => {
                if (label) label.textContent = 'Salin';
                if (icon) icon.className = 'bi bi-copy';
                btn.classList.remove('copied');
            }, 2000);
        });
    });
}

function initPaymentDropzone() {
    const dropzone = document.getElementById('paymentDropzone');
    const input = document.getElementById('paymentProof');
    const idleBox = document.getElementById('paymentDropzoneIdle');
    const fileInfo = document.getElementById('paymentFileInfo');
    const fileName = document.getElementById('paymentFileName');
    const fileSize = document.getElementById('paymentFileSize');
    const removeBtn = document.getElementById('paymentFileRemove');
    const clientError = document.getElementById('paymentClientError');
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('paymentSubmit');
    if (!dropzone || !input || !idleBox || !fileInfo) return;

    const maxBytes = parseInt(dropzone.dataset.maxBytes || '2097152', 10);
    const allowedTypes = ['image/jpeg', 'image/png'];

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function showError(message) {
        clientError.textContent = message;
        clientError.classList.remove('d-none');
        dropzone.classList.add('has-error');
    }

    function clearError() {
        clientError.textContent = '';
        clientError.classList.add('d-none');
        dropzone.classList.remove('has-error');
    }

    function renderEmpty() {
        fileInfo.classList.add('d-none');
        idleBox.classList.remove('d-none');
        if (submitBtn) submitBtn.classList.add('d-none');
    }

    function renderFile(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatSize(file.size);
        idleBox.classList.add('d-none');
        fileInfo.classList.remove('d-none');
        if (submitBtn) submitBtn.classList.remove('d-none');
    }

    function resetInput() {
        input.value = '';
        renderEmpty();
    }

    // validasi sisi klien (validasi sah di server)
    function handleFile(file) {
        clearError();
        if (!file) {
            renderEmpty();
            return;
        }
        if (!allowedTypes.includes(file.type)) {
            resetInput();
            showError('Format berkas tidak sesuai ketentuan (wajib JPG/PNG).');
            return;
        }
        if (file.size > maxBytes) {
            resetInput();
            showError('Ukuran berkas melebihi batas maksimal ' + (maxBytes / 1048576) + ' MB.');
            return;
        }
        renderFile(file);
    }

    input.addEventListener('change', () => handleFile(input.files[0] || null));

    removeBtn.addEventListener('click', () => {
        clearError();
        resetInput();
    });

    ['dragenter', 'dragover'].forEach((evt) => {
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'dragend'].forEach((evt) => {
        dropzone.addEventListener(evt, () => dropzone.classList.remove('is-dragover'));
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('is-dragover');

        const files = e.dataTransfer ? e.dataTransfer.files : null;
        if (!files || files.length === 0) return;

        const file = files[0];
        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        } catch (err) {
            showError('Browser Anda tidak mendukung drag & drop. Gunakan tombol Choose File.');
            return;
        }
        handleFile(file);
    });

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
        });
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                submitBtn.disabled = false;
                handleFile(input.files[0] || null);
            }
        });
    }
}
