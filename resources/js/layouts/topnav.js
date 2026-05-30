document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('aneris-search-input')?.focus();
    }
});

function toggleTnProfileMenu(e) {
    e.stopPropagation();
    const trigger = document.getElementById('tnProfileTrigger');
    const dropdown = document.getElementById('tnProfileDropdown');
    const isOpen = dropdown.classList.contains('open');
    isOpen ? closeTnProfileMenu() : (
        trigger.classList.add('open'),
        trigger.setAttribute('aria-expanded', 'true'),
        dropdown.classList.add('open')
    );
}

function closeTnProfileMenu() {
    const t = document.getElementById('tnProfileTrigger');
    const d = document.getElementById('tnProfileDropdown');
    if (!t || !d) return;
    t.classList.remove('open');
    t.setAttribute('aria-expanded', 'false');
    d.classList.remove('open');
}
document.addEventListener('click', e => {
    const wrap = document.getElementById('tnProfileWrap');
    if (wrap && !wrap.contains(e.target)) closeTnProfileMenu();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeTnProfileMenu();
});

window.toggleTnProfileMenu = toggleTnProfileMenu;
window.closeTnProfileMenu = closeTnProfileMenu;
