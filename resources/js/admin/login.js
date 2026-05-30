// ── TOGGLE PASSWORD VISIBILITY ──
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');
const toggleIcon     = document.getElementById('toggleIcon');

if (togglePassword) {
    togglePassword.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleIcon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

// ── LOADING STATE SAAT SUBMIT ──
const form      = document.getElementById('admin-login-form');
const btnLogin  = document.getElementById('btnLogin');
const btnText    = document.getElementById('btnText');
const btnLoading = document.getElementById('btnLoading');

if (form) {
    form.addEventListener('submit', () => {
        btnLogin.disabled    = true;
        btnText.style.display    = 'none';
        btnLoading.style.display = 'inline-flex';
    });
}
