function switchTab(name, el) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
    const panel = document.getElementById('tab-' + name);
    if (panel) panel.classList.add('active');
    el.classList.add('active');
}

function filterClientOrders(status, chip) {
    document.querySelectorAll('.chip').forEach(c => {
        c.style.background = 'var(--surface2)';
        c.style.color = 'var(--muted)';
        c.style.borderColor = 'var(--border2)';
    });
    chip.style.background = 'var(--accent-dim)';
    chip.style.color = 'var(--accent)';
    chip.style.borderColor = 'var(--accent)';

    document.querySelectorAll('#cl-orders-list .cl-order-item').forEach(item => {
        item.style.display = (status === 'all' || item.dataset.status === status) ? 'flex' : 'none';
    });
}

function openReviewModal(orderId, artistName) {
    document.getElementById('reviewOrderId').value = orderId;
    document.getElementById('reviewArtistName').textContent = artistName;
    document.getElementById('reviewModal').classList.add('open');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });
    });
});

window.switchTab = switchTab;
window.filterClientOrders = filterClientOrders;
window.openReviewModal = openReviewModal;
