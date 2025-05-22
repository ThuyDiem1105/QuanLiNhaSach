const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('errorMessage');

loginForm.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const validUsername = 'admin';
    const validPassword = '123456';

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username === validUsername && password === validPassword)
        window.location.href = './admin/home.html';
    else
        errorMessage.style.display = 'block';
})