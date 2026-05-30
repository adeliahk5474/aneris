// ── FILE UPLOAD ──
const fileInput    = document.getElementById('portfolioFiles');
const fileDropZone = document.getElementById('fileDropZone');
const fileList     = document.getElementById('fileList');
const btnBrowse    = document.getElementById('btnBrowse');

let selectedFiles = [];

const FILE_ICONS = {
    jpg: 'bi-file-image', jpeg: 'bi-file-image', png: 'bi-file-image',
    gif: 'bi-file-image', webp: 'bi-file-image',
    pdf: 'bi-file-pdf',
    zip: 'bi-file-zip', rar: 'bi-file-zip',
    mp4: 'bi-file-play', mov: 'bi-file-play', avi: 'bi-file-play',
};

function formatSize(bytes) {
    if (bytes < 1024)        return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function getIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    return FILE_ICONS[ext] || 'bi-file-earmark';
}

function renderFileList() {
    if (!fileList) return;
    fileList.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const item = document.createElement('div');
        item.className = 'file-item';
        item.innerHTML = `
            <i class="bi ${getIcon(file.name)} file-item-icon"></i>
            <div class="file-item-info">
                <div class="file-item-name">${file.name}</div>
                <div class="file-item-size">${formatSize(file.size)}</div>
            </div>
            <button type="button" class="file-item-remove" data-index="${i}">
                <i class="bi bi-x"></i>
            </button>
        `;
        fileList.appendChild(item);
    });

    // Sync ke input
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    if (fileInput) fileInput.files = dt.files;
}

function addFiles(files) {
    const arr = [...files];
    arr.forEach(f => {
        if (selectedFiles.length < 10) selectedFiles.push(f);
    });
    renderFileList();
}

if (btnBrowse)    btnBrowse.addEventListener('click', () => fileInput?.click());
if (fileDropZone) fileDropZone.addEventListener('click', (e) => {
    if (e.target === fileDropZone || e.target.classList.contains('file-drop-text') ||
        e.target.classList.contains('file-drop-sub') || e.target.classList.contains('file-drop-icon')) {
        fileInput?.click();
    }
});
if (fileInput) fileInput.addEventListener('change', () => addFiles(fileInput.files));

if (fileDropZone) {
    fileDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileDropZone.classList.add('dragover');
    });
    fileDropZone.addEventListener('dragleave', () => fileDropZone.classList.remove('dragover'));
    fileDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        fileDropZone.classList.remove('dragover');
        addFiles(e.dataTransfer.files);
    });
}

document.addEventListener('click', (e) => {
    if (e.target.closest('.file-item-remove')) {
        const idx = parseInt(e.target.closest('.file-item-remove').dataset.index);
        selectedFiles.splice(idx, 1);
        renderFileList();
    }
});

// ── SOCIAL MEDIA LINKS ──
const socialLinks = document.getElementById('socialLinks');
const btnAddLink  = document.getElementById('btnAddLink');
const MAX_LINKS   = 5;

function updateRemoveButtons() {
    if (!socialLinks) return;
    const rows = socialLinks.querySelectorAll('.social-link-row');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.btn-remove-link');
        if (btn) btn.style.display = rows.length > 1 ? 'flex' : 'none';
    });
    if (btnAddLink) {
        btnAddLink.style.display = rows.length >= MAX_LINKS ? 'none' : 'flex';
    }
}

if (btnAddLink) {
    btnAddLink.addEventListener('click', () => {
        if (!socialLinks) return;
        const rows = socialLinks.querySelectorAll('.social-link-row');
        if (rows.length >= MAX_LINKS) return;

        const row = document.createElement('div');
        row.className = 'social-link-row';
        row.innerHTML = `
            <input type="url" name="social_media_links[]"
                   placeholder="https://..." class="social-input">
            <button type="button" class="btn-remove-link">
                <i class="bi bi-x"></i>
            </button>
        `;
        socialLinks.appendChild(row);
        updateRemoveButtons();
        row.querySelector('input')?.focus();
    });
}

document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-remove-link')) {
        const row = e.target.closest('.social-link-row');
        if (row && socialLinks?.querySelectorAll('.social-link-row').length > 1) {
            row.remove();
            updateRemoveButtons();
        }
    }
});

updateRemoveButtons();

// ── SUBMIT LOADING STATE ──
const verifForm    = document.getElementById('verif-form');
const btnSubmit    = document.getElementById('btnSubmit');
const submitText   = document.getElementById('submitText');
const submitLoading = document.getElementById('submitLoading');

if (verifForm) {
    verifForm.addEventListener('submit', () => {
        if (btnSubmit)      btnSubmit.disabled = true;
        if (submitText)     submitText.style.display = 'none';
        if (submitLoading)  submitLoading.style.display = 'inline-flex';
    });
}

// ── COUNTDOWN TIMER (halaman status) ──
const countdownEl = document.getElementById('countdown');
if (countdownEl) {
    const target = new Date(countdownEl.dataset.target);

    function updateCountdown() {
        const diff = target - new Date();
        if (diff <= 0) {
            countdownEl.textContent = '— bisa submit sekarang';
            return;
        }
        const days  = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        countdownEl.textContent = `(${days} hari ${hours} jam lagi)`;
    }

    updateCountdown();
    setInterval(updateCountdown, 60000);
}
