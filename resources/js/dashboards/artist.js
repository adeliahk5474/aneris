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
            if (e.target === el) closeModal(id);
        });
    });

    // ── File input change listener (single, no duplicate) ──
    const resultFile = document.getElementById('resultFile');
    if (resultFile) {
        resultFile.addEventListener('change', function () {
            const name = this.files[0]?.name || null;
            const uploadText = document.getElementById('uploadText');
            if (uploadText) {
                uploadText.textContent = name || 'Klik untuk upload hasil kerja';
                uploadText.className = name ? 'file-selected' : '';
            }
        });
    }

    // ── Upload area click → trigger file input ──────
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        uploadArea.addEventListener('click', () => {
            const fi = document.getElementById('resultFile');
            if (fi) fi.click();
        });
    }

    // ── Auto-open tab dari URL query ?tab=xxx ──────
    const params  = new URLSearchParams(window.location.search);
    const tabName = params.get('tab');
    if (tabName) {
        const navItem = document.querySelector(`.sidebar-nav-item[data-tab="${tabName}"]`);
        switchDashTab(tabName, navItem || null);
    }
});


/* ════════════════════════════════════════
   MODAL HELPERS
════════════════════════════════════════ */
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}


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

    // Reset file input & label
    const fileInput  = document.getElementById('resultFile');
    const uploadText = document.getElementById('uploadText');
    if (fileInput)  fileInput.value = '';
    if (uploadText) {
        uploadText.textContent = 'Klik untuk upload hasil kerja';
        uploadText.className   = '';
    }

    openModal('sendModal');
}

function closeSendModal() {
    closeModal('sendModal');

    // Reset file input & label
    const fileInput  = document.getElementById('resultFile');
    const uploadText = document.getElementById('uploadText');
    if (fileInput)  fileInput.value = '';
    if (uploadText) {
        uploadText.textContent = 'Klik untuk upload hasil kerja';
        uploadText.className   = '';
    }
}


/* ════════════════════════════════════════
   REVIEW MODAL
════════════════════════════════════════ */
function openReviewModal(orderId, clientName) {
    document.getElementById('reviewOrderId').value = orderId;
    document.getElementById('reviewModalSub').textContent = 'Beri penilaian untuk ' + clientName;
    openModal('reviewModal');
}

function closeReviewModal() {
    closeModal('reviewModal');
}


/* ════════════════════════════════════════
   GLOBAL EXPORTS
════════════════════════════════════════ */
window.switchDashTab    = switchDashTab;
window.filterOrders     = filterOrders;
window.openSendModal    = openSendModal;
window.closeSendModal   = closeSendModal;
window.openReviewModal  = openReviewModal;
window.closeReviewModal = closeReviewModal;
