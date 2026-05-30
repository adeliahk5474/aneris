function toggleOnProfileMenu(e) {
    e.stopPropagation();
    const trigger = document.getElementById('onProfileTrigger');
    const dropdown = document.getElementById('onProfileDropdown');
    const isOpen = dropdown.classList.contains('open');

    if (isOpen) {
        closeOnProfileMenu();
    } else {
        trigger.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
        dropdown.classList.add('open');
    }
}

function closeOnProfileMenu() {
    const trigger = document.getElementById('onProfileTrigger');
    const dropdown = document.getElementById('onProfileDropdown');
    if (!trigger || !dropdown) return;
    trigger.classList.remove('open');
    trigger.setAttribute('aria-expanded', 'false');
    dropdown.classList.remove('open');
}

document.addEventListener('click', function (e) {
    const wrap = document.getElementById('onProfileWrap');
    if (wrap && !wrap.contains(e.target)) closeOnProfileMenu();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeOnProfileMenu();
});

window.toggleOnProfileMenu = toggleOnProfileMenu;
window.closeOnProfileMenu = closeOnProfileMenu;
