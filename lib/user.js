var 
    
    // Grab all the relevant elements

    loginFormContainer = document.getElementById("user-login-form-container"),
    
    registerFormContainer = document.getElementById("user-register-form-container"),
    
    loginFormToggle = document.getElementById("log-in-form-toggle"),
    
    registerFormToggle = document.getElementById("register-form-toggle"),
    
    profileModal = document.getElementById("profile-modal"),
    
    profileContainer = document.getElementById("profile-container"),
    
    profileModalToggle = document.getElementById("profile-modal-toggle"),
    
    closeProfileModal = document.getElementById("close-profile-modal"),
    
    updateProfileForm = document.getElementById("update-profile-form"),
    
    updateProfileFormSubmit = document.getElementById("submit-update-profile-form"),
    
    // Toggling the views of various features
    
    toggleLoginForm = function(display) {
        
        loginFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleRegisterForm = function(display) {
        
        registerFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleProfileModal = function(display) {
        
        profileModal.style.display = (display) ? 'block' : 'none';
        profileContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    // Form validation and submission
    
    updateProfile = function() {
        
        var user_id = document.getElementById("update-profile-user-id").value;
        
        Ajax('PUT', '', [], function(r) {
            
        });
        
    }

    ;

/** FORM VALIDATION AND SUBMISSION **/

if(updateProfileFormSubmit) {
    updateProfileFormSubmit.addEventListener("click", function() {
        
        updateProfile();
        
        // Stop the form from being submitted old-style
        e.preventDefault();
        return false;
        
    });
}

/** TOGGLING VIEWS **/

// When the 'Log In' button is pressed, make the log in form display
if(loginFormToggle) {
    loginFormToggle.addEventListener("click", function() {
        toggleLoginForm(true);
        toggleRegisterForm(false);
    });
}

// When the 'Register' button is pressed, make the register form display
if(registerFormToggle) {
    registerFormToggle.addEventListener("click", function() {
        toggleLoginForm(false);
        toggleRegisterForm(true);
});
}

// When a user clicks their profile in the header, make a big modal displaying their profile pop-up
if(profileModalToggle) {
    profileModalToggle.addEventListener("click", function() {
        toggleProfileModal(true);
    });
}

// When a user clicks the 'x' in the top-right of the profile modal, close it
if(closeProfileModal) {
    closeProfileModal.addEventListener("click", function() {
        toggleProfileModal(false);
    });
}








