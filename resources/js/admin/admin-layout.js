// resources/js/admin/admin-layout.js

// ── LIGHTBOX (gambar) ──
function openLightbox(src) {
    const img = document.getElementById('lightboxImg');
    const box = document.getElementById('lightbox');
    if (!img || !box) return;
    img.src = src;
    box.classList.add('open');
}

function closeLightbox() {
    const box = document.getElementById('lightbox');
    if (box) box.classList.remove('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});

window.openLightbox  = openLightbox;
window.closeLightbox = closeLightbox;
