import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ── ECHO INIT ──
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// ── PROFILE MENU ──
function toggleProfileMenu(e) {
    e.stopPropagation();
    const trigger  = document.getElementById('profileTrigger');
    const dropdown = document.getElementById('profileDropdown');
    if (!trigger || !dropdown) return;
    const isOpen = dropdown.classList.contains('open');
    isOpen ? closeProfileMenu() : (
        trigger.classList.add('open'),
        trigger.setAttribute('aria-expanded', 'true'),
        dropdown.classList.add('open')
    );
}
function closeProfileMenu() {
    const t = document.getElementById('profileTrigger');
    const d = document.getElementById('profileDropdown');
    if (!t || !d) return;
    t.classList.remove('open');
    t.setAttribute('aria-expanded', 'false');
    d.classList.remove('open');
}
document.addEventListener('click', e => {
    const wrap = document.getElementById('profileMenuWrap');
    if (wrap && !wrap.contains(e.target)) closeProfileMenu();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeProfileMenu();
});
window.toggleProfileMenu = toggleProfileMenu;
window.closeProfileMenu  = closeProfileMenu;

// ── CHAT BADGE & TOAST ──
(function () {
    const userIdMeta = document.querySelector('meta[name="auth-user-id"]');
    if (!userIdMeta) return;

    const currentUserId = userIdMeta.getAttribute('content');

    // ── Badge ──
    function getBadge() {
        return document.getElementById('chat-nav-badge');
    }

    function setBadgeCount(n) {
        const badge = getBadge();
        if (!badge) return;
        if (n > 0) {
            badge.textContent = n > 99 ? '99+' : n;
            badge.classList.add('visible');
        } else {
            badge.textContent = '';
            badge.classList.remove('visible');
        }
    }

    // Fetch count dari server saat halaman load
    function refreshBadge() {
        fetch('/chat/unread-count', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(r => r.json())
            .then(data => setBadgeCount(data.count))
            .catch(() => {});
    }

    refreshBadge();

    // Expose untuk dipanggil dari luar (chat-thread reset badge)
    window._refreshChatBadge = refreshBadge;
    window._setBadgeCount    = setBadgeCount;

    // ── Toast ──
    const toast        = document.getElementById('chat-notif-toast');
    const toastAvatar  = document.getElementById('chat-notif-avatar');
    const toastName    = document.getElementById('chat-notif-name');
    const toastMessage = document.getElementById('chat-notif-message');
    let notifTimer     = null;

    function isInActiveChatThread() {
        const area = document.getElementById('messages-area');
        return !!(area && area.dataset.currentUserId);
    }

    function showToast(data, href) {
        // Toast hanya di luar thread
        if (isInActiveChatThread()) return;

        if (toast) {
            toastAvatar.src          = data.sender?.avatar || '/images/default-avatar.png';
            toastName.textContent    = data.sender?.name   || 'Someone';
            toastMessage.textContent = data.message        || '📎 Sent an attachment';
            toast.classList.add('show');
            toast.onclick = (ev) => {
                if (ev.target.id === 'chat-notif-close') return;
                window.location.href = href;
            };
            clearTimeout(notifTimer);
            notifTimer = setTimeout(() => toast.classList.remove('show'), 5000);
        }

        // Update badge: tambah 1 lalu fetch ulang dari server untuk angka akurat
        refreshBadge();
    }

    window._closeChatNotif = function () {
        if (toast) toast.classList.remove('show');
        clearTimeout(notifTimer);
    };

    // Subscribe ke semua channel aktif user
    fetch('/chat/active-channels', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then(r => r.json())
        .then(channels => {
            channels.forEach(ch => {
                window.Echo.private(ch.channel)
                    .listen('.MessageSent', (e) => {
                        if (e.sender_id === currentUserId) return;
                        showToast(e, ch.href);

                        // Dispatch custom event supaya chat-list.js bisa listen
                        window.dispatchEvent(new CustomEvent('chat:message', {
                            detail: { data: e, href: ch.href }
                        }));
                    });
            });
        })
        .catch(err => console.error('Chat notif error:', err));
})();
