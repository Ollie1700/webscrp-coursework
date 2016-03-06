'use strict';

class Room {
    
    constructor(room, user_id) {
        
        // Initialise the HTML elements for the channel
        var self = this,
            channel = document.createElement('div'),
            channel_title = document.createElement('span'),
            channel_title_text = document.createTextNode('#' + room.room_name),
            channel_chat = document.createElement('div'),
            upload_area = document.createElement('div'),
            upload_area_text = document.createTextNode('Upload to #' + room.room_name),
            upload_img = document.createElement('img');
        
        // Initialise some basic spam prevention variables
        this.current_spam = 0; // Amount of messages the user has sent in a short time
        this.spam_limit = 5; // Amount of messages the user is allowed to send in a short time
        this.spam_cooldown = 10000; // Time the user has to wait if they have spammed too much (in ms)
        this.allowed_time_between_messages = 2000; // After this amount of time has passed between messages, current_spam will be reset
        this.last_message_sent_at = 0; // When the last message was sent
        this.spam_achieved_at = 0; // If the user is currently on spam cooldown
        
        // Initialise the basic attributes for the channel
        channel.setAttribute('class', 'channel');
        channel.setAttribute('id', 'channel-' + room.room_id);
        
        channel_title.setAttribute('class', 'channel-title');
        
        channel_chat.setAttribute('class', 'channel-chat');
        channel_chat.setAttribute('id', 'channel-chat-' + room.room_id);
        
        channel_title.appendChild(channel_title_text);
        
        channel.appendChild(channel_title);
        channel.appendChild(channel_chat);
        
        // Initialise the upload area for this channel
        upload_area.setAttribute('id', 'drag-target-' + room.room_id);
        upload_area.setAttribute('class', 'drag-target');
        upload_area.setAttribute('roomid', room.room_id);
        
        upload_img.setAttribute('src', 'img/upload.png');
        upload_area.appendChild(upload_img);
        
        upload_area.appendChild(upload_area_text);
        
        channel.appendChild(upload_area);
        
        // Add the drag listeners
        addDragListeners(upload_area);
        
        // Append the channel to the mains screen
        document.getElementById('channel-container').appendChild(channel);
        
        // Make these available globally
        this.room_data = JSON.parse(JSON.stringify(room)); // Clone the object
        this.user_id = user_id;
        this.channel_div = channel;
        this.channel_chat_div = channel_chat;
        
        // We need parameters for polling messages later
        this.poll_params = {
            'limit': 0
        };
        
        // Get all the messages from this room
        Ajax('GET', '/room/' + room.room_id + '/message', [], function(r, c) {
            
            var i,
                messages = get_json_objects_from_result(r);
            
            if(!messages) return;
            
            for(i = 0; i < messages.length; i++) {
                (function(m, s) {
                    
                    self.appendMessage(self, channel_chat, m);
                    
                })(messages[i], this);
                
                self.poll_params.limit++;
            }
            
        });
        
        // Initialise input
        this.initInput();
        
        // Set up message polling
        this.messagePollInterval = setInterval(function(){
            self.pollForMessages(self);
        }, 2000);
        
        // Add the variables that we expect to get from this
        this.room_id = room.room_id;
        this.room_name = room.room_name;
        this.room_admin_id = room.room_admin_id;
    }
    
    initInput() {
        // Set up the HTML elements for the input
        var self = this,
            channel_input = document.createElement('div'),
            channel_textarea = document.createElement('textarea'),
            disconnect_message_div = document.createElement('div'),
            disconnect_message = document.createTextNode('You have disconnected from the chat service. Please wait or try refreshing the page.');
        
        // Append the disconnect message
        disconnect_message_div.setAttribute('class', 'disconnect-message');
        disconnect_message_div.appendChild(disconnect_message);
        channel_input.appendChild(disconnect_message_div);
        
        this.disconnect_message_div = disconnect_message_div;
        
        // Initialise the input and its attributes after the messages have been loaded
        channel_input.setAttribute('class', 'channel-input');

        channel_textarea.setAttribute('id', this.room_data.room_id + '-message-input');
        channel_textarea.setAttribute('type', 'text');
        channel_textarea.setAttribute('placeholder', 'Send a message to #' + this.room_data.room_name);

        channel_input.appendChild(channel_textarea);

        this.channel_div.appendChild(channel_input);

        // Add the event listeners to the input
        channel_textarea.addEventListener('keydown', function(e) {
            if(e.keyCode == 13) { // The enter key
                
                // Grab the date for a timestamp
                var timestamp = self.getCurrentTimestamp(),
                    params = ['user_id=' + self.user_id, 'message=' + channel_textarea.value, 'timestamp=' + timestamp];
                
                // If the message is nothing but whitespace don't send it
                if(!(/\S/.test(channel_textarea.value))) {
                    return;
                }
                
                // Spam protection
                if(Date.now() - self.spam_achieved_at >= self.spam_cooldown) {
                    
                    // Post the message
                    Ajax('POST', '/room/' + self.room_data.room_id + '/message', params, function(r, c) {

                        // If message was sent successfully
                        if(c == 201) {

                            var messages = get_json_objects_from_result(r),
                                message = messages[0];

                            self.disconnect_message_div.style.display = 'none';

                            self.appendMessage(self, self.channel_chat_div, message);

                            channel_textarea.value = '';

                        }
                        // If message sending failed
                        else {
                            var error_message = {
                                'message_message': '<em style="color:red;">Failed to send message, please try again later.</em>',
                                'message_timestamp': timestamp,
                                'message_sender_name': 'System'
                            };

                            self.appendMessage(self, self.channel_chat_div, error_message);
                            
                            self.disconnect_message_div.style.display = 'block';
                        }

                    });
                    
                    if(Date.now() - self.last_message_sent_at >= self.allowed_time_between_messages) {
                        self.current_spam = 0;
                    }
                    else {
                        self.current_spam += 1;

                        if(self.current_spam == self.spam_limit) {
                            self.spam_achieved_at = Date.now();
                        }
                    }

                    self.last_message_sent_at = Date.now();
                }
                // User spammed too much
                else {
                    var spam_message = {
                        'message_message': '<em style="color:red;">You are sending messages too quickly. Please wait ' + ((self.spam_cooldown - (Date.now() - self.spam_achieved_at)) / 1000) + ' seconds before posting again.</em>',
                        'message_timestamp': timestamp,
                        'message_sender_name': 'System'
                    };
                    
                    self.appendMessage(self, self.channel_chat_div, spam_message);
                }
                
                /* spam debugging
                console.log('Current spam: ' + self.current_spam);
                console.log('Spam limit: ' + self.spam_limit);
                console.log('Spam cooldown: ' + self.spam_cooldown);
                console.log('Allowed time between messages: ' + self.allowed_time_between_messages);
                console.log('Last message sent at: ' + self.last_message_sent_at);
                console.log('Spam achieved at: ' + self.spam_achieved_at);
                */
                
                // Prevent the default form submission from happening
                e.preventDefault();
                return false;
            }
        });
    }

    pollForMessages(self) {
        Ajax('GET', '/room/' + self.room_data.room_id + '/message', ['limit=' + self.poll_params.limit], function(r, c) {
            
            if(c == 200) {
                var i,
                    messages = get_json_objects_from_result(r),
                    latest_message;
                
                if(messages == null) {
                    self.disconnect_message_div.style.display = 'block';
                    return;
                }
                
                // Remove the disconnect message if it's being displayed
                self.disconnect_message_div.style.display = 'none';

                for(i = 0; i < messages.length; i++) {

                    // If the message is one of our own, don't display it again
                    if(messages[i].user_id == self.user_id) {
                        self.poll_params.limit++;
                        continue;
                    }

                    latest_message = self.appendMessage(self, self.channel_chat_div, messages[i]);

                    self.poll_params.limit++;
                }

                if(latest_message) {
                    self.channel_chat_div.scrollTop = latest_message.offsetTop;
                }
            }
            else {
                // If we get any other response than 200, we've been disconnected from the chat service
                self.disconnect_message_div.style.display = 'block';
            }
            
        });
    }

    appendMessage(self, channel_chat, m) {
        var message_span = document.createElement('span'),
            sender_span = document.createElement('span'),
            actual_message_span = document.createElement('span'),
            message = document.createTextNode(m.message_message),
            sender = document.createTextNode(m.message_timestamp + ' | ' + m.message_sender_name),
            message_control_span = document.createElement('span'),
            message_edit_button = document.createElement('a'),
            message_delete_button = document.createElement('a'),
            message_edit_text = document.createTextNode('Edit'),
            message_delete_text = document.createTextNode('Delete'),
            message_control_seperator = document.createTextNode(' | '),
            message_editor = document.createElement('input');
        
        actual_message_span.innerHTML = m.message_message;
        
        message_span.appendChild(actual_message_span);
        
        message_span.setAttribute('class', 'message');
        
        sender_span.setAttribute('class', 'sender');
        
        sender_span.appendChild(sender);
        
        message_span.appendChild(sender_span);
        
        // Add the controls to the message if it's a message from us
        if(m.user_id == window.current_user_id) {
            message_control_span.setAttribute('class', 'message-controls');
            message_edit_button.setAttribute('href', '#');
            message_delete_button.setAttribute('href', '#');
            message_edit_button.appendChild(message_edit_text);
            message_delete_button.appendChild(message_delete_text);
            message_control_span.appendChild(message_edit_button);
            message_control_span.appendChild(message_control_seperator);
            message_control_span.appendChild(message_delete_button);
            message_span.appendChild(message_control_span);

            message_editor.setAttribute('class', 'message-editor');
            message_editor.setAttribute('value', m.message_message);
            message_span.appendChild(message_editor);
            message_span.appendChild(message_control_span);
            
            // Event listeners
            
            // Editing the message when enter is pressed on the message editor
            message_editor.addEventListener('keydown', function(e) {
                if(e.keyCode == 13) { // The enter key
                    
                    var edited_message = message_editor.value,
                        edited_timestamp = 'Edited at ' + self.getCurrentTimestamp();

                    Ajax('PUT', '/room/' + self.room_data.room_id + '/message/' + m.message_id, ['user_id=' + m.user_id, 'message_message=' + edited_message, 'message_timestamp=' + edited_timestamp], function(r, c) {

                        if(c == 201) {

                            var new_message = get_json_objects_from_result(r)[0];

                            if(!new_message) console.log(new_message);

                            actual_message_span.innerHTML = new_message.message_message;
                            sender_span.innerHTML = new_message.message_timestamp + ' | ' + new_message.message_sender_name;
                            
                            message_editor.style.display = 'none';
                            actual_message_span.style.display = 'block';
                        }
                        else {
                            console.log(r);
                        }

                    });

                }
            });
            
            // Make the editor appear when the 'Edit' button is clicked
            message_edit_button.addEventListener('click', function(e) {
                message_editor.style.display = 'block';
                actual_message_span.style.display = 'none';
                // Prevent default link action
                e.preventDefault();
            });
        }
        // END message controls

        // If this message is from a friend, add the friend icon
        for(var i = 0; i < window.chatapp.friends_list.length; i++) {
            var friend = window.chatapp.friends_list[i];
            if(friend.user_id == m.user_id) {
                var img = document.createElement('img');
                img.setAttribute('src', 'img/friend_icon.png');
                sender_span.appendChild(document.createTextNode(' '));
                sender_span.appendChild(img);
                break;
            }
        }
        
        channel_chat.appendChild(message_span);
        
        // Scroll to where the current message is
        channel_chat.scrollTop = channel_chat.scrollHeight;
        
        // Return the message
        return message_span;
    }
    
    getCurrentTimestamp() {
        var date = new Date();
        return date.getHours() + ':' + date.getMinutes();
    }
    
};

// Initialise the rooms if the user is logged in
if(window.current_user_id) {
    Ajax('GET', '/user/' + window.current_user_id + '/room', [], function(r, c) {

        if(c == 200) {

            var i,
                rooms = get_json_objects_from_result(r);

            for(i = 0; i < rooms.length; i++) {
                window.chatapp.joined_rooms.push(new Room(rooms[i], window.current_user_id));
            }

        }
        else {
            alert('Error getting rooms. Try refreshing the page.');
        }

    });
}











