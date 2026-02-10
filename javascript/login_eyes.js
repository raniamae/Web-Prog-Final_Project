// Select eyes and pupils
const leftEye = document.getElementById('left-eye');
const rightEye = document.getElementById('right-eye');
const leftPupil = leftEye.querySelector('.pupil');
const rightPupil = rightEye.querySelector('.pupil');

// Select your inputs
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');

function movePupil(eye, pupil, e) {
    const rect = eye.getBoundingClientRect();
    const eyeX = rect.left + rect.width / 2;
    const eyeY = rect.top + rect.height / 2;
    const dx = e.clientX - eyeX;
    const dy = e.clientY - eyeY;
    const distance = Math.min(Math.sqrt(dx*dx + dy*dy), 15); // limit movement
    const angle = Math.atan2(dy, dx);
    pupil.style.left = 50 + distance * Math.cos(angle) / (rect.width/2) * 50 + '%';
    pupil.style.top = 50 + distance * Math.sin(angle) / (rect.height/2) * 50 + '%';
}

// Eyes follow mouse when typing in username
usernameInput.addEventListener('input', () => {
    document.addEventListener('mousemove', (e) => {
        movePupil(leftEye, leftPupil, e);
        movePupil(rightEye, rightPupil, e);
    });
});

// Eyes close when typing in password
passwordInput.addEventListener('input', () => {
    leftEye.classList.add('closed');
    rightEye.classList.add('closed');
});

// Open eyes when leaving password field
passwordInput.addEventListener('blur', () => {
    leftEye.classList.remove('closed');
    rightEye.classList.remove('closed');
});