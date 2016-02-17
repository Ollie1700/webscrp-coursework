<?php

require_once 'init.php';

$rooms = $CURRENT_USER->get_rooms_list();

foreach($rooms as $room) {
    echo 
        '<div id="channel-'.$room->get_room_id().'" class="channel">
            <span class="channel-title">#'.$room->get_room_name().'</span>
            <div id="channel-chat-'.$room->get_room_id().'" class="channel-chat">';
    
    $messages = $room->get_messages();
    
    foreach($messages as $message) {
        echo '
            <span class="message">
                '.$message->get_message_message().'
                <span class="sender">
                    '.$message->get_message_timestamp().' | '.$message->get_sender_name().'
                </span>
            </span>
        ';
    }
    
    echo '</div>';
    
    echo
        '<div class="channel-input">
                <textarea id="'.$room->get_room_id().'-message-input" type="text" placeholder="Send a message to #'.$room->get_room_name().'"></textarea>
            </div>
        </div>
        ';
}

?>

<script>
    <!--

    <?php foreach($rooms as $room) : ?>

        (function() {
            var 
                channel_chat = document.getElementById("channel-chat-<?php echo $room->get_room_id(); ?>"),
                channel_chat_input = document.getElementById("<?php echo $room->get_room_id(); ?>-message-input");
            
            channel_chat_input.addEventListener('keydown', function(e) {
                if(e.keyCode == 13) {
                    
                    // If the message is nothing but whitespace don't send it
                    if(!(/\S/.test(channel_chat_input.value))) {
                        return;
                    }
                    
                    var date = new Date();
                    
                    var timestamp = date.getHours() + ':' + date.getMinutes(); // + ', ' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
                    
                    Ajax('POST', '/room/<?php echo $room->get_room_id(); ?>/message', ['user_id=<?php echo $CURRENT_USER->get_user_id(); ?>', 'message=' + channel_chat_input.value, 'timestamp=' + timestamp], function(r, c){
                        
                        var objs = get_json_objects_from_result(r);
                        
                        if(objs) {
                            var
                                msg = objs[0],
                                message_span = document.createElement("span"),
                                message_sender_span = document.createElement("span"),
                                message_sender_text_node = document.createTextNode('Sent at ' + msg.message_timestamp + ' | ' + msg.message_sender_name);

                            message_span.className += "message";
                            message_sender_span.className += "sender";

                            message_sender_span.appendChild(message_sender_text_node);

                            message_span.innerHTML = msg.message_message;
                            message_span.appendChild(message_sender_span);
                            channel_chat.appendChild(message_span);

                            channel_chat_input.value = '';
                            
                            channel_chat.scrollIntoView(message_span);
                            channel_chat.scrollTop = channel_chat.scrollHeight;
                        }
                        else {
                            var
                                error_span = document.createElement("span"),
                                error_message = document.createTextNode("Failed to send message. Please try again later.");
                            error_span.style.color = 'red';
                            error_span.appendChild(error_message);
                            channel_chat.appendChild(error_span);
                        }
                        
                    });
                    
                    // Prevent the default form submission
                    e.preventDefault();
                    return false;
                }
            });
        })();
    
        (function() {
            
            var 
                limit = 0,
                initial_pass = true,
                channel_chat = document.getElementById("channel-chat-<?php echo $room->get_room_id(); ?>");
            
            channel_chat.scrollTop = channel_chat.scrollHeight + 320;
            
            setInterval(function(){
                
                Ajax('GET', '/room/<?php echo $room->get_room_id(); ?>/message', ['limit=' + limit], function(r) {
                    
                    console.log(r);
                    
                    if(!r) return;
                    
                    var 
                        objs = get_json_objects_from_result(r),
                        latest_message;
                    
                    for(var i = 0; i < objs.length; i++) {
                        
                        // If this is the first retrieval of messages, don't display any of them; we've already displayed them during
                        // the initial room rendering. We just want to get the limit.
                        if(initial_pass) {
                            limit++;
                            continue;
                        }
                        
                        // If the message is one of our own messages, don't display it again
                        if(objs[i].user_id == <?php echo $CURRENT_USER->get_user_id(); ?>) {
                           limit++;
                           continue; 
                        }
                        
                        var
                            message_span = document.createElement("span"),
                            message_sender_span = document.createElement("span"),
                            message_sender_text_node = document.createTextNode(objs[i].message_timestamp + ' | ' + objs[i].message_sender_name);
                        
                        message_span.className += "message";
                        message_sender_span.className += "sender";
                        
                        message_sender_span.appendChild(message_sender_text_node);
                        
                        message_span.innerHTML = objs[i].message_message;
                        message_span.appendChild(message_sender_span);
                        channel_chat.appendChild(message_span);
                        
                        latest_message = message_span;
                        
                        limit++;
                    }
                    
                    // Always scroll down to the latest message
                    if(latest_message) {
                        channel_chat.scrollTop = latest_message.offsetTop;
                    }
                    
                    // Once we reach this part of the code, the initial pass has completed
                    initial_pass = false;
                });
                
            }, 2000);
            
        })();

    <?php endforeach; ?>
    
    -->
</script>


















