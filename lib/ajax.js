function Ajax(verb, resource, params, callback) {
    
    "use strict";

	var
        ajaxObj = new XMLHttpRequest(),
        param_str = '?';
    
    if (params !== null) {
        for (var i = 0; i < params.length; i++) {
            var 
                keyValue = params[i].split('='),
                key = keyValue[0],
                value = keyValue[1];
            
            param_str += key + '=' + escape(value) + '&';
        }
    }
    
	ajaxObj.open(verb, "api" + resource + param_str, true); // The TRUE implies asynchronous
	ajaxObj.onreadystatechange = function() {
        
        if (ajaxObj.readyState == 4) {
            
            var responseText = unescape(ajaxObj.responseText);
            
            // OK
            if (ajaxObj.status == 200) {
                return callback(responseText);
            }

            // CREATED (POST / PUT was successful)
            else if (ajaxObj.status == 201) {
                return callback(responseText);
            }

            // BAD REQUEST (POST / PUT was handed invalid data)
            else if (ajaxObj.status == 400) {
                console.log('400 - ' + responseText);
            }

            // UNAUTHORISED
            else if (ajaxObj.status == 401) {
                console.log('401 - ' + responseText);
            }

            // NOT FOUND
            else if (ajaxObj.status == 404) {
                console.log('404 - ' + responseText);
            }

            // METHOD NOT ALLOWED (an unsupported http verb was used)
            else if (ajaxObj.status == 405) {
                console.log('405 - ' + responseText);
            }

            // CONFLICT (POST / PUT is attempting to create the same resource twice)
            else if (ajaxObj.status == 409) {
                console.log('409 - ' + responseText);
            }

            // INTERNAL SERVER ERROR (when all else fails)
            else if (ajaxObj.status == 500) {
                console.log('500 - ' + responseText);
            }
        }
	}
	ajaxObj.send();

}

function get_json_objects_from_result(result) {
    var objs = [], recovery;
    
    // If the string doesn't start with a { then it isn't JSON, it's a PHP error
    if(!(result.lastIndexOf('{', 0) === 0)) {
        //TODO JSON is recoverable at this stage if there is any
        recovery = result.split('{');
        console.log('[PHP Output] ' + recovery[0]);
        return null;
    }
    
    result = result.slice(1, -1).split('}{');
    for (var i = 0; i < result.length; i++) {
        objs.push(JSON.parse("{" + result[i] + "}"));
    }
    return objs;
}



















