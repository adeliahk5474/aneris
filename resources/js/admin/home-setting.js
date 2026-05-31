// resources/js/admin/home-setting.js

document.addEventListener('DOMContentLoaded', () => {

    // ── Live text preview ──────────────────────────────────────────
    document.querySelectorAll('[data-preview]').forEach(input => {
        const target = document.getElementById(input.dataset.preview);
        if (!target) return;
        input.addEventListener('input', () => { target.textContent = input.value; });
    });

    // ── Preview gambar sebelum upload ──────────────────────────────
    window.previewZone = function(input, zoneId, previewId) {
        const preview = document.getElementById(previewId);
        const zone    = document.getElementById(zoneId);
        if (!preview || !input.files[0]) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            // Tampilkan preview gambar baru
            preview.src = e.target.result;
            preview.style.display = 'block';

            // Update live preview banner jika ada
            const card = zone?.closest('.hs-card')?.querySelector('.hs-preview');
            if (card) {
                card.style.backgroundImage = `url(${e.target.result})`;
                card.style.backgroundSize  = 'cover';
                card.style.backgroundPosition = 'center';
            }

            // Ubah teks zone
            const label = zone?.querySelector('span:not(.hs-upload-hint)');
            if (label) label.textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    };

    // ── Drag & drop ────────────────────────────────────────────────
    document.querySelectorAll('.hs-upload-zone').forEach(zone => {
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            const fileInput = zone.querySelector('input[type="file"]');
            if (!fileInput || !e.dataTransfer.files[0]) return;

            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    });
});
