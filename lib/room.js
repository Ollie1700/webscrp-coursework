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
                    
                    self.appendMessage(channel_chat, m);
                    
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
            channel_textarea = document.createElement('textarea');
        
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
                var date = new Date(),
                    timestamp = date.getHours() + ':' + date.getMinutes(),
                    params = ['user_id=' + self.user_id, 'message=' + channel_textarea.value, 'timestamp=' + timestamp];
                
                // If the message is nothing but whitespace don't send it
                if(!(/\S/.test(channel_textarea.value))) {
                    return;
                }
                
                // Post the message
                Ajax('POST', '/room/' + self.room_data.room_id + '/message', params, function(r, c) {
                    
                    // If message was sent successfully
                    if(c == 201) {
                        
                        var messages = get_json_objects_from_result(r),
                            message = messages[0];
                        
                        self.appendMessage(self.channel_chat_div, message);
                        
                        channel_textarea.value = '';
                        
                    }
                    // If message sending failed
                    else {
                        var error_message = {
                            'message_message': '<em style="color:red;">Failed to send message, please try again later.</em>',
                            'message_timestamp': timestamp,
                            'message_sender': 'System'
                        };
                        
                        self.appendMessage(self.channel_chat_div, error_message);
                    }
                    
                });
                
                // Prevent the default form submission from happening
                e.preventDefault();
                return false;
            }
        });
    }

    pollForMessages(self) {
        Ajax('GET', '/room/' + self.room_data.room_id + '/message', ['limit=' + self.poll_params.limit], function(r, c) {
            
            var i,
                messages = get_json_objects_from_result(r),
                latest_message;
            
            for(i = 0; i < messages.length; i++) {
                
                // If the message is one of our own, don't display it again
                if(messages[i].user_id == self.user_id) {
                    self.poll_params.limit++;
                    continue;
                }
                
                latest_message = self.appendMessage(self.channel_chat_div, messages[i]);
                
                self.poll_params.limit++;
            }
            
            if(latest_message) {
                self.channel_chat_div.scrollTop = latest_message.offsetTop;
            }
            
        });
    }

    appendMessage(channel_chat, m) {
        var message_span = document.createElement('span'),
            sender_span = document.createElement('span'),
            message = document.createTextNode(m.message_message),
            sender = document.createTextNode(m.message_timestamp + ' | ' + m.message_sender_name);
        
        message_span.setAttribute('class', 'message');
        
        sender_span.setAttribute('class', 'sender');
        
        sender_span.appendChild(sender);

        message_span.innerHTML = m.message_message; //message_span.appendChild(message);
        message_span.appendChild(sender_span);
        
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











