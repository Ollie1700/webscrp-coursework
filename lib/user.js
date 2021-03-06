var 
    
    // Grab all the relevant elements

    loginFormContainer = document.getElementById("user-login-form-container"),
    
    registerFormContainer = document.getElementById("user-register-form-container"),
    
    loginFormToggle = document.getElementById("log-in-form-toggle"),
    
    logInLink = document.getElementById("log-in-link"),
    
    registerFormToggle = document.getElementById("register-form-toggle"),
    
    registerForm = document.getElementById("form-register"),
    
    registerFormSubmit = document.getElementById("form-register-submit"),
    
    profileModal = document.getElementById("profile-modal"),
    
    profileContainer = document.getElementById("profile-container"),
    
    profileModalToggle = document.getElementById("profile-modal-toggle"),
    
    closeProfileModal = document.getElementById("close-profile-modal"),
    
    updateProfileForm = document.getElementById("update-profile-form"),
    
    updateProfileFormSubmit = document.getElementById("submit-update-profile-form"),
    
    addFriendForm = document.getElementById("add-friend-form"),
    
    addFriendFormSubmit = document.getElementById("add-friend-submit"),
    
    addFriendFormFeedback = document.getElementById("add-friend-form-feedback"),
    
    friendsListContainer = document.getElementById("friends-list-container"),
    
    friendsList = document.getElementById("friends-list"),
    
    roomsList = document.getElementById("user-rooms-list"),
    
    userRoomsInfo = document.getElementById("user-rooms-info"),
    
    joinRoomForm = document.getElementById("join-room-form"),
    
    joinRoomFormSubmit = document.getElementById("join-room-input-submit"),

    firstProfileLoad = true,
    
    // Helper functions
    
    addFriendToList = function(o) {
        var
            row = document.createElement('tr'),
            name_column = document.createElement('td'),
            remove_column = document.createElement('td'),
            name = document.createTextNode(o.user_first_name + ' ' + o.user_last_name),
            remove_friend_button = document.createElement('input');

        remove_friend_button.type = 'submit';
        remove_friend_button.value = 'Remove friend';

        remove_friend_button.addEventListener("click", function(e) {

            Ajax('DELETE', '/user/' + window.current_user_id + '/friend', ['friend_id=' + o.user_id], function(r, c) {
                switch(c) {
                    case 200:
                        addFriendFormFeedback.style.color = 'green';
                        addFriendFormFeedback.innerHTML = r;
                        friendsList.removeChild(row);
                        break;
                    default:
                        addFriendFormFeedback.style.color = 'red';
                        addFriendFormFeedback.innerHTML = r;
                        break;
                }
            });

            e.preventDefault();
            return false;
        });

        name_column.appendChild(name);
        remove_column.appendChild(remove_friend_button);
        row.appendChild(name_column);
        row.appendChild(remove_column);
        friendsList.appendChild(row);
    },
    
    // Toggling the views of various features
    
    toggleLoginForm = function(display) {
        
        loginFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleRegisterForm = function(display) {
        
        registerFormContainer.style.display = (display) ? 'block' : 'none';
        
    },
    
    toggleProfileModal = function(display) {
        
        // If it's the first time we're opening the profile, load the friends list and rooms list
        if(firstProfileLoad) {
            
            // Load friends
            Ajax('GET', '/user/' + window.current_user_id + '/friend', [], function(r, c) {
                var
                    objs = get_json_objects_from_result(r);
                
                if(objs) {
                    
                    for(var i = 0; i < objs.length; i++) {
                        addFriendToList(objs[i]);
                    }
                    
                }
                else {
                    friendsListContainer.innerHTML = 'You have no friends yet. Add some!';
                }
            });
            
            // Load rooms
            Ajax('GET', '/user/' + window.current_user_id + '/room', [], function(r, c) {
                
                var
                    objs = get_json_objects_from_result(r);
                
                if(objs) {
                    
                    var table = document.createElement('table');
                    
                    for(var i = 0; i < objs.length; i++) {
                        
                        (function(o) {
                            var
                                row = document.createElement('tr'),
                                name_col = document.createElement('td'),
                                delete_col = document.createElement('td'),
                                name = document.createTextNode(o.room_name),
                                leave_room = document.createElement('input');
                            
                            leave_room.type = 'submit';
                            leave_room.value = 'Leave room';
                            
                            leave_room.addEventListener('click', function(e) {
                                Ajax('DELETE', '/user/' + window.current_user_id + '/room', ['room_id=' + o.room_id], function(r, c) {
                                    if(c == 200) {
                                        var left_room = get_json_objects_from_result(r)[0];
                                        userRoomsInfo.style.color = 'green';
                                        userRoomsInfo.innerHTML = 'You have left <strong>' + left_room.room_name + '</strong>.';
                                        table.removeChild(row);
                                        if(table.childNodes.length == 0) {
                                            roomsList.innerHTML = 'No rooms to display';
                                        }
                                        removeRoom(left_room.room_id);
                                    }
                                    else {
                                        userRoomsInfo.style.color = 'red';
                                        userRoomsInfo.innerHTML = r;
                                    }
                                });
                                e.preventDefault();
                                return false;
                            });

                            name_col.appendChild(name);
                            delete_col.appendChild(leave_room);
                            row.appendChild(name_col);
                            row.appendChild(delete_col);
                            table.appendChild(row);
                        })(objs[i]);
                    }
                    
                    roomsList.appendChild(table);
                }
                else {
                    roomsList.innerHTML = '<tr><td>No rooms to display.</td></tr>';
                }
                
            });
            
            // We're done
            firstProfileLoad = false;
        }
        
        if(display) {
            profileModal.style.display = 'block';
            setTimeout(function() {
                profileContainer.style.margin = '5% auto';
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
    
    registerUser = function() {
        
        var
            email = document.getElementById("form-register-email").value,
            first_name = document.getElementById("form-register-first-name").value,
            last_name = document.getElementById("form-register-last-name").value,
            password = document.getElementById("form-register-password").value,
            confirm_password = document.getElementById("form-register-confirm-password").value,
            register_form_feedback = document.getElementById("register-form-feedback"),
            newly_created_account_name = document.getElementById("newly_created_account_name");
        
        if(password != confirm_password) {
            register_form_feedback.innerHTML = 'Passwords must match.';
            return;
        }
        
        Ajax('POST', '/user', ['email=' + email, 'first_name=' + first_name, 'last_name=' + last_name, 'password=' + password], function(r, c) {
            
            if(c == 201) {
                
                var
                    success = document.getElementById("successfully-created-account"),
                    acc_name = document.getElementById("newly-created-account-name");
                
                acc_name.innerHTML = email;
                
                success.style.display = 'block';
                
                register_form_feedback.innerHTML = '';
                
                registerForm.style.display = 'none';
                
            }
            else {
                register_form_feedback.innerHTML = r;
            }
            
        });
        
    },
    
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
            user_id = document.getElementById("add-friend-user-id").value;
        
        Ajax('POST', '/user/' + user_id + '/friend', ['friend_email=' + friend_email], function(r, c) {
            
            var objs = get_json_objects_from_result(r);
            
            if(objs) {
                switch(c) {
                    case 201:
                        // Add friend to table
                        addFriendToList({"user_id":objs[0].user_id, "user_first_name":objs[0].user_first_name, "user_last_name":objs[0].user_last_name});

                        // Gives some feedback
                        addFriendFormFeedback.style.color = 'green';
                        addFriendFormFeedback.innerHTML = 'Friend request sent!';
                        break;
                    case 404:
                        addFriendFormFeedback.style.color = 'red';
                        addFriendFormFeedback.innerHTML = r;
                        break;
                }
            }
            else {
                addFriendFormFeedback.style.color = 'red';
                addFriendFormFeedback.innerHTML = r;
            }
        });
        
    },
    
    joinRoom = function(created) {
        
        var
            join_room_name = document.getElementById("join-room-input-room").value,
            user_rooms_list = document.getElementById("user_rooms_list");
        
        Ajax('POST', '/user/' + window.current_user_id + '/room', ['room_name=' + join_room_name], function(r, c) {
            
            if(c == 201) {
                var 
                    new_room = get_json_objects_from_result(r)[0];
                
                renderRoom(new_room);

                // Feedback
                if(!created) {
                    userRoomsInfo.style.color = 'green';
                    userRoomsInfo.innerHTML = 'Successfully joined ' + new_room.room_name;
                }
            }
            else if(c == 400) {
                userRoomsInfo.style.color = 'red';
                userRoomsInfo.innerHTML = r;
            }
            // The user is already part of this room
            else if(c == 409) {
                userRoomsInfo.style.color = 'red';
                userRoomsInfo.innerHTML = r;
            }
            // If a room doesn't exist, create it
            else {
                Ajax('POST', '/room', ['name=' + join_room_name, 'admin_id=' + window.current_user_id], function(r, c) {
                    if(c == 201) {
                        joinRoom(true);
                        userRoomsInfo.style.color = 'green';
                        userRoomsInfo.innerHTML = r + " You are now the admin of <strong>" + join_room_name + "</strong>.";
                    }
                    else {
                        userRoomsInfo.style.color = 'red';
                        userRoomsInfo.innerHTML = r;
                    }
                });
            }
            
        });
        
    },
    
    renderRoom = function(room) {
        var
            channel_container = document.getElementById('channel-container'),
            channel_div = document.createElement('div'),
            channel_title_span = document.createElement('span'),
            channel_title = document.createTextNode('#' + room.room_name),
            channel_chat = document.createElement('div'),
            channel_input_div = document.createElement('div'),
            channel_input = document.createElement('textarea');
        
        channel_div.setAttribute('id', 'channel-' + room.room_id);
        channel_div.setAttribute('class', 'channel');
        
        channel_title_span.setAttribute('class', 'channel-title');
        channel_title_span.appendChild(channel_title);
        
        channel_chat.setAttribute('id', 'channel-chat-' + room.room_id);
        channel_chat.setAttribute('class', 'channel-chat');
        
        channel_input_div.setAttribute('class', 'channel-input');
        
        channel_input.setAttribute('id', room.room_id + '-message-input');
        channel_input.setAttribute('type', 'text');
        channel_input.setAttribute('placeholder', 'Send a message to #' + room.room_name);
        
        channel_input_div.appendChild(channel_input);
        
        channel_div.appendChild(channel_title_span);
        channel_div.appendChild(channel_chat);
        channel_div.appendChild(channel_input_div);
        
        channel_container.appendChild(channel_div);
    },
    
    removeRoom = function(room_id) {
        document.getElementById('channel-container').removeChild(document.getElementById('channel-' + room_id));
    }

    ;

/** FORM VALIDATION AND SUBMISSION **/

if(registerFormSubmit) {
    registerFormSubmit.addEventListener("click", function(e) {
        
        registerUser();
        
        // Stop the form from being submitted old-style
        e.preventDefault(e);
        return false;
    });
}

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

if(joinRoomFormSubmit) {
    joinRoomFormSubmit.addEventListener("click", function(e) {
        
        joinRoom();
        
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

if(logInLink) {
    logInLink.addEventListener("click", function() {
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






