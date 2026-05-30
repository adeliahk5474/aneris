// resources/js/pages/verification/create.js

// ── STATE ──
let selectedFiles = [];
const MAX_FILES   = 10;
const MAX_BYTES   = 20 * 1024 * 1024; // 20MB per file

// ── ELEMENTS ──
const dropZone    = document.getElementById('fileDropZone');
const fileInput   = document.getElementById('portfolioFiles');
const fileList    = document.getElementById('fileList');
const fileCount   = document.getElementById('fileCountInfo');
const btnBrowse   = document.getElementById('btnBrowse');
const btnSubmit   = document.getElementById('btnSubmit');
const submitText  = document.getElementById('submitText');
const submitLoad  = document.getElementById('submitLoading');
const socialWrap  = document.getElementById('socialLinks');
const btnAddLink  = document.getElementById('btnAddLink');
const verifForm   = document.getElementById('verif-form');
const declareChk  = document.getElementById('declarationCheck');

// ── BROWSE ──
btnBrowse?.addEventListener('click', () => fileInput?.click());
dropZone?.addEventListener('click', (e) => {
    if (
        e.target === dropZone ||
        e.target.classList.contains('file-drop-text') ||
        e.target.classList.contains('file-drop-sub') ||
        e.target.classList.contains('file-drop-formats')
    ) {
        fileInput?.click();
    }
});

// ── DRAG & DROP ──
dropZone?.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone?.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone?.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    addFiles([...e.dataTransfer.files]);
});

// ── FILE INPUT CHANGE ──
fileInput?.addEventListener('change', () => {
    addFiles([...fileInput.files]);
    fileInput.value = '';
});

// ── ADD FILES ──
function addFiles(newFiles) {
    for (const file of newFiles) {
        if (selectedFiles.length >= MAX_FILES) break;
        if (!file.type.startsWith('image/')) {
            alert(`File "${file.name}" bukan gambar dan dilewati.`);
            continue;
        }
        if (file.size > MAX_BYTES) {
            alert(`File "${file.name}" melebihi 20MB dan dilewati.`);
            continue;
        }
        const isDup = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDup) selectedFiles.push(file);
    }
    renderFileList();
    updateSubmitState();
}

// ── RENDER FILE LIST ──
function renderFileList() {
    if (!fileList) return;
    fileList.innerHTML = '';

    selectedFiles.forEach((file, idx) => {
        const kb = file.size < 1024 * 1024
            ? Math.round(file.size / 1024) + 'KB'
            : (file.size / 1024 / 1024).toFixed(1) + 'MB';

        const item = document.createElement('div');
        item.className = 'file-item';

        const thumb = document.createElement('img');
        thumb.className = 'file-item-thumb';
        thumb.alt = file.name;
        const reader = new FileReader();
        reader.onload = (e) => { thumb.src = e.target.result; };
        reader.readAsDataURL(file);

        item.innerHTML = `
            <div class="file-item-info">
                <div class="file-item-name">${escHtml(file.name)}</div>
                <div class="file-item-size">${kb}</div>
            </div>
            <button type="button" class="file-item-remove" title="Hapus" data-idx="${idx}">
                <i class="bi bi-x"></i>
            </button>
        `;
        item.prepend(thumb);

        item.querySelector('.file-item-remove').addEventListener('click', () => {
            selectedFiles.splice(idx, 1);
            renderFileList();
            updateSubmitState();
        });

        fileList.appendChild(item);
    });

    // Count info
    const n = selectedFiles.length;
    if (fileCount) {
        if (n === 0) {
            fileCount.textContent = '';
            fileCount.className   = 'file-count-info';
        } else if (n < 3) {
            fileCount.textContent = `${n} file dipilih — butuh minimal 3`;
            fileCount.className   = 'file-count-info warn';
        } else {
            fileCount.textContent = `${n} file dipilih ✓`;
            fileCount.className   = 'file-count-info ok';
        }
    }
}

// ── SUBMIT STATE ──
function updateSubmitState() {
    if (!btnSubmit) return;
    const hasFiles   = selectedFiles.length >= 3;
    const hasDeclare = declareChk?.checked ?? false;
    btnSubmit.disabled = !(hasFiles && hasDeclare);
}

declareChk?.addEventListener('change', updateSubmitState);

// ── SOCIAL LINKS ──
let linkCount = 1;
const MAX_LINKS = 5;

function detectPlatform(input, idx) {
    const val  = input.value.toLowerCase();
    const icon = document.getElementById('platform_' + idx);
    if (!icon) return;

    const map = [
        { match: 'instagram',  cls: 'bi-instagram' },
        { match: 'tiktok',     cls: 'bi-tiktok'    },
        { match: 'twitter',    cls: 'bi-twitter-x' },
        { match: 'x.com',      cls: 'bi-twitter-x' },
        { match: 'youtube',    cls: 'bi-youtube'   },
        { match: 'pixiv',      cls: 'bi-image'     },
        { match: 'deviantart', cls: 'bi-palette'   },
        { match: 'artstation', cls: 'bi-brush'     },
        { match: 'behance',    cls: 'bi-behance'   },
        { match: 'facebook',   cls: 'bi-facebook'  },
    ];

    const found = map.find(m => val.includes(m.match));
    icon.innerHTML = `<i class="bi ${found ? found.cls : 'bi-link-45deg'}"></i>`;
}

btnAddLink?.addEventListener('click', () => {
    if (linkCount >= MAX_LINKS) return;
    const idx = linkCount++;
    const row = document.createElement('div');
    row.className     = 'social-link-row';
    row.dataset.index = idx;
    row.innerHTML = `
        <div class="social-link-platform" id="platform_${idx}">
            <i class="bi bi-link-45deg"></i>
        </div>
        <input type="url" name="social_media_links[]"
               placeholder="https://..."
               class="social-input"
               oninput="detectPlatform(this, ${idx})">
        <button type="button" class="btn-remove-link" onclick="removeLink(this)">
            <i class="bi bi-x"></i>
        </button>
    `;
    socialWrap?.appendChild(row);
    if (linkCount >= MAX_LINKS) btnAddLink.style.display = 'none';
});

function removeLink(btn) {
    btn.closest('.social-link-row')?.remove();
    linkCount = Math.max(1, linkCount - 1);
    if (btnAddLink) btnAddLink.style.display = '';
}

// ── SUBMIT VIA FETCH ──
// Lebih reliable untuk multi-file dibanding form submit biasa
verifForm?.addEventListener('submit', (e) => {
    e.preventDefault();

    // Validasi client-side
    if (selectedFiles.length < 3) {
        alert('Upload minimal 3 file gambar portofolio.');
        return;
    }
    if (!declareChk?.checked) {
        alert('Centang pernyataan keaslian terlebih dahulu.');
        return;
    }

    // Build FormData dari form (ambil CSRF, sosmed links, declaration)
    const formData = new FormData(verifForm);

    // Hapus portfolio_files dari FormData form (yang dari native input, mungkin kosong)
    // lalu tambahkan dari selectedFiles array
    formData.delete('portfolio_files[]');
    selectedFiles.forEach(file => {
        formData.append('portfolio_files[]', file);
    });

    // Loading state
    if (submitText) submitText.style.display = 'none';
    if (submitLoad) submitLoad.style.display = '';
    if (btnSubmit)  btnSubmit.disabled = true;

    fetch(verifForm.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(async (response) => {
        const contentType = response.headers.get('content-type') ?? '';

        // Response JSON = dari AJAX handler kita
        if (contentType.includes('application/json')) {
            const json = await response.json();
            if (json.success) {
                window.location.href = json.redirect;
            } else {
                showError(json.message ?? 'Terjadi kesalahan.');
                resetButton();
            }
            return;
        }

        // Response HTML = validasi error dari Laravel (redirect back with errors)
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        // Render HTML error page
        const html = await response.text();
        document.open();
        document.write(html);
        document.close();
    })
    .catch(err => {
        console.error('Submit error:', err);
        showError('Terjadi kesalahan jaringan. Coba lagi.');
        resetButton();
    });
});

function resetButton() {
    if (submitText) submitText.style.display = '';
    if (submitLoad) submitLoad.style.display = 'none';
    if (btnSubmit)  btnSubmit.disabled = false;
}

function showError(msg) {
    // Cari atau buat error box
    let box = document.querySelector('.verif-alert.error');
    if (!box) {
        box = document.createElement('div');
        box.className = 'verif-alert error';
        verifForm?.before(box);
    }
    box.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${msg}`;
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ── DECLARE TOGGLE ──
function toggleDeclare(el) {
    updateSubmitState();
}

// ── ESCAPE HTML ──
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// ── EXPOSE GLOBALS ──
window.detectPlatform = detectPlatform;
window.removeLink     = removeLink;
window.toggleDeclare  = toggleDeclare;
