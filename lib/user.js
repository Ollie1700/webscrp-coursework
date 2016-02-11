var 
    loginFormContainer = document.getElementById("user-login-form-container"),
    
    registerFormContainer = document.getElementById("user-register-form-container"),
    
    loginFormToggle = document.getElementById("log-in-form-toggle"),
    
    registerFormToggle = document.getElementById("register-form-toggle"),
    
    toggleLoginForm = function(display) {
        
        loginFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleRegisterForm = function(display) {
        
        registerFormContainer.style.display = (display) ? 'block' : 'none';
        
    };

// When the 'Log In' button is pressed, make the log in form display
loginFormToggle.addEventListener("click", function() {
    toggleLoginForm(true);
    toggleRegisterForm(false);
});

// When the 'Register' button is pressed, make the register form display
registerFormToggle.addEventListener("click", function() {
    toggleLoginForm(false);
    toggleRegisterForm(true);
});