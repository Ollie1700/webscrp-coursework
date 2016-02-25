
var 
    
    addDragListeners = function(target) {
        
        window.addEventListener('dragenter', function(e) {
            
            //target.style.display = 'block';
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
        window.addEventListener('dragexit', function(e) {
            
            //target.style.display = 'none';
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
        target.addEventListener('dragover', function(e) {
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
        target.addEventListener('drop', function(e) {
            
            var i,
                files = e.dataTransfer.files,
                fd = new FormData(),
                room_id = e.target.getAttribute('roomid');
            
            for(i = 0; i < files.length; i++) {
                fd.append('file_' + i, files[i]);
            }
            
            Ajax('POST', '/room/' + room_id + '/upload', fd, function(r, c) {
                console.log(r);
            });
            
            // Prevent default behaviour
            e.preventDefault();
        });
    },
    
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
                }
                else {
                    console.log("Error " + c + ": " + r);
                }
            });
            
            document.getElementById('profile-pic-upload').removeAttribute('class');
            
            // Prevent default behaviour
            e.preventDefault();
        });
        
    };












