/* ─── MODAL ──────────────────────────── */
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
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
function setOverallStar(val) {
    document.getElementById('overallRating').value = val;
    document.querySelectorAll('#overallStars .star-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i < val);
    });
}

function setMiniStar(cat, val) {
    document.getElementById('rating-' + cat).value = val;
    document.querySelectorAll(`#stars-${cat} .mini-star`).forEach((btn, i) => {
        btn.classList.toggle('active', i < val);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => {
            if (e.target === m) closeModal(m.id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    });
    document.querySelectorAll('.star-btn.active').forEach(btn => {
        btn.style.color = 'var(--yellow)';
    });
    document.querySelectorAll('.mini-star.active').forEach(btn => {
        btn.style.color = 'var(--yellow)';
    });
});

window.openModal = openModal;
window.closeModal = closeModal;
window.showFileName = showFileName;
window.setOverallStar = setOverallStar;
window.setMiniStar = setMiniStar;
