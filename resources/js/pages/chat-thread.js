// resources/js/pages/chat-thread.js

// ── ELEMENTS ──
const msgArea    = document.getElementById('messages-area');
const orderId    = msgArea?.dataset.orderId    || null;
const currentUid = msgArea?.dataset.currentUserId || null;
const form       = document.getElementById('chat-form');
const input      = document.getElementById('message-input');
const imageInput = document.getElementById('image-input');

// Reset badge saat thread dibuka
if (typeof window._refreshChatBadge === 'function') {
    window._refreshChatBadge();
}

// ── AUTO SCROLL ──
function scrollBottom() {
    if (msgArea) msgArea.scrollTop = msgArea.scrollHeight;
}
scrollBottom();

// ── ENTER TO SEND ──
function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submitForm();
    }
}

// ── AUTO RESIZE TEXTAREA ──
if (input) {
    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

// ── SEND VIA AJAX ──
function submitForm() {
    if (!form) return;
    const formData = new FormData(form);
    const msg   = formData.get('message')?.trim();
    const image = formData.get('image')?.size > 0;
    if (!msg && !image) return;

    if (msg) appendBubbleMine(msg, null, new Date());

    if (input)      { input.value = ''; input.style.height = 'auto'; }
    if (imageInput) imageInput.value = '';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(() => {
        // Update sidebar conv-item milik kita (preview + waktu + checkmark unread)
        const receiverId = document.querySelector('input[name="receiver_id"]')?.value;
        const href = orderId
            ? buildHref('order', orderId)
            : buildHref('user',  receiverId);
        updateSidebar({ message: msg || '📎 Attachment', sender_id: currentUid }, href, true);
    })
    .catch(err => console.error('Send failed:', err));
}

if (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm();
    });
}

// ── HELPERS ──
function buildHref(type, id) {
    if (!id) return '';
    const base = window.location.origin;
    return type === 'order'
        ? `${base}/chat/thread?order_id=${id}`
        : `${base}/chat/thread?user_id=${id}`;
}

function toRelative(href) {
    try {
        return new URL(href, window.location.origin).pathname +
               new URL(href, window.location.origin).search;
    } catch { return href; }
}

function findConvItem(href) {
    const target = toRelative(href);
    return [...document.querySelectorAll('.conv-item')].find(el =>
        toRelative(el.getAttribute('href') || el.dataset.href || '') === target
    ) || null;
}

// ── SIDEBAR UPDATE ──
// isMine = true  → kita yang kirim (checkmark abu, preview update)
// isMine = false → orang lain kirim (tambah unread badge, preview tebal)
function updateSidebar(data, href, isMine = false) {
    const convList = document.querySelector('.conv-list');
    if (!convList) return;

    const item = findConvItem(href);
    if (!item) {
        // Pesan pertama dari user baru — reload satu kali
        window.location.reload();
        return;
    }

    // Update preview
    const preview = item.querySelector('.conv-preview');
    if (preview) preview.textContent = data.message || '📎 Attachment';

    // Update waktu
    const timeEl = item.querySelector('.conv-time');
    if (timeEl) timeEl.textContent = 'baru saja';

    // Update checkmark (hanya untuk pesan milik kita)
    const convBottom = item.querySelector('.conv-bottom');
    if (convBottom) {
        let check = convBottom.querySelector('.conv-check');
        if (isMine) {
            if (!check) {
                check = document.createElement('i');
                check.className = 'bi bi-check2-all conv-check';
                convBottom.prepend(check);
            }
            // Abu = belum dibaca penerima
            check.classList.remove('read');
        } else {
            // Pesan dari orang lain — hapus checkmark kalau ada
            if (check) check.remove();
        }
    }

    if (!isMine) {
        // Tambah/update unread badge
        let badge = item.querySelector('.conv-unread-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'conv-unread-badge';
            const convTop = item.querySelector('.conv-top');
            if (convTop) convTop.appendChild(badge);
        }
        const current = parseInt(badge.textContent) || 0;
        badge.textContent = current + 1;

        // Tebalkan nama & preview
        item.classList.add('unread');
    }

    // Naikkan ke atas
    convList.prepend(item);
}

// ── REALTIME: LISTEN KE PUSHER ──
if (window.Echo && currentUid) {
    if (orderId) {
        window.Echo.private(`order.${orderId}`)
            .listen('.MessageSent', (e) => {
                appendBubbleOther(e);
                // Update sidebar — pesan masuk di thread aktif ini
                updateSidebar(e, buildHref('order', orderId), false);
                // Hapus unread badge untuk thread ini karena user sedang di sini
                const item = findConvItem(buildHref('order', orderId));
                if (item) {
                    item.querySelector('.conv-unread-badge')?.remove();
                    item.classList.remove('unread');
                }
            });
    } else {
        const receiverId = document.querySelector('input[name="receiver_id"]')?.value;
        if (receiverId) {
            const ids = [currentUid, receiverId].sort();
            window.Echo.private(`dm.${ids[0]}.${ids[1]}`)
                .listen('.MessageSent', (e) => {
                    appendBubbleOther(e);
                    updateSidebar(e, buildHref('user', receiverId), false);
                    // User sedang di thread ini → langsung hapus badge
                    const item = findConvItem(buildHref('user', receiverId));
                    if (item) {
                        item.querySelector('.conv-unread-badge')?.remove();
                        item.classList.remove('unread');
                    }
                });
        }
    }
}

// ── LISTEN EVENT DARI APP.JS (pesan dari channel LAIN) ──
window.addEventListener('chat:message', (ev) => {
    const { data, href } = ev.detail;
    if (data.sender_id === currentUid) return;

    // Kalau pesan ini untuk thread yang sedang dibuka, skip (sudah ditangani Echo atas)
    if (toRelative(href) === window.location.pathname + window.location.search) return;

    // Update sidebar saja
    updateSidebar(data, href, false);
});

// ── APPEND BUBBLE MILIK SENDIRI ──
function appendBubbleMine(message, imagePath, date) {
    if (!msgArea) return;
    const time = date
        ? date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        : '';
    const row = document.createElement('div');
    row.className = 'bubble-row mine';
    let inner = '<div>';
    if (imagePath) inner += `<div class="bubble-img"><img src="/storage/${imagePath}" alt="attachment"></div>`;
    if (message)   inner += `<div class="bubble">${escapeHtml(message)}</div>`;
    // Checkmark: abu dulu (belum dibaca)
    inner += `<div class="bubble-time">${time} <i class="bi bi-check2-all read-check"></i></div>`;
    inner += '</div>';
    row.innerHTML = inner;
    msgArea.appendChild(row);
    scrollBottom();
}

// ── APPEND BUBBLE DARI LAWAN BICARA ──
function appendBubbleOther(data) {
    if (!msgArea) return;
    const avatar = data.sender?.avatar || '/images/default-avatar.png';
    const time = data.created_at
        ? new Date(data.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        : '';
    const row = document.createElement('div');
    row.className = 'bubble-row';
    let inner = `<img src="${avatar}" class="bubble-avatar" alt="">`;
    inner += '<div>';
    if (data.image)   inner += `<div class="bubble-img"><img src="/storage/${data.image}" alt="attachment"></div>`;
    if (data.message) inner += `<div class="bubble">${escapeHtml(data.message)}</div>`;
    inner += `<div class="bubble-time">${time}</div>`;
    inner += '</div>';
    row.innerHTML = inner;
    msgArea.appendChild(row);
    scrollBottom();
}

// ── ESCAPE HTML ──
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

window.handleEnter = handleEnter;
