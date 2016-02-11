<!DOCTYPE html>
<html>
    <head>
        <title>TESTING AJAX</title>
        
        <script src="lib/ajax.js"></script>
        
    </head>
    <body>
        
        Rooms:
        <div id="rooms"></div>
        
        <script>
            
            var y = document.getElementById("rooms");
            
            Ajax("GET", "/room", null, function(r) {
                
                r = r.slice(1, -1).split('}{');
                
                for(var i = 0; i < r.length; i++) {
                    var obj = JSON.parse("{" + r[i] + "}");
                    
                    y.innerHTML += obj.room_name + "<br>";
                }
                
            });
            
        </script>
    </body>
</html>