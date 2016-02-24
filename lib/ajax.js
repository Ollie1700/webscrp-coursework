function Ajax(verb, resource, data, callback) {
    
    "use strict";

	var
        ajaxObj = new XMLHttpRequest(),
        param_str = '?',
        isFormData = (data instanceof FormData);
    
    if (data !== null && !isFormData) {
        for (var i = 0; i < data.length; i++) {
            var 
                keyValue = data[i].split(/=(.+)/), // This regex splits the string at the 1st equals only. This prevents unexpected behaviour when the message itself contains =
                key = keyValue[0],
                value = keyValue[1];
            
            param_str += key + '=' + escape(value) + (i + 1 == data.length ? '' : '&');
        }
    }
    
	ajaxObj.open(verb, "api" + resource + param_str, true); // The TRUE implies asynchronous
	ajaxObj.onreadystatechange = function() {
        
        if (ajaxObj.readyState == 4) {
            
            var responseText = unescape(ajaxObj.responseText);
            
            // OK
            if (ajaxObj.status == 200) {
                return callback(responseText, ajaxObj.status);
            }

            // CREATED (POST / PUT was successful)
            else if (ajaxObj.status == 201) {
                return callback(responseText, ajaxObj.status);
            }

            // BAD REQUEST (POST / PUT was handed invalid data)
            else if (ajaxObj.status == 400) {
                console.log('400 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }

            // UNAUTHORISED
            else if (ajaxObj.status == 401) {
                console.log('401 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }

            // NOT FOUND
            else if (ajaxObj.status == 404) {
                console.log('404 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }

            // METHOD NOT ALLOWED (an unsupported http verb was used)
            else if (ajaxObj.status == 405) {
                console.log('405 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }

            // CONFLICT (POST / PUT is attempting to create the same resource twice)
            else if (ajaxObj.status == 409) {
                console.log('409 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }

            // INTERNAL SERVER ERROR (when all else fails)
            else if (ajaxObj.status == 500) {
                console.log('500 - ' + responseText);
                return callback(responseText, ajaxObj.status);
            }
        }
	}
    
    if(isFormData) {
	   ajaxObj.send(data);
    }
    else {
        ajaxObj.send();
    }

}

function get_json_objects_from_result(result) {
    var objs = [], recovery;
    
    // If the result is empty, just return an empty array
    if(!result) {
        return [];
    }
    
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



















