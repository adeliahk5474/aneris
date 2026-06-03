/* ─── MODAL ──────────────────────────── */
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';

    if (id === 'reviewModal') {
        starValues['overall'] = 5;
        if (document.getElementById('overallRating')) {
            document.getElementById('overallRating').value = 5;
        }
        renderOverallStars(5);

        document.querySelectorAll('.mini-stars').forEach(container => {
            const cat = container.id.replace('stars-', '');
            starValues[cat] = 5;
            const input = document.getElementById('rating-' + cat);
            if (input) input.value = 5;
            renderMiniStars(cat, 5);
        });
    }
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

/* ─── FILE UPLOAD LABEL ──────────────── */
function showFileName(input) {
    const label = document.getElementById('uploadLabel');
    if (input.files[0]) {
        label.textContent = input.files[0].name;
        label.className = 'send-upload-text selected';
    }
}

/* ─── REVIEW STARS ───────────────────── */
const starValues = {};

function setOverallStar(val) {
    starValues['overall'] = val;
    document.getElementById('overallRating').value = val;
    renderOverallStars(val);
}

function renderOverallStars(val) {
    document.querySelectorAll('#overallStars .star-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i < val);
    });
}

function setMiniStar(cat, val) {
    starValues[cat] = val;
    document.getElementById('rating-' + cat).value = val;
    renderMiniStars(cat, val);
}

function renderMiniStars(cat, val) {
    document.querySelectorAll(`#stars-${cat} .mini-star`).forEach((btn, i) => {
        btn.classList.toggle('active', i < val);
    });
}

function initStars() {
    const overallContainer = document.getElementById('overallStars');
    if (overallContainer) {
        const btns = overallContainer.querySelectorAll('.star-btn');
        const defaultOverall = parseInt(document.getElementById('overallRating')?.value || '5');
        starValues['overall'] = defaultOverall;
        renderOverallStars(defaultOverall);

        btns.forEach((btn, i) => {
            btn.addEventListener('mouseenter', () => renderOverallStars(i + 1));
            btn.addEventListener('mouseleave', () => renderOverallStars(starValues['overall'] || 5));
            btn.addEventListener('click', () => setOverallStar(i + 1));
        });
    }

    document.querySelectorAll('.mini-stars').forEach(container => {
        const cat = container.id.replace('stars-', '');
        const btns = container.querySelectorAll('.mini-star');
        const defaultVal = parseInt(document.getElementById('rating-' + cat)?.value || '5');
        starValues[cat] = defaultVal;
        renderMiniStars(cat, defaultVal);

        btns.forEach((btn, i) => {
            btn.addEventListener('mouseenter', () => renderMiniStars(cat, i + 1));
            btn.addEventListener('mouseleave', () => renderMiniStars(cat, starValues[cat] || 5));
            btn.addEventListener('click', () => setMiniStar(cat, i + 1));
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => {
            if (e.target === m) closeModal(m.id);
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        }
    });

    initStars();
});

window.openModal = openModal;
window.closeModal = closeModal;
window.showFileName = showFileName;
window.setOverallStar = setOverallStar;
window.setMiniStar = setMiniStar;
