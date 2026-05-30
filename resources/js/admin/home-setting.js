// resources/js/admin/home-setting.js

document.addEventListener('DOMContentLoaded', () => {

    // ── Live text preview ──────────────────────────────────────────
    // Input/textarea dengan data-preview="elementId" akan update elemen tersebut live
    document.querySelectorAll('[data-preview]').forEach(input => {
        const targetId = input.dataset.preview;
        const target = document.getElementById(targetId);
        if (!target) return;

        const update = () => { target.textContent = input.value; };
        input.addEventListener('input', update);
        update(); // inisialisasi
    });

    // ── Color picker sync ──────────────────────────────────────────
    // data-color="banner1" → syncs banner1_color_text ↔ banner1_color_picker
    //                         dan update banner1_preview_box + banner1_card background

    function setupColorSync(prefix) {
        const textInput  = document.getElementById(`${prefix}_color_text`);
        const picker     = document.getElementById(`${prefix}_color_picker`);
        const previewBox = document.getElementById(`${prefix}_preview_box`);
        const card       = document.getElementById(`${prefix}_card`);

        if (!textInput || !picker) return;

        const applyColor = (hex) => {
            if (!/^#[0-9A-Fa-f]{6}$/.test(hex)) return;
            picker.value = hex;
            if (previewBox) previewBox.style.background = hex;
            if (card)       card.style.background = hex;
        };

        // Klik kotak preview → buka color picker
        if (previewBox) {
            previewBox.addEventListener('click', () => picker.click());
        }

        // Color picker berubah → update text input + preview
        picker.addEventListener('input', () => {
            textInput.value = picker.value;
            applyColor(picker.value);
        });

        // Text input berubah → update picker + preview
        textInput.addEventListener('input', () => {
            applyColor(textInput.value);
        });
    }

    setupColorSync('banner1');
    setupColorSync('banner2');
});
