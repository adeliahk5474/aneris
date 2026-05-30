const cfg = window.notificationsPage || {};

function markRead(notifId) {
    // Pakai notif_id — bukan id
    const url = (cfg.markReadUrlTemplate || `/notifications/${notifId}/read`).replace('__NOTIF_ID__', notifId);
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Content-Type': 'application/json',
        }
    }).catch(() => {});
}

window.markRead = markRead;
