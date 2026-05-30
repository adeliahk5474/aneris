function showForm(type) {
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(type).classList.add('active');
    document.querySelectorAll('.auth-tab')[type === 'login' ? 0 : 1].classList.add('active');

    // Update visual panel text
    const headline = document.getElementById('visual-headline');
    const sub = document.getElementById('visual-sub');
    const avatarText = document.getElementById('visual-avatar-text');

    if (type === 'login') {
        headline.textContent = 'Creative Exchange';
        sub.textContent = "The world's premier destination for professional creator services. Discover, collaborate, and bring your vision to life.";
        avatarText.textContent = 'Joined by 10k+ artists';
    } else {
        headline.textContent = 'Creative Exchange';
        sub.textContent = 'The exclusive digital gallery for professional services and artist discovery.';
        avatarText.textContent = 'Joined by 10k+ artists';
    }
}

function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function selectIdentity(role) {
    document.getElementById('card-artist').classList.toggle('selected', role === 'artist');
    document.getElementById('card-client').classList.toggle('selected', role === 'client');
}

window.showForm = showForm;
window.togglePass = togglePass;
window.selectIdentity = selectIdentity;
