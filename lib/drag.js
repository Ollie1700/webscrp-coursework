
var 
    initProfilePicDragUpload = function(target) {
        
        target.addEventListener('dragover', function(e) {
            
            document.getElementById('profile-pic-upload').setAttribute('class', 'pulse');
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
        target.addEventListener('dragleave', function(e) {
            
            document.getElementById('profile-pic-upload').removeAttribute('class');
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
        target.addEventListener('drop', function(e) {
            
            var i,
                files = e.dataTransfer.files,
                fd = new FormData();
            
            fd.append('file', files[0]);
            
            Ajax('POST', '/user/' + window.current_user_id + '/profilepic', fd, function(r, c) {
                if(c == 201) {
                    target.innerHTML = r;
                    document.getElementById('header-profile-pic').innerHTML = r;
                }
                else {
                    console.log("Error " + c + ": " + r);
                }
            });
            
            document.getElementById('profile-pic-upload').removeAttribute('class');
            
            // Prevent default behaviour
            e.preventDefault();
            return false;
        });
        
    },
    
    uploadToRoom = function(e) {
        var i,
            files = e.dataTransfer.files,
            fd = new FormData(),
            room_id = e.target.getAttribute('roomid') ? e.target.getAttribute('roomid') : e.target.parentNode.getAttribute('roomid'),
            room = window.chatapp.getRoomById(room_id);
        
        for(i = 0; i < files.length; i++) {
            fd.append('file_' + i, files[i]);
        }

        fd.append('room_id', room_id);
        fd.append('user_id', window.current_user_id);
        fd.append('timestamp', room.getCurrentTimestamp());

        Ajax('POST', '/room/' + room_id + '/upload', fd, function(r, c) {

            if(c == 201) {

                var messages = get_json_objects_from_result(r);

                room.appendMessage(room, messages[0]);
            }
            else {
                console.log(r);
            }

        });

        // Prevent default behaviour
        e.preventDefault();
        return false;
    },
    
    isHoldingFile = function(e) {
        var dt = e.dataTransfer;
        return dt.types != null && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('application/x-moz-file'));
    },
    
    displayDropTargets = function(show) {
        var drop_targets = document.getElementsByClassName('drag-target'),
            i = 0;
        
        for(i = 0; i < drop_targets.length; i++) {
            drop_targets[i].style.display = show ? 'block' : 'none';
        }
    },
    
    drag_timer;

// Bubble through the channels and respond to events

// FIRED ONCE WHENEVER THE USER STARTS TO DRAG SOMETHING //
document.getElementById('channel-container').addEventListener('dragstart', function(e) {
    
    // If the user is dragging a message
    if(e.target.className == 'message') {
        // Format it as a quote
        e.dataTransfer.setData('text/plain', e.target.getAttribute('raw-content'));
        
        // Set the drag image
        var img = new Image(); 
        img.src = 'img/quote.png'; 
        e.dataTransfer.setDragImage(img, 66, 27);
    }
    
});

// FIRED ONCE WHEN A USER ENTERS A VALID DROP AREA (validity specified by preventingDefault and returning false) //
document.getElementById('channel-container').addEventListener('dragenter', function(e) {
    
    // If the user is dragging a file
    if(isHoldingFile(e)) {
        displayDropTargets(true);
    }
    
    e.preventDefault();
    return false;
});

// FIRED WHEN THE USER LEAVES A VALID DROP ZONE //
document.getElementById('channel-container').addEventListener('dragleave', function(e) {
    drag_timer = window.setTimeout(function() {
        displayDropTargets(false);
    }, 500);
});


// FIRED CONSTANTLY WHILE A USER IS DRAGGING SOMETHING VALID AROUND (validity specified by preventingDefault and returning false) //
document.getElementById('channel-container').addEventListener('dragover', function(e) {
    window.clearTimeout(drag_timer);
    
    if(e.target.className == 'drag-target' || e.target.parentNode.className == 'drag-target') {
        e.preventDefault();
        return false;
    }
    
    if(e.target.tagName == 'TEXTAREA' && e.target.getAttribute('roomid')) {
        e.preventDefault();
        return false;
    }
    
});

// FIRED ONCE WHEN USER DROPS ON A VALID TARGET //
document.getElementById('channel-container').addEventListener('drop', function(e) {
    
    // File
    if(isHoldingFile(e)) {
        e.preventDefault();
        uploadToRoom(e);
        displayDropTargets(false);
        return false;
    }
    
    // Message
    else if(e.dataTransfer.getData('text/plain')) {
        var raw_content = e.dataTransfer.getData('text/plain'),
            room_id = e.target.getAttribute('roomid'),
            room = window.chatapp.getRoomById(room_id);
        
        Ajax('POST', '/room/' + room_id + '/message', ['user_id=' + window.current_user_id, 'message=' + raw_content, 'timestamp=' + room.getCurrentTimestamp()], function(r, c) {
            
            if(c == 201) {
                
                var message = get_json_objects_from_result(r)[0];
                
                message.message_sender_name = 'Quotation from ' + message.message_sender_name;
                
                room.appendMessage(room, message);
                
            }
            else {
                console.log(c + " error when quoting message: " + r);
            }
            
        });
        
    }
    
});

///////////
document.getElementById('channel-container').addEventListener('dragend', function(e) {
    //console.log("Drag end");
});

document.getElementById('channel-container').addEventListener('dragstart', function(e) {
    //console.log("Drag Start");
});

document.getElementById('channel-container').addEventListener('dragexit', function(e) {
    //console.log("Drag Exit");
});










