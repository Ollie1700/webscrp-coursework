
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
    };

