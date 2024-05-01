var btnLogin = document.getElementById('do-login');
var idLogin = document.getElementById('login');
var username = document.getElementById('username');
var loginForm = document.getElementById('loginForm');

btnLogin.onclick = function(){
    idLogin.innerHTML = '<p>We\'re happy to see you again, </p><h1>' + username.value + '</h1>';
    
    // Wait for 2 seconds and then redirect
    setTimeout(function() {
        loginForm.submit(); // Submit the form, which will redirect to login.php
    }, 2000);
}