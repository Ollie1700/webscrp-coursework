
var 

    addDragListeners = function(target) {
        target.addEventListener("dragover", function(e) {
            e.preventDefault();
        });
        
        target.addEventListener("drop", function(e) {
            e.preventDefault();
            
        });
    }