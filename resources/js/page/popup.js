const cfg = window.popupPage || {};

function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
}

function previewSlot(input, slotId) {
    if (!input.files || !input.files[0]) return;
    const slot = document.getElementById(slotId);
    const reader = new FileReader();
    reader.onload = e => {
        let img = slot.querySelector('img.pv');
        if (!img) {
            img = document.createElement('img');
            img.className = 'pv';
            img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:10px;';
            slot.appendChild(img);
        }
        img.src = e.target.result;
        slot.classList.add('has-img');
    };
    reader.readAsDataURL(input.files[0]);
}

function clearSlot(e, slotId, fileId) {
    e.stopPropagation();
    const slot = document.getElementById(slotId);
    const img = slot.querySelector('img.pv');
    if (img) img.remove();
    slot.classList.remove('has-img');
    document.getElementById(fileId).value = '';
}

let addonIndex = 2;

function addAddon() {
    const i = addonIndex++;
    const row = document.createElement('div');
    row.className = 'addon-row';
    row.innerHTML = `
            <input type="text" name="addons[${i}][name]" class="afield" placeholder="Add-on name">
            <input type="text" name="addons[${i}][description]" class="afield" placeholder="Short description">
            <div class="apbox"><span class="appfx">Rp</span><input type="number" name="addons[${i}][price]" class="apinp" placeholder="0" min="0" step="1000"></div>
            <button type="button" class="arm" onclick="removeAddon(this)"><i class="bi bi-trash3"></i></button>
        `;
    document.getElementById('addon-list').appendChild(row);
    row.querySelector('input').focus();
}

function removeAddon(btn) {
    btn.closest('.addon-row').remove();
}

function saveDraft() {
    document.getElementById('commStatus').value = 'inactive';
    document.getElementById('commForm').submit();
}

function countChars(el, counterId) {
    const counter = document.getElementById(counterId);
    if (counter) counter.textContent = el.value.length;
}

window.switchTab = switchTab;
window.previewSlot = previewSlot;
window.clearSlot = clearSlot;
window.addAddon = addAddon;
window.removeAddon = removeAddon;
window.saveDraft = saveDraft;
window.countChars = countChars;

if (cfg.switchToCommissionTab) {
    const btns = document.querySelectorAll('.tab-btn');
    if (btns[1]) switchTab('commission', btns[1]);
}
