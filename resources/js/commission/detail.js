const cfg = window.commissionDetail || {};

/* ── GALLERY STATE ── */
const images = cfg.images || [];
let currentIndex = 0;

function goToImage(idx) {
    if (idx < 0) idx = images.length - 1;
    if (idx >= images.length) idx = 0;
    currentIndex = idx;

    const mainImg = document.getElementById('mainImg');
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = images[idx];
        mainImg.style.opacity = '1';
    }, 120);

    // Update thumbnails
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === idx);
    });

    // Update counter
    const counter = document.getElementById('galleryCounter');
    if (counter) counter.textContent = `${idx + 1} / ${images.length}`;

    // Update nav visibility
    const prev = document.getElementById('galleryPrev');
    const next = document.getElementById('galleryNext');
    if (prev) prev.classList.toggle('hidden', images.length <= 1);
    if (next) next.classList.toggle('hidden', images.length <= 1);

    // Scroll thumb into view
    const thumb = document.getElementById('thumb-' + idx);
    if (thumb) thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

function galleryNav(dir) {
    goToImage(currentIndex + dir);
}

/* ── LIGHTBOX ── */
function openLightbox(idx) {
    currentIndex = idx;
    document.getElementById('lightboxImg').src = images[idx];
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}

function lbNav(dir) {
    const newIdx = (currentIndex + dir + images.length) % images.length;
    currentIndex = newIdx;
    document.getElementById('lightboxImg').src = images[newIdx];
}

/* ── LIKE TOGGLE ── */
let isLiked = !!cfg.isLiked;
let likeCount = cfg.likeCount ?? 0;
let likeLoading = false;

async function toggleLike() {
    if (likeLoading) return;
    likeLoading = true;

    const btn = document.getElementById('like-btn');
    const icon = document.getElementById('like-icon');
    const count = document.getElementById('like-count');

    // Optimistic UI
    isLiked = !isLiked;
    likeCount += isLiked ? 1 : -1;
    if (likeCount < 0) likeCount = 0;

    btn.classList.toggle('liked', isLiked);
    btn.classList.add('pop');
    icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
    count.textContent = likeCount;

    setTimeout(() => btn.classList.remove('pop'), 400);

    try {
        const res = await fetch(cfg.likeToggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                likeable_id: cfg.serviceId,
                likeable_type: 'commission_service',
            }),
        });

        const data = await res.json();

        if (res.ok) {
            // Sinkronkan dengan server
            isLiked = data.liked;
            likeCount = data.like_count;
            btn.classList.toggle('liked', isLiked);
            icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
            count.textContent = likeCount;
        } else {
            // Rollback jika gagal
            isLiked = !isLiked;
            likeCount += isLiked ? 1 : -1;
            btn.classList.toggle('liked', isLiked);
            icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
            count.textContent = likeCount;
        }

    } catch (err) {
        // Rollback
        isLiked = !isLiked;
        likeCount += isLiked ? 1 : -1;
        btn.classList.toggle('liked', isLiked);
        icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
        count.textContent = likeCount;
    } finally {
        likeLoading = false;
    }
}

/* ── ADDON & PRICE ── */
var basePrice = cfg.basePrice ?? 0;
var addonTotal = 0;
var checkedAddons = [];

function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function updateTotal() {
    var total = basePrice + addonTotal;
    var elTotal = document.getElementById('totalPrice');
    var elBase = document.getElementById('basePriceDisp');
    var elAddon = document.getElementById('addonDisp');
    var rowAddon = document.getElementById('addonRow');

    if (elTotal) elTotal.textContent = fmt(total);
    if (elBase) elBase.textContent = fmt(basePrice);
    if (rowAddon) {
        if (addonTotal > 0) {
            rowAddon.style.display = 'flex';
            if (elAddon) elAddon.textContent = '+' + fmt(addonTotal);
        } else {
            rowAddon.style.display = 'none';
        }
    }
}

function toggleAddon(item, price) {
    item.classList.toggle('checked');
    var name = item.querySelector('.addon-name-text')?.textContent?.trim() ?? '';

    if (item.classList.contains('checked')) {
        addonTotal += price;
        checkedAddons.push({ name, price });
    } else {
        addonTotal -= price;
        addonTotal = Math.max(0, addonTotal);
        checkedAddons = checkedAddons.filter(a => !(a.name === name && a.price === price));
    }
    updateTotal();
}

function handleOrder(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-order');
    var form = document.getElementById('order-form');
    var inp = document.getElementById('selectedAddonsInput');
    if (inp) inp.value = JSON.stringify(checkedAddons);
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses…';
    form.submit();
}

/* ── DESC TOGGLE ── */
function toggleDesc(e) {
    var el = document.getElementById('descText');
    var btn = e.currentTarget;
    if (!el) return;
    var expanded = el.classList.toggle('expanded');
    btn.innerHTML = expanded
        ? '<i class="bi bi-chevron-up"></i> Lebih sedikit'
        : '<i class="bi bi-chevron-down"></i> Selengkapnya';
}

function initCommissionDetail() {
    /* ── TOUCH/SWIPE SUPPORT ── */
    let touchStartX = 0;
    const galleryMain = document.getElementById('galleryMain');
    if (galleryMain) {
        galleryMain.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        galleryMain.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) galleryNav(diff > 0 ? 1 : -1);
        }, { passive: true });
    }

    /* ── KEYBOARD NAV ── */
    document.addEventListener('keydown', e => {
        if (document.getElementById('lightbox').classList.contains('open')) {
            if (e.key === 'ArrowLeft') lbNav(-1);
            if (e.key === 'ArrowRight') lbNav(1);
            if (e.key === 'Escape') closeLightbox();
        } else {
            if (e.key === 'ArrowLeft') galleryNav(-1);
            if (e.key === 'ArrowRight') galleryNav(1);
        }
    });

    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === this) closeLightbox();
        });
    }

    updateTotal();
}

document.addEventListener('DOMContentLoaded', initCommissionDetail);

window.goToImage = goToImage;
window.galleryNav = galleryNav;
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.lbNav = lbNav;
window.toggleLike = toggleLike;
window.toggleAddon = toggleAddon;
window.handleOrder = handleOrder;
window.toggleDesc = toggleDesc;
