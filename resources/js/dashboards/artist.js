// resources/js/dashboards/artist.js

const cfg = window.artistDashboard || {};

/* ══════════════════════════════════════
   INIT
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    // ── Revenue Chart ──────────────────────────────
    const ctx = document.getElementById('revenueChart');
    if (ctx && typeof Chart !== 'undefined') {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: cfg.monthlyLabels || [],
                datasets: [{
                    data: cfg.monthlyEarnings || [],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#0d0d0f',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => 'Rp ' + c.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: {
                        display: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            color: '#7c7b87',
                            font: { size: 10 },
                            callback: v => v >= 1000000
                                ? 'Rp ' + (v / 1000000).toFixed(1) + 'jt'
                                : 'Rp ' + v.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    }

    // ── Modal backdrop click to close ──────────────
    ['sendModal', 'reviewModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', e => {
            if (e.target === el) el.classList.remove('open');
        });
    });

    // ── Auto-open tab dari URL query ?tab=xxx ──────
    const params  = new URLSearchParams(window.location.search);
    const tabName = params.get('tab');
    if (tabName) {
        const navItem = document.querySelector(`.sidebar-nav-item[onclick*="'${tabName}'"]`);
        switchDashTab(tabName, navItem || null);
    }
});


/* ════════════════════════════════════════
   TAB SWITCH
════════════════════════════════════════ */
function switchDashTab(name, el) {
    document.querySelectorAll('.dash-tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.sidebar-nav-item').forEach(i => i.classList.remove('active'));

    const panel = document.getElementById('dash-' + name);
    if (panel) panel.style.display = 'block';
    if (el)    el.classList.add('active');

    const url = new URL(window.location.href);
    if (name === 'overview') {
        url.searchParams.delete('tab');
    } else {
        url.searchParams.set('tab', name);
    }
    history.replaceState(null, '', url.toString());
}


/* ════════════════════════════════════════
   ORDER FILTER
════════════════════════════════════════ */
function filterOrders(status, chip) {
    document.querySelectorAll('.filter-chips .chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    document.querySelectorAll('#orders-list .queue-item').forEach(item => {
        item.style.display = (status === 'all' || item.dataset.status === status)
            ? 'flex'
            : 'none';
    });
}


/* ════════════════════════════════════════
   SEND FILE MODAL
════════════════════════════════════════ */
function openSendModal(orderId, phase) {
    document.getElementById('sendOrderId').value = orderId;
    document.getElementById('sendModalSub').textContent =
        'Upload hasil ' + (phase === 'sketch' ? 'Sketch' : 'Coloring') + ' untuk dikirim ke client';
    document.getElementById('sendModal').classList.add('open');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.remove('open');
    const fileInput = document.getElementById('resultFile');
    if (fileInput) fileInput.value = '';
    const uploadText = document.getElementById('uploadText');
    if (uploadText) {
        uploadText.textContent = 'Klik untuk upload hasil kerja';
        uploadText.className   = '';
    }
}

function showFileName(input) {
    const name       = input.files[0]?.name || 'Klik untuk upload hasil kerja';
    const uploadText = document.getElementById('uploadText');
    if (uploadText) {
        uploadText.textContent = name;
        uploadText.className   = 'file-selected';
    }
}


/* ════════════════════════════════════════
   REVIEW MODAL
════════════════════════════════════════ */
function openReviewModal(orderId, clientName) {
    document.getElementById('reviewOrderId').value = orderId;
    document.getElementById('reviewModalSub').textContent = 'Beri penilaian untuk ' + clientName;
    document.getElementById('reviewModal').classList.add('open');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('open');
}


/* ════════════════════════════════════════
   GLOBAL EXPORTS
════════════════════════════════════════ */
window.switchDashTab   = switchDashTab;
window.filterOrders    = filterOrders;
window.openSendModal   = openSendModal;
window.closeSendModal  = closeSendModal;
window.showFileName    = showFileName;
window.openReviewModal = openReviewModal;
window.closeReviewModal = closeReviewModal;
