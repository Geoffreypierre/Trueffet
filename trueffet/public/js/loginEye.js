const visibilityToggler = document.getElementById('toggle-eye');
const passwordInput = document.getElementById('current_password');

visibilityToggler.addEventListener('click', () => {
    if (visibilityToggler.classList.contains('opened_eye')) {
        visibilityToggler.classList.remove('opened_eye');
        visibilityToggler.src = 'img/ico/closed_eye.png';
        passwordInput.setAttribute('type', 'text');
    } else {
        visibilityToggler.classList.add('opened_eye');
        visibilityToggler.src = 'img/ico/opened_eye.png';
        passwordInput.setAttribute('type', 'password');
    }
})