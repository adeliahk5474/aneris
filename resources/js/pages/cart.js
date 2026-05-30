const cfg = window.cartPage || {};

let selectedPaymentFilter = 'ewallet';

/* ── TAB SWITCHING ── */
function switchTab(name, el) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (el) el.classList.add('active');
}

/* ── PAYMENT METHOD SELECTOR ── */
function selectPayment(el) {
    document.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('active'));
    el.classList.add('active');
    selectedPaymentFilter = el.dataset.filter || 'ewallet';
}

/* ── REFERENCE IMAGES ── */
// Simpan file yang dipilih per order di sini supaya tidak hilang saat input.value dikosongkan
const pendingFiles = {};

function previewRefs(input, orderId) {
    const grid = document.getElementById('ref-grid-' + orderId);
    const addBtn = document.getElementById('ref-add-' + orderId);
    const max = 4;

    if (!pendingFiles[orderId]) pendingFiles[orderId] = [];

    Array.from(input.files).forEach(file => {
        if (grid.querySelectorAll('.ref-item').length >= max) return;

        // Simpan file ke pendingFiles supaya bisa dipakai saat triggerPayment
        pendingFiles[orderId].push(file);

        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'ref-item';
            div.dataset.fileIdx = pendingFiles[orderId].length - 1;
            div.innerHTML = `
                <img src="${e.target.result}" alt="">
                <button type="button" class="ref-remove"
                    onclick="removeRef(this, '${orderId}')">
                    <i class="bi bi-x"></i>
                </button>`;
            grid.insertBefore(div, addBtn);
            checkRefLimit(orderId);
            syncFileInput(orderId);
        };
        reader.readAsDataURL(file);
    });

    // Reset input supaya file yang sama bisa dipilih lagi
    input.value = '';
}

function removeRef(btn, orderId) {
    const item = btn.closest('.ref-item');
    const idx = parseInt(item.dataset.fileIdx);
    if (!isNaN(idx) && pendingFiles[orderId]) {
        pendingFiles[orderId][idx] = null; // mark as removed
    }
    item.remove();
    checkRefLimit(orderId);
    syncFileInput(orderId);
}

// Sync file input dengan pendingFiles yang masih aktif
function syncFileInput(orderId) {
    const input = document.getElementById('ref-input-' + orderId);
    if (!input) return;

    const validFiles = (pendingFiles[orderId] || []).filter(f => f !== null);
    const dt = new DataTransfer();
    validFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function checkRefLimit(orderId) {
    const grid = document.getElementById('ref-grid-' + orderId);
    const addBtn = document.getElementById('ref-add-' + orderId);
    if (addBtn) {
        addBtn.style.display = grid.querySelectorAll('.ref-item').length >= 4 ? 'none' : '';
    }
}

/* ── SAVE BRIEF (ajax sebelum bayar) ── */
async function saveBrief(orderId) {
    const form = document.getElementById('brief-form-' + orderId);
    if (!form) return;

    // Sync files terbaru ke input sebelum submit
    syncFileInput(orderId);

    const formData = new FormData(form);

    try {
        await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    } catch (e) {
        // Lanjut bayar meski gagal simpan brief
        console.warn('Brief save failed, continuing to payment:', e);
    }
}

/* ── MIDTRANS SNAP PAYMENT ── */
async function triggerPayment(orderId) {
    const btn = document.getElementById('btn-pay-' + orderId);
    const overlay = document.getElementById('payment-overlay');

    btn.disabled = true;
    btn.style.opacity = '.5';
    overlay.classList.add('active');

    // Auto-save brief dulu sebelum bayar
    await saveBrief(orderId);

    try {
        const res = await fetch(cfg.paymentCheckoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                payment_filter: selectedPaymentFilter,
            }),
        });

        const data = await res.json();
        overlay.classList.remove('active');

        if (!res.ok || data.error) {
            showFlash('error', data.error ?? 'Gagal memuat pembayaran. Coba lagi.');
            btn.disabled = false;
            btn.style.opacity = '1';
            return;
        }

        window.snap.pay(data.snap_token, {
            onSuccess: async function () {
                showFlash('ok', 'Pembayaran diterima, memverifikasi…');

                let tries = 0;
                const poll = setInterval(async () => {
                    tries++;
                    try {
                        const r = await fetch('/payment/verify-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ order_id: orderId }),
                        });
                        const d = await r.json();
                        if (d.is_paid || tries >= 15) {
                            clearInterval(poll);
                            window.location.href = cfg.cartStatusUrl;
                        }
                    } catch {
                        if (tries >= 15) {
                            clearInterval(poll);
                            window.location.href = cfg.cartStatusUrl;
                        }
                    }
                }, 1000);
            },
            onPending: function () {
                showFlash('ok', 'Menunggu konfirmasi pembayaran…');
                setTimeout(() => window.location.reload(), 2000);
            },
            onError: function () {
                showFlash('error', 'Pembayaran gagal. Silakan coba lagi.');
                btn.disabled = false;
                btn.style.opacity = '1';
            },
            onClose: function () {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });

    } catch (err) {
        overlay.classList.remove('active');
        showFlash('error', 'Koneksi bermasalah. Coba lagi.');
        btn.disabled = false;
        btn.style.opacity = '1';
    }
}

/* ── FLASH HELPER ── */
function showFlash(type, msg) {
    document.querySelectorAll('.flash-dynamic').forEach(el => el.remove());

    const div = document.createElement('div');
    div.className = (type === 'ok' ? 'flash-ok' : 'flash-err') + ' flash-dynamic';
    div.innerHTML = `<i class="bi bi-${type === 'ok' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i> ${msg}`;

    const summary = document.querySelector('.summary-card');
    if (summary) summary.insertAdjacentElement('beforebegin', div);
    else document.querySelector('.panel-inner')?.prepend(div);

    setTimeout(() => div.remove(), 5000);
}

/* ── AUTO-ACTIVATE TAB FROM URL ── */
const urlParams = new URLSearchParams(window.location.search);
const urlTab = urlParams.get('tab');
const urlOrder = urlParams.get('order_id');

if (urlTab && ['checkout', 'status'].includes(urlTab)) {
    const idx = { checkout: 0, status: 1 }[urlTab];
    switchTab(urlTab, document.querySelectorAll('.tab-item')[idx]);
}

if (urlOrder && (urlTab === 'checkout' || !urlTab)) {
    setTimeout(() => {
        const card = document.getElementById('co-card-' + urlOrder);
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 200);
}

window.switchTab = switchTab;
window.selectPayment = selectPayment;
window.previewRefs = previewRefs;
window.removeRef = removeRef;
window.checkRefLimit = checkRefLimit;
window.triggerPayment = triggerPayment;
window.showFlash = showFlash;
