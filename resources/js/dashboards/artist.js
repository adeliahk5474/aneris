/* resources/js/dashboards/artist.js */

/* ── CHART ──────────────────────────────────── */
const cfg = window.artistDashboard || {};

/* ══════════════════════════════════════════════════
   PORTFOLIO UPLOAD STATE (accumulate, tidak reset)
══════════════════════════════════════════════════ */
let portfolioFiles = [];          // array File objects yang terakumulasi
const MAX_PORTFOLIO = 10;

/**
 * Dipanggil dari onchange input file.
 * Accumulate file ke array, TIDAK replace.
 */
function updatePortfolioPreview(input) {
    const newFiles = Array.from(input.files);
    // Reset nilai input supaya event change bisa trigger lagi untuk file sama
    input.value = '';

    for (const file of newFiles) {
        if (portfolioFiles.length >= MAX_PORTFOLIO) break;
        // Cegah duplikat berdasarkan nama + ukuran
        const isDup = portfolioFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDup) portfolioFiles.push(file);
    }

    renderPortfolioPreview();
    syncPortfolioInput();
}

function renderPortfolioPreview() {
    const preview = document.getElementById('portfolioPreview');
    if (!preview) return;

    preview.innerHTML = '';

    portfolioFiles.forEach((file, idx) => {
        const isImage = file.type.startsWith('image/');
        const sizeMb  = file.size < 1024 * 1024
            ? Math.round(file.size / 1024) + 'KB'
            : (file.size / 1024 / 1024).toFixed(1) + 'MB';

        const item = document.createElement('div');
        item.className = 'verif-file-item';
        item.style.cssText = 'position:relative;cursor:pointer;';

        if (isImage) {
            const img = document.createElement('img');
            img.style.cssText = 'width:60px;height:60px;object-fit:cover;border-radius:4px;';
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            item.appendChild(img);
        } else {
            const icon = document.createElement('i');
            const ext  = file.name.split('.').pop().toLowerCase();
            icon.className = ext === 'pdf' ? 'bi bi-file-earmark-pdf' : 'bi bi-file-earmark-richtext';
            icon.style.cssText = 'font-size:24px;color:var(--accent);';
            item.appendChild(icon);
        }

        const name = document.createElement('span');
        const shortName = file.name.length > 14
            ? file.name.substring(0, 11) + '...'
            : file.name;
        name.textContent = shortName + ' (' + sizeMb + ')';
        name.style.cssText = 'font-size:10px;color:var(--muted);text-align:center;word-break:break-all;margin-top:4px;';
        item.appendChild(name);

        // Tombol hapus per-item
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '×';
        removeBtn.style.cssText = 'position:absolute;top:-4px;right:-4px;width:16px;height:16px;border-radius:50%;' +
            'background:var(--red);border:none;color:#fff;font-size:10px;cursor:pointer;' +
            'display:flex;align-items:center;justify-content:center;line-height:1;padding:0;';
        removeBtn.addEventListener('click', () => {
            portfolioFiles.splice(idx, 1);
            renderPortfolioPreview();
            syncPortfolioInput();
        });
        item.appendChild(removeBtn);

        preview.appendChild(item);
    });

    // Counter
    const n = portfolioFiles.length;
    let counter = preview.querySelector('.verif-file-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'verif-file-counter';
        counter.style.cssText = 'grid-column:1/-1;font-size:12px;margin-top:4px;';
        preview.appendChild(counter);
    }

    if (n === 0) {
        counter.textContent = '';
        counter.style.color = 'var(--muted)';
    } else if (n < 3) {
        counter.textContent = `${n}/${MAX_PORTFOLIO} file dipilih — butuh minimal 3`;
        counter.style.color = 'var(--yellow)';
    } else {
        counter.textContent = `${n}/${MAX_PORTFOLIO} file dipilih ✓`;
        counter.style.color = 'var(--green)';
    }
}

/**
 * Sync array portfolioFiles kembali ke FileList di input
 * supaya saat form submit semua file ikut terkirim.
 */
function syncPortfolioInput() {
    const input = document.getElementById('portfolioFiles');
    if (!input) return;

    const dt = new DataTransfer();
    portfolioFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

/* ══════════════════════════════════════
   CHART
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

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

    /* Modal backdrop click to close */
    ['sendModal', 'reviewModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', e => {
            if (e.target === el) el.classList.remove('open');
        });
    });

    /* Portfolio drop zone drag & drop */
    const dropZone = document.getElementById('portfolioDropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--accent)';
            dropZone.style.background  = 'var(--accent-dim)';
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '';
            dropZone.style.background  = '';
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.borderColor = '';
            dropZone.style.background  = '';

            const files = Array.from(e.dataTransfer.files);
            for (const file of files) {
                if (portfolioFiles.length >= MAX_PORTFOLIO) break;
                const isDup = portfolioFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDup) portfolioFiles.push(file);
            }
            renderPortfolioPreview();
            syncPortfolioInput();
        });
    }

    /* Auto-open tab dari URL query ?tab=xxx */
    const params  = new URLSearchParams(window.location.search);
    const tabName = params.get('tab');
    if (tabName) {
        const navItem = document.querySelector(`.sidebar-nav-item[onclick*="'${tabName}'"]`);
        switchDashTab(tabName, navItem || null);
    }

    updateRemoveButtons();
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
   SOCIAL LINK ROWS (di tab portfolio)
════════════════════════════════════════ */
function addSocialRow() {
    const container = document.getElementById('socialLinksContainer');
    if (!container) return;

    const rows = container.querySelectorAll('.verif-social-row');
    if (rows.length >= 5) return;

    const row     = document.createElement('div');
    row.className = 'verif-social-row';
    row.innerHTML = `
        <input type="url" name="social_media_links[]" class="form-input"
            placeholder="https://..." style="margin-bottom:0;">
        <button type="button" class="verif-remove-link" onclick="removeSocialRow(this)">
            <i class="bi bi-trash"></i>
        </button>`;
    container.appendChild(row);
    row.querySelector('input')?.focus();
    updateRemoveButtons();
}

function removeSocialRow(btn) {
    btn.closest('.verif-social-row')?.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const container = document.getElementById('socialLinksContainer');
    if (!container) return;
    const rows = container.querySelectorAll('.verif-social-row');
    rows.forEach(row => {
        const btn = row.querySelector('.verif-remove-link');
        if (btn) btn.style.display = rows.length > 1 ? 'flex' : 'none';
    });
}


/* ════════════════════════════════════════
   GLOBAL EXPORTS
════════════════════════════════════════ */
window.switchDashTab          = switchDashTab;
window.filterOrders           = filterOrders;
window.openSendModal          = openSendModal;
window.closeSendModal         = closeSendModal;
window.showFileName           = showFileName;
window.openReviewModal        = openReviewModal;
window.closeReviewModal       = closeReviewModal;
window.updatePortfolioPreview = updatePortfolioPreview;
window.addSocialRow           = addSocialRow;
window.removeSocialRow        = removeSocialRow;
window.updateRemoveButtons    = updateRemoveButtons;
