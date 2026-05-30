// resources/js/pages/verification/show.js

// ── SCORE STATE ──
const scores = {
    score_social_style:    0,
    score_social_age:      0,
    score_social_wip:      0,
    score_social_comments: 0,
    score_portfolio:       0,
};

// Init dari data attributes
const meta = document.getElementById('score-meta');
if (meta) {
    scores.score_social_style    = parseInt(meta.dataset.scoreSocialStyle    || 0);
    scores.score_social_age      = parseInt(meta.dataset.scoreSocialAge      || 0);
    scores.score_social_wip      = parseInt(meta.dataset.scoreSocialWip      || 0);
    scores.score_social_comments = parseInt(meta.dataset.scoreSocialComments || 0);
    scores.score_portfolio       = parseInt(meta.dataset.scorePortfolio      || 0);
}

// ── UPDATE SCORE DARI SLIDER ──
function updateScore(rangeEl) {
    const key = rangeEl.dataset.key;
    const val = parseInt(rangeEl.value);
    scores[key] = val;

    // Update display label
    const display = document.getElementById('val_' + key);
    if (display) display.textContent = val;

    // Update preview total
    const total = Object.values(scores).reduce((a, b) => a + b, 0);
    const preview = document.getElementById('previewValue');
    if (preview) {
        preview.textContent = total + '/100';
        preview.style.color = total >= 60 ? '#34d399' : total >= 40 ? '#fbbf24' : '#f87171';
    }
}

// Init preview on load
document.addEventListener('DOMContentLoaded', () => {
    // Trigger semua slider untuk set initial display
    document.querySelectorAll('input[type="range"][data-key]').forEach(el => {
        updateScore(el);
    });
});

// ── SYNC FORM + CONFIRM ──
function syncAndConfirm(form, msg) {
    // Validasi catatan final
    const finalNotes = document.getElementById('notes_final')?.value?.trim();
    if (!finalNotes || finalNotes.length < 10) {
        alert('Catatan final wajib diisi (minimal 10 karakter).');
        return false;
    }

    if (!confirm(msg)) return false;

    // Sync semua nilai ke hidden inputs
    const prefix = form.id === 'approveForm' ? 'hid_' : 'hid2_';

    Object.keys(scores).forEach(key => {
        const el = document.getElementById(prefix + key);
        if (el) el.value = scores[key];
    });

    const setHid = (id, srcId) => {
        const el  = document.getElementById(prefix + srcId.replace('notes_', 'notes_'));
        const src = document.getElementById(srcId);
        if (el && src) el.value = src.value;
    };

    // Sync notes
    const noteFields = ['notes_social', 'notes_portfolio', 'notes_final'];
    noteFields.forEach(field => {
        const hidId  = prefix + field.replace('notes_', 'notes_');
        const hidEl  = document.getElementById(hidId);
        const srcEl  = document.getElementById(field);
        if (hidEl && srcEl) hidEl.value = srcEl.value;
    });

    return true;
}

// ── LIGHTBOX ──
function openLightbox(url, name) {
    const lb  = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    const nm  = document.getElementById('lightboxName');
    if (!lb || !img) return;
    img.src = url;
    if (nm) nm.textContent = name || '';
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lb = document.getElementById('lightbox');
    if (lb) lb.classList.remove('open');
    document.body.style.overflow = '';
}

// ESC to close lightbox
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
});

// ── EXPOSE GLOBALS ──
window.updateScore     = updateScore;
window.syncAndConfirm  = syncAndConfirm;
window.openLightbox    = openLightbox;
window.closeLightbox   = closeLightbox;
