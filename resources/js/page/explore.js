const cfg = window.explorePage || {};
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const isAuth = cfg.isAuth ?? false;

async function toggleLike(btn, likeableId, likeableType) {
    if (!isAuth) { window.location.href = cfg.authFormUrl; return; }

    // Optimistic UI
    const isLiked = btn.classList.contains('liked');
    const icon    = btn.querySelector('i');

    btn.classList.toggle('liked');
    icon.className = isLiked ? 'bi bi-heart' : 'bi bi-heart-fill';

    // Animasi pop
    btn.classList.remove('pop');
    void btn.offsetWidth;
    btn.classList.add('pop');

    // Update like count di badge
    const card   = btn.closest('.masonry-item');
    const badge  = card ? document.getElementById('lc-' + likeableId) : null;
    let curCount = parseInt(card?.dataset.likeCount ?? '0', 10);
    curCount     = isLiked ? Math.max(0, curCount - 1) : curCount + 1;
    if (card) card.dataset.likeCount = curCount;

    if (badge) {
        if (curCount > 0) {
            badge.classList.remove('hidden');
            badge.querySelector('span').textContent = curCount;
        } else {
            badge.classList.add('hidden');
        }
    }

    try {
        const res = await fetch(cfg.likeToggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ likeable_id: likeableId, likeable_type: likeableType }),
        });

        const data = await res.json();

        if (!res.ok) {
            // Rollback
            btn.classList.toggle('liked');
            icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
            return;
        }

        // Sync ke server value
        if (badge) {
            const sc = data.like_count ?? curCount;
            if (card) card.dataset.likeCount = sc;
            if (sc > 0) {
                badge.classList.remove('hidden');
                badge.querySelector('span').textContent = sc;
            } else {
                badge.classList.add('hidden');
            }
        }

    } catch {
        // Rollback jika error
        btn.classList.toggle('liked');
        icon.className = isLiked ? 'bi bi-heart-fill' : 'bi bi-heart';
    }
}

window.toggleLike = toggleLike;
