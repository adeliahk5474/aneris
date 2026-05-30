/* resources/js/dashboards/artist.js */

/* ── CHART ──────────────────────────────────── */
const cfg = window.artistDashboard || {};

document.addEventListener('DOMContentLoaded', () => {

    /* ── Revenue Chart ── */
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

    /* ── Modal backdrop click to close ── */
    ['sendModal', 'reviewModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', e => {
            if (e.target === el) el.classList.remove('open');
        });
    });

    /* ── Portfolio upload drag & drop ── */
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

            const input = document.getElementById('portfolioFiles');
            if (!input) return;

            // Transfer dropped files ke input
            const dt    = e.dataTransfer;
            const files = dt.files;

            // DataTransfer untuk set files ke input
            const transfer = new DataTransfer();
            Array.from(files).forEach(f => transfer.items.add(f));
            input.files = transfer.files;
            updatePortfolioPreview(input);
        });
    }

    /* ── Auto-open tab dari URL query ?tab=xxx ── */
    const params  = new URLSearchParams(window.location.search);
    const tabName = params.get('tab');
    if (tabName) {
        // Cari nav item yang cocok
        const navItem = document.querySelector(`.sidebar-nav-item[onclick*="'${tabName}'"]`);
        switchDashTab(tabName, navItem || null);
    }

    /* ── Auto-show flash session di tab yang benar ── */
    // Jika ada alert-success/error di DOM dan tab portfolio aktif, scroll ke atas
    const successAlert = document.querySelector('.alert-success');
    const errorAlert   = document.querySelector('.alert-error');
    if ((successAlert || errorAlert) && tabName === 'portfolio') {
        const panel = document.getElementById('dash-portfolio');
        if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ── Inisialisasi remove button sosmed (baris pertama) ── */
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

    // Update URL tanpa reload supaya browser back button tetap waras
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
   PORTFOLIO UPLOAD PREVIEW
════════════════════════════════════════ */
function updatePortfolioPreview(input) {
    const preview = document.getElementById('portfolioPreview');
    if (!preview) return;

    preview.innerHTML = '';
    const files    = Array.from(input.files);
    const maxFiles = 10;

    // Validasi jumlah file
    if (files.length > maxFiles) {
        showVerifAlert(`Maksimal ${maxFiles} file. Kamu memilih ${files.length} file.`, 'error');
        input.value = '';
        return;
    }

    if (files.length < 3) {
        showVerifAlert('Pilih minimal 3 file portofolio.', 'warning');
    } else {
        clearVerifAlert();
    }

    files.forEach((file, i) => {
        const item = document.createElement('div');
        item.className = 'verif-file-item';

        const isImage = file.type.startsWith('image/');

        if (isImage) {
            const img    = document.createElement('img');
            img.src      = URL.createObjectURL(file);
            img.onload   = () => URL.revokeObjectURL(img.src); // cleanup memory
            item.appendChild(img);
        } else {
            const icon       = document.createElement('i');
            const ext        = file.name.split('.').pop().toLowerCase();
            icon.className   = ext === 'pdf'
                ? 'bi bi-file-earmark-pdf'
                : 'bi bi-file-earmark-richtext';
            icon.style.cssText = 'font-size:24px; color:var(--accent);';
            item.appendChild(icon);
        }

        const sizeMb = (file.size / 1024 / 1024).toFixed(1);
        const name   = document.createElement('span');
        name.textContent = (file.name.length > 14
            ? file.name.substring(0, 11) + '...'
            : file.name) + ` (${sizeMb}MB)`;
        item.appendChild(name);

        preview.appendChild(item);
    });

    // Counter
    const counter       = document.createElement('div');
    counter.className   = 'verif-file-counter';
    counter.style.cssText = 'grid-column:1/-1; font-size:12px; color:var(--muted); margin-top:4px;';
    counter.textContent = `${files.length}/${maxFiles} file dipilih`;
    preview.appendChild(counter);
}


/* ════════════════════════════════════════
   SOCIAL LINK ROWS
════════════════════════════════════════ */
function addSocialRow() {
    const container = document.getElementById('socialLinksContainer');
    if (!container) return;

    const rows = container.querySelectorAll('.verif-social-row');
    if (rows.length >= 5) {
        showVerifAlert('Maksimal 5 link sosial media.', 'warning');
        return;
    }

    const row       = document.createElement('div');
    row.className   = 'verif-social-row';
    row.innerHTML   = `
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
   VERIF ALERT HELPERS (inline di form)
════════════════════════════════════════ */
function showVerifAlert(msg, type = 'error') {
    clearVerifAlert();
    const form  = document.querySelector('.verif-form');
    if (!form) return;

    const div   = document.createElement('div');
    div.id      = 'verifInlineAlert';
    div.className = type === 'error' ? 'alert-error' : 'alert-warning';

    // warning style tidak ada di CSS, fallback ke kuning
    if (type === 'warning') {
        div.style.cssText = 'background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.2);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--yellow);';
    }

    div.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${msg}`;
    form.prepend(div);
}

function clearVerifAlert() {
    document.getElementById('verifInlineAlert')?.remove();
}


/* ════════════════════════════════════════
   GLOBAL EXPORTS
════════════════════════════════════════ */
window.switchDashTab         = switchDashTab;
window.filterOrders          = filterOrders;
window.openSendModal         = openSendModal;
window.closeSendModal        = closeSendModal;
window.showFileName          = showFileName;
window.openReviewModal       = openReviewModal;
window.closeReviewModal      = closeReviewModal;
window.updatePortfolioPreview = updatePortfolioPreview;
window.addSocialRow          = addSocialRow;
window.removeSocialRow       = removeSocialRow;
window.updateRemoveButtons   = updateRemoveButtons;
