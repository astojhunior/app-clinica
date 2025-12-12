// public/js/login-form.js

document.addEventListener('DOMContentLoaded', () => {
    // Focus opcional en email
    const emailInput = document.getElementById('email');
    if (emailInput && (document.activeElement === document.body || !document.activeElement)) {
        emailInput.focus();
    }

    // ====== Toggle contraseña principal (id="password") ======
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    const iconEye = document.getElementById('icon-eye');
    const iconEyeOff = document.getElementById('icon-eye-off');

    if (passwordInput && toggleBtn && iconEye && iconEyeOff) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            iconEye.classList.toggle('hidden', !isHidden);
            iconEyeOff.classList.toggle('hidden', isHidden);
        });
    }

    // ====== Toggle confirmación (id="password_confirmation") ======
    const passConfInput = document.getElementById('password_confirmation');
    const toggleConfBtn = document.getElementById('togglePasswordConfirm');
    const iconEyeConf = document.getElementById('icon-eye-confirm');
    const iconEyeOffConf = document.getElementById('icon-eye-off-confirm');

    if (passConfInput && toggleConfBtn && iconEyeConf && iconEyeOffConf) {
        toggleConfBtn.addEventListener('click', () => {
            const isHidden = passConfInput.type === 'password';
            passConfInput.type = isHidden ? 'text' : 'password';
            iconEyeConf.classList.toggle('hidden', !isHidden);
            iconEyeOffConf.classList.toggle('hidden', isHidden);
        });
    }
});
