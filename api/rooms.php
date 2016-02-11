<?php

require_once 'init.php';

$rooms = array();

foreach($CURRENT_USER->get_rooms_list() as $room_id) {
    $rooms[] = Room::get($room_id);
}

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
                <input id="'.$room->get_room_id().'-message-input" type="text" placeholder="Send a message to #'.$room->get_room_name().'">
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
                    
                    var date = new Date();
                    
                    var timestamp = date.getHours() + ':' + date.getMinutes(); // + ', ' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
                    
                    Ajax('POST', '/room/<?php echo $room->get_room_id(); ?>/message', ['user_id=<?php echo $CURRENT_USER->get_user_id(); ?>', 'message=' + channel_chat_input.value, 'timestamp=' + timestamp], function(r){});
                    
                    var
                        message_span = document.createElement("span"),
                        message_sender_span = document.createElement("span"),
                        message_text_node = document.createTextNode(channel_chat_input.value),
                        message_sender_text_node = document.createTextNode(timestamp + ' | <?php echo $CURRENT_USER->get_user_first_name() . ' ' . $CURRENT_USER->get_user_last_name(); ?>');
                    
                    message_span.className += "message";
                    message_sender_span.className += "sender";
                    
                    message_sender_span.appendChild(message_sender_text_node);
                    
                    message_span.appendChild(message_text_node);
                    message_span.appendChild(message_sender_span);
                    channel_chat.appendChild(message_span);
                    
                    channel_chat_input.value = '';
                    
                    channel_chat.scrollTop = channel_chat.scrollHeight;
                    
                    return false; // Prevent the default form submission
                }
            });
        })();
    
        (function() {
            
            var limit = 0;
            
            setInterval(function(){
                
                Ajax('GET', '/room/<?php echo $room->get_room_id(); ?>/message', ['limit=' + limit], function(r) {
                    
                    if(!r) return;
                    
                    var 
                        objs = get_json_objects_from_result(r),
                        channel_chat = document.getElementById("channel-chat-<?php echo $room->get_room_id(); ?>"),
                        latest_message;
                    
                    if(objs == null) return;
                    
                    for(var i = 0; i < objs.length; i++) {
                        
                        var
                            message_span = document.createElement("span"),
                            message_sender_span = document.createElement("span"),
                            message_text_node = document.createTextNode(objs[i].message_message),
                            message_sender_text_node = document.createTextNode(objs[i].message_timestamp + ' | ' + objs[i].message_sender_name);
                        
                        message_span.className += "message";
                        message_sender_span.className += "sender";
                        
                        message_sender_span.appendChild(message_sender_text_node);
                        
                        message_span.appendChild(message_text_node);
                        message_span.appendChild(message_sender_span);
                        channel_chat.appendChild(message_span);
                        
                        latest_message = message_span;
                        
                        limit++;
                    }
                    
                    // Always scroll down to the latest message
                    channel_chat.scrollTop = latest_message.offsetTop;
                    
                });
                
            }, 2000);
            
        })();

    <?php endforeach; ?>

    -->
</script>


















