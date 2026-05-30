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
    if (e.target === dropZone || e.target.classList.contains('file-drop-text') ||
        e.target.classList.contains('file-drop-sub') || e.target.classList.contains('file-drop-formats')) {
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
    fileInput.value = ''; // reset supaya bisa pilih file yang sama lagi
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
        // Cek duplikat nama+ukuran
        const isDup = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDup) selectedFiles.push(file);
    }
    renderFileList();
    syncFileInput();
    updateSubmitState();
}

// ── RENDER LIST ──
function renderFileList() {
    if (!fileList) return;
    fileList.innerHTML = '';

    selectedFiles.forEach((file, idx) => {
        const kb  = file.size < 1024 * 1024
            ? Math.round(file.size / 1024) + 'KB'
            : (file.size / 1024 / 1024).toFixed(1) + 'MB';

        const item = document.createElement('div');
        item.className = 'file-item';

        // Preview thumb
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
            syncFileInput();
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

// ── SYNC FILE INPUT ──
// Karena tidak bisa set FileList secara langsung di native input,
// kita pakai DataTransfer untuk rebuild
function syncFileInput() {
    if (!fileInput) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    fileInput.files = dt.files;
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
        { match: 'instagram',  cls: 'bi-instagram',  extra: 'instagram' },
        { match: 'tiktok',     cls: 'bi-tiktok',     extra: 'tiktok'    },
        { match: 'twitter',    cls: 'bi-twitter-x',  extra: 'twitter'   },
        { match: 'x.com',      cls: 'bi-twitter-x',  extra: 'twitter'   },
        { match: 'youtube',    cls: 'bi-youtube',    extra: 'youtube'   },
        { match: 'pixiv',      cls: 'bi-image',      extra: 'pixiv'     },
        { match: 'deviantart', cls: 'bi-palette',    extra: ''          },
        { match: 'artstation', cls: 'bi-brush',      extra: ''          },
        { match: 'behance',    cls: 'bi-behance',    extra: ''          },
        { match: 'facebook',   cls: 'bi-facebook',   extra: ''          },
    ];

    const found = map.find(m => val.includes(m.match));
    icon.innerHTML = `<i class="bi ${found ? found.cls : 'bi-link-45deg'}"></i>`;
    icon.className = 'social-link-platform' + (found?.extra ? ' ' + found.extra : '');
}

btnAddLink?.addEventListener('click', () => {
    if (linkCount >= MAX_LINKS) return;
    const idx = linkCount++;
    const row = document.createElement('div');
    row.className    = 'social-link-row';
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

// ── SUBMIT LOADER ──
verifForm?.addEventListener('submit', () => {
    if (submitText)  submitText.style.display  = 'none';
    if (submitLoad)  submitLoad.style.display  = '';
    if (btnSubmit)   btnSubmit.disabled = true;
});

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
