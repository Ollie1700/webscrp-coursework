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
    
    addFriendForm = document.getElementById("add-friend-form"),
    
    addFriendFormSubmit = document.getElementById("add-friend-submit"),
    
    // Toggling the views of various features
    
    toggleLoginForm = function(display) {
        
        loginFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleRegisterForm = function(display) {
        
        registerFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleProfileModal = function(display) {
        
        if(display) {
            profileModal.style.display = 'block';
            setTimeout(function() {
                profileContainer.style.margin = '10% auto';
            }, 100);
        }
        else {
            profileContainer.style.margin = '-100% auto';
            setTimeout(function() {
                profileModal.style.display = 'none';
            }, 300);
        }
    },
    
    // Form validation and submission
    
    updateProfile = function() {
        
        var
            user_id = document.getElementById("update-profile-user-id").value,
            email = document.getElementById("update-profile-form-email").value,
            first_name = document.getElementById("update-profile-form-first-name").value,
            last_name = document.getElementById("update-profile-form-last-name").value,
            feedback = document.getElementById("update-profile-form-feedback");
        
        Ajax('PUT', '/user/' + user_id, ['email=' + email, 'first_name=' + first_name, 'last_name=' + last_name], function(r, c) {
            
            if(c == 201) {
                document.getElementById("user-name").innerHTML = 'Welcome back, ' + first_name;
                feedback.style.color = 'green';
                feedback.innerHTML = r;
            }
            else if(c == 400) {
                feedback.style.color = 'red';
                feedback.innerHTML = r;
            }
            
        });
        
    },
    
    addFriend = function() {
        
        var
            friend_email = document.getElementById("add-friend-input").value,
            user_id = document.getElementById("add-friend-user-id").value,
            form_feedback = document.getElementById("add-friend-form-feedback");
        
        Ajax('POST', '/user/' + user_id + '/friend/add', ['friend_email=' + friend_email], function(r, c) {
            switch(c) {
                case 201:
                    form_feedback.style.color = 'green';
                    form_feedback.innerHTML = r;
                    break;
                case 404:
                    form_feedback.style.color = 'red';
                    form_feedback.innerHTML = r;
                    break;
            }
        });
        
    }

    ;

/** FORM VALIDATION AND SUBMISSION **/

if(updateProfileFormSubmit) {
    updateProfileFormSubmit.addEventListener("click", function(e) {
        
        updateProfile();
        
        // Stop the form from being submitted old-style
        e.preventDefault();
        return false;
    });
}

if(addFriendFormSubmit) {
    addFriendFormSubmit.addEventListener("click", function(e) {
        
        addFriend();
        
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








