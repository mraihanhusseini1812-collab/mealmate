let currentMode = 'daily';
let currentWeeklyDay = 'Senin';

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function detectTodayDayName() {
    if (typeof todayFormatted === 'string' && todayFormatted.includes(',')) {
        const dayCandidate = todayFormatted.split(',')[0].trim();
        if (typeof weeklyData === 'object' && weeklyData !== null && dayCandidate in weeklyData) {
            return dayCandidate;
        }
    }
    return 'Senin';
}

function switchMode(mode) {
    currentMode = mode;

    const btnDaily = document.getElementById('btn-mode-daily');
    const btnWeeklyDesktop = document.getElementById('btn-mode-weekly');
    const btnWeeklyMobile = document.getElementById('btn-mode-weekly-mobile');
    const selectorContainer = document.getElementById('weekly-selector-container');

    if (mode === 'daily') {
        btnDaily.classList.add('active');
        btnDaily.classList.remove('non-active');

        if (btnWeeklyDesktop) {
            btnWeeklyDesktop.classList.add('non-active');
            btnWeeklyDesktop.classList.remove('active');
        }
        if (btnWeeklyMobile) {
            btnWeeklyMobile.classList.add('non-active');
            btnWeeklyMobile.classList.remove('active');
            document.getElementById('mobile-dropdown-label').innerText = 'Menu Minggu ini';
        }

        if (selectorContainer) {
            selectorContainer.classList.remove('is-open');
        }
        updateHeader('Menu Hari ini', 'Menu yang tersedia untuk hari ini', todayFormatted);
        renderCards(dailyData);
    } else {
        btnDaily.classList.add('non-active');
        btnDaily.classList.remove('active');

        if (btnWeeklyDesktop) {
            btnWeeklyDesktop.classList.add('active');
            btnWeeklyDesktop.classList.remove('non-active');
        }

        if (selectorContainer) {
            selectorContainer.classList.add('is-open');
        }
        selectWeeklyDay(currentWeeklyDay, false);
    }
}

function selectWeeklyDay(day, isMobileSelect = false) {
    currentWeeklyDay = day;

    const btnDaily = document.getElementById('btn-mode-daily');
    const btnWeeklyDesktop = document.getElementById('btn-mode-weekly');
    const btnWeeklyMobile = document.getElementById('btn-mode-weekly-mobile');

    if (isMobileSelect) {
        currentMode = 'weekly';

        btnDaily.classList.add('non-active');
        btnDaily.classList.remove('active');

        if (btnWeeklyMobile) {
            btnWeeklyMobile.classList.add('active');
            btnWeeklyMobile.classList.remove('non-active');
            document.getElementById('mobile-dropdown-label').innerText = `${day}`;
        }

        if (btnWeeklyDesktop) {
            btnWeeklyDesktop.classList.add('active');
            btnWeeklyDesktop.classList.remove('non-active');
        }
    }

    const radioElem = document.getElementById(`day-${day}`);
    if (radioElem) {
        radioElem.checked = true;
    }

    const dayData = (typeof weeklyData === 'object' && weeklyData !== null) ? (weeklyData[day] || {}) : {};
    const menus = dayData.menus || [];
    const dateText = dayData.tanggal || day;

    updateHeader(`Menu Hari ${day}`, `Menu yang tersedia untuk hari ${day}`, dateText);
    renderCards(menus);
}

function updateHeader(title, subtitle, date) {
    const headerContainer = document.getElementById('header-container');
    if (headerContainer) {
        headerContainer.classList.add('is-switching');
        setTimeout(() => headerContainer.classList.remove('is-switching'), 160);
    }
    document.getElementById('header-title').innerText = title;
    document.getElementById('header-subtitle').innerText = subtitle;
    document.getElementById('header-date').innerText = date;
}

function renderCards(menus) {
    const container = document.getElementById('menu-cards-container');
    if (!container) {
        return;
    }

    if (!menus || menus.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-5 menu-card-animate"><p class="text-muted mb-0">Belum ada jadwal menu yang tersedia</p></div>';
        return;
    }

    let allCardsHtml = '';

    menus.forEach((item, index) => {
        let badgeClass = 'badge-pagi';
        let iconClass = 'bi-sunrise';

        if (item.slot === 'Siang') {
            badgeClass = 'badge-siang';
            iconClass = 'bi-sun-fill';
        } else if (item.slot === 'Malam') {
            badgeClass = 'badge-malam';
            iconClass = 'bi-moon-stars';
        }

        const rating = Number(item.rating) || 0;
        const reviews = Number(item.reviews) || 0;
        let starsHtml = '';

        for (let i = 1; i <= 5; i++) {
            if (rating >= i) {
                starsHtml += '<i class="bi bi-star-fill"></i>';
            } else if (rating >= i - 0.5) {
                starsHtml += '<i class="bi bi-star-half"></i>';
            } else {
                starsHtml += '<i class="bi bi-star"></i>';
            }
        }

        const delayMs = index * 55;

        allCardsHtml += `
            <div class="card-container col-md d-flex justify-content-center align-items-center p-0 gap-3 menu-card-animate" style="animation-delay: ${delayMs}ms;">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 400px;">
                    <div class="position-relative">
                        <img src="./assets/img/${escapeHtml(item.image)}" class="card-img-top" alt="${escapeHtml(item.menu_name)}" loading="lazy" style="width: 100%; height: 220px; object-fit: cover;">

                        <span class="badge-slot ${badgeClass}">
                            <i class="bi ${iconClass}"></i>
                            ${escapeHtml(item.slot)}
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-success mb-2">${escapeHtml(item.menu_name)}</h5>
                        <p class="card-text text-muted small flex-grow-1">
                            ${escapeHtml(item.description)}
                        </p>

                        <div class="d-flex align-items-center gap-1 mt-2">
                            <div class="text-warning small">
                                ${starsHtml}
                            </div>
                            <span class="text-muted small fw-semibold ms-1">(${reviews})</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = allCardsHtml;
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('btn-mode-daily')) {
        currentWeeklyDay = detectTodayDayName();
        const urlParams = new URLSearchParams(window.location.search);
        const requestedMode = urlParams.get('mode') === 'weekly' ? 'weekly' : 'daily';
        switchMode(requestedMode);
    }
    initPackageSlider();
});

function initPackageSlider() {
    const track = document.getElementById('package-track');
    const nav = document.getElementById('packages-nav');
    const prev = document.getElementById('package-prev');
    const next = document.getElementById('package-next');
    if (!track || !nav || !prev || !next) return;

    const step = () => {
        const card = track.querySelector('.package-card');
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        return card ? card.offsetWidth + gap : track.clientWidth;
    };

    function updateNav() {
        const overflow = track.scrollWidth > track.clientWidth + 1;

        nav.classList.toggle('d-md-flex', overflow);
        track.classList.toggle('justify-content-center', !overflow);

        prev.disabled = track.scrollLeft <= 0;
        next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
    }

    prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));

    track.addEventListener('scroll', updateNav, { passive: true });
    window.addEventListener('resize', updateNav);
    window.addEventListener('load', updateNav);
    if ('ResizeObserver' in window) new ResizeObserver(updateNav).observe(track);

    updateNav();
}
