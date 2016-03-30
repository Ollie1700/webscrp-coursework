<?php

require_once 'init.php';

// Parse the request and see what we can do

$uri = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
$verb = $_SERVER['REQUEST_METHOD'];

// Trim the array down to the API request
while(array_shift($uri) != 'api');

$noun = array_shift($uri);

switch($noun) {
    
    case 'room':
        
        $room_id = array_shift($uri);
        
        /**
         * Path: /room/{room_id}
         * 
         * Parameters: name
         */
        if(!empty($room_id)) {
            
            $noun_2 = array_shift($uri);
            
            if($noun_2) {
                
                switch($noun_2) {
                    case 'message':
                        
                        $message_id = array_shift($uri);
                        
                        if($message_id) {
                            switch($verb) {
                                case 'POST':
                                    
                                    echo 'Cannot overwrite existing message';
                                    exit_with_status_code(409);
                                    
                                case 'PUT':
                                    
                                    $user_id = $_REQUEST['user_id'];
                                    $message_message = $_REQUEST['message_message'];
                                    $message_timestamp = $_REQUEST['message_timestamp'];
                                    
                                    if(!isset($user_id) || !isset($message_message) || !isset($message_timestamp)) {
                                        echo 'You must provide user id, message and timestamp to update a message.';
                                        exit_with_status_code(400);
                                    }
                                    
                                    $message_raw_content = $message_message;
                                    
                                    $message_message = parse_message($message_message);
                                    
                                    $message = Message::update($message_id, $user_id, $message_message, $message_raw_content, $message_timestamp);
                                    
                                    if(!$message) {
                                        echo 'Failed to update message';
                                        exit_with_status_code(500);
                                    }
                                    
                                    echo $message->to_json();
                                    exit_with_status_code(201);
                                    
                                case 'GET': break;
                                case 'DELETE':
                                    
                                    $user_id = $_REQUEST['user_id'];
                                    $message_timestamp = $_REQUEST['message_timestamp'];
                                    
                                    if(!isset($user_id) || !isset($message_timestamp)) {
                                        echo 'You must provide user id and timestamp to delete a message.';
                                        exit_with_status_code(400);
                                    }
                                    
                                    $message = Message::update($message_id, $user_id, '<em>Message deleted.</em>', '<em>Message deleted.</em>', $message_timestamp);
                                    
                                    $success = $message->delete_message();
                                    
                                    if(!$success) {
                                        echo 'Failed to delete message';
                                        exit_with_status_code(500);
                                    }
                                    
                                    echo $message->to_json();
                                    exit_with_status_code(200);
                            }
                        }
                        else {
                            switch($verb) {
                                case 'POST':
                                    
                                    $user_id = $_REQUEST['user_id'];
                                    $message_message = $_REQUEST['message'];
                                    $timestamp = $_REQUEST['timestamp'];
                                    
                                    if(empty($user_id) || empty($message_message) || empty($timestamp)) {
                                        echo 'User ID, message and timestamp must be provided.';
                                        exit_with_status_code(400);
                                    }
                                    
                                    $message_raw_content = $message_message;
                                    
                                    $message_message = parse_message($message_message);
                                    
                                    $created_message = Message::create($room_id, $user_id, $message_message, $message_raw_content, $timestamp);
                                    echo $created_message->to_json();
                                    
                                    exit_with_status_code(201);
                                    
                                case 'PUT':break;

                                case 'GET':
                                    
                                    $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 0;
                                    
                                    $room = Room::get($room_id);
                                    
                                    if(!$room) { 
                                        echo 'Could not get room with id of ' . $room_id . '.';
                                        exit_with_status_code(500);
                                    }
                                    
                                    $messages = $room->get_messages();
                                    
                                    if($limit >= count($messages)) {
                                        // Return nothing and exit
                                        exit_with_status_code(200);
                                    }
                                    
                                    $sliced = array_slice($messages, $limit);
                                    
                                    foreach($sliced as $msg) {
                                        echo $msg->to_json();
                                    }
                                    
                                    exit_with_status_code(200);

                                case 'DELETE': break;
                            }
                        }
                        break;
                        
                    case 'upload':
                        
                        switch($verb) {
                            case 'POST': 
                                
                                foreach($_FILES as $file) {
                                    $info = pathinfo($file['name']);
                                    $ext = strtolower($info['extension']); // make lower case to avoid confusion with checks later
                                    
                                    // Check if the extension is valid
                                    if($ext == 'jpg' ||
                                       $ext == 'gif' ||
                                       $ext == 'png') { // Can add more later
                                        
                                        move_uploaded_file($file['tmp_name'], '../uploads/' . $file['name']);
                                    }
                                    else {
                                        echo 'Didn\'t upload ' . $file['name'] . ' - it was not of a valid filetype.\n';
                                        echo 'You are only allowed to upload jpg, gif and png files.';
                                        exit_with_status_code(400);
                                    }
                                }
                                
                                $message = '<img class="embedded-image" src="uploads/' . $file['name'] . '">';
                                
                                $created_message = Message::create($_REQUEST['room_id'], $_REQUEST['user_id'], $message, $message, $_REQUEST['timestamp']);
                                echo $created_message->to_json();
                                
                                exit_with_status_code(201);
                                
                            case 'PUT': break;
                            case 'GET': break;
                            case 'DELETE': break;
                        }
                        
                        break;
                }
                
            }
            else {
                switch($verb) {

                    case 'POST':

                        echo 'Cannot overwrite existing room.';
                        exit_with_status_code(409);

                    case 'PUT':

                        $room_name = $_REQUEST['name'];

                        if(empty($room_name)) {
                            echo 'Room name cannot be null.';
                            exit_with_status_code(400);
                        }
                        else {
                            $updated_room = Room::update($room_id, $room_name);
                            if($updated_room) {
                                echo 'Successfully updated room.';
                                exit_with_status_code(201);
                            }
                            else {
                                echo 'Error updating room.';
                                exit_with_status_code(400);
                            }
                        }

                    case 'GET':

                        $room = Room::get($room_id);

                        if($room) {
                            echo $room->to_json();
                            exit_with_status_code(200);
                        }
                        else {
                            echo 'Room not found.';
                            exit_with_status_code(404);
                        }

                    case 'DELETE':

                        $result = Room::delete($room_id);

                        if($result) {
                            echo 'Successfully deleted room';
                            exit_with_status_code(200);
                        }
                        else {
                            echo 'The room you are attempting to delete doesn\'t exist';
                            exit_with_status_code(404);
                        }

                    default:
                        exit_with_status_code(405);

                }
            }
        }
        
        /**
         * Path: /room
         * 
         * Parameters: name
         */
        else {
            switch($verb) {
                    
                case 'POST':
                    
                    if(!isset($_REQUEST['name']) || !isset($_REQUEST['admin_id'])) {
                        echo 'Room name and admin ID are required.';
                        exit_with_status_code(400);
                    }
                    else {
                        $room = Room::create($_REQUEST['name'], $_REQUEST['admin_id']);
                        if($room) {
                            echo 'Room successfully created.';
                            exit_with_status_code(201);
                        }
                        else {
                            echo 'Error creating room';
                            exit_with_status_code(400);
                        }
                    }
                    
                case 'PUT':
                    
                    echo 'Operation not supported';
                    exit_with_status_code(405);
                    
                case 'GET':
                    
                    $rooms = Room::get();
                    
                    if($rooms) {
                        foreach($rooms as $room) {
                            echo $room->to_json();
                        }
                        exit_with_status_code(200);
                    }
                    else {
                        echo 'Error getting rooms.';
                        exit_with_status_code(500);
                    }
                    
                case 'DELETE':
                    
                    echo 'Cannot delete all rooms at once.';
                    
                default:
                    exit_with_status_code(405);
            }
        }
        
        break;
        
    case 'user':
        
        $user_id = array_shift($uri);
        
        /**
         * Path: /user/{user_id}
         * 
         * Parameters: email, first_name, last_name
         */
        if(!empty($user_id)) {
            
            $noun_2 = array_shift($uri);
            
            if($noun_2) {
                
                switch($noun_2) {
                    case 'friend':

                        switch($verb) {
                            case 'POST': 
                                
                                if(isset($_REQUEST['friend_email'])) {

                                    $friend_email = $_REQUEST['friend_email'];

                                    $user = User::get($user_id);

                                    $friend = $user->add_friend_by_email($friend_email);

                                    if($friend) {
                                        echo $friend->to_json();
                                        exit_with_status_code(201);
                                    }
                                    else {
                                        echo 'User not found!';
                                        exit_with_status_code(404);
                                    }

                                }
                                else {
                                    echo 'The email of the friend you wish to add is required.';
                                    exit_with_status_code(400);
                                }
                                
                            case 'PUT': break;
                            case 'GET':

                                $user = User::get($user_id);
                                
                                if(!$user) {
                                    echo "Couldn't get user.";
                                    exit_with_status_code(500);
                                }

                                $friends = $user->get_friends_list();

                                foreach($friends as $friend) {
                                    echo $friend->to_json();
                                }

                                exit_with_status_code(200);

                            case 'DELETE':
                                
                                if(isset($_REQUEST['friend_id'])) {

                                    $user = User::get($user_id);

                                    $success = $user->remove_friend($_REQUEST['friend_id']);

                                    if($success) {
                                        echo 'Successfully removed friend.';
                                        exit_with_status_code(200);
                                    }
                                }
                               else {
                                   echo 'The ID of the friend you wish to remove is required.';
                                   exit_with_status_code(400);
                               }
                                
                        }
                        
                        break;
                        
                    case 'room':
                        switch($verb) {
                            case 'POST':
                                
                                $user = User::get($user_id);
                                
                                if(isset($_REQUEST['room_name'])) {
                                    $room_name = $_REQUEST['room_name'];
                                    
                                    // Check if the user is already part of this room
                                    foreach($user->get_rooms_list() as $r) {
                                        if($r->get_room_name() == $room_name) {
                                            echo 'You are already in this room.';
                                            exit_with_status_code(409);
                                        }
                                    }
                                    
                                    $success = $user->join_room_by_name($room_name);
                                    if($success) {
                                        echo $success->to_json();
                                        exit_with_status_code(201);
                                    }
                                    else {
                                        echo 'Room not found.';
                                        exit_with_status_code(404);
                                    }
                                }
                                else {
                                    echo 'The name of the room you wish to join is required';
                                    exit_with_status_code(400);
                                }
                                
                            case 'PUT': break;
                            case 'GET':
                                
                                $user = User::get($user_id);
                                
                                if(!$user) {
                                    echo "Couldn't get user.";
                                    exit_with_status_code(500);
                                }
                                
                                $rooms = $user->get_rooms_list();
                                
                                foreach($rooms as $room) {
                                    echo $room->to_json();
                                }
                                
                                exit_with_status_code(200);
                                
                            case 'DELETE': 
                                
                                $user = User::get($user_id);
                                
                                if(isset($_REQUEST['room_id'])) {
                                    $room_id = $_REQUEST['room_id'];
                                    $success = $user->leave_room($room_id);
                                    if($success) {
                                        echo $success->to_json();
                                        exit_with_status_code(200);
                                    }
                                    else {
                                        echo 'Room not found or you are not a part of that room.';
                                        exit_with_status_code(404);
                                    }
                                }
                                else {
                                    echo 'The ID of the room you wish to leave is required.';
                                    exit_with_status_code(400);
                                }
                                
                        }
                        
                    case 'profilepic':
                        switch($verb) {
                            case 'POST':
                                
                                $user = User::get($user_id);
                                
                                foreach($_FILES as $file) {
                                    $info = pathinfo($file['name']);
                                    $ext = strtolower($info['extension']); // make lower case to avoid confusion with checks later
                                    
                                    // Check if the extension is valid
                                    if($ext == 'jpg' ||
                                       $ext == 'gif' ||
                                       $ext == 'png') { // Can add more later
                                        
                                        $file_name = md5($user_id) . '.' . $ext;
                                        $upload_path = '../uploads/profile_pics/' . $file_name;
                                        
                                        move_uploaded_file($file['tmp_name'], $upload_path);
                                        
                                        $success = $user->set_profile_pic($file_name);
                                        
                                        if($success) {
                                            echo $user->get_user_profile_pic_img();
                                            exit_with_status_code(201);
                                        }
                                        else {
                                            echo 'Failed to set the profile picture in the database after it was uploaded.';
                                            exit_with_status_code(500);
                                        }
                                    }
                                    else {
                                        echo 'Didn\'t upload ' . $file['name'] . ' - it was not of a valid filetype.\n';
                                        echo 'You are only allowed to upload jpg, gif and png files.';
                                        exit_with_status_code(400);
                                    }
                                }
                                
                            case 'PUT': break;
                            case 'GET':
                                
                                $user = User::get($user_id);
                                
                                $has_profile_pic = $user->get_user_profile_pic();
                                
                                if($has_profile_pic) {
                                    echo $user->get_user_profile_pic_img();
                                    exit_with_status_code(200);
                                }
                                else {
                                    echo 'User does not have profile picture.';
                                    exit_with_status_code(404);
                                }
                                
                            case 'DELETE': break;
                        }
                        break;
                }
                
            }
            else {
                switch($verb) {

                    case 'POST':

                        echo 'Cannot overwrite existing user.';
                        exit_with_status_code(409);

                    case 'PUT':

                        $email = $_REQUEST['email'];
                        $first_name = $_REQUEST['first_name'];
                        $last_name = $_REQUEST['last_name'];

                        if(empty($email) || empty($first_name) || empty($last_name)) {
                            echo 'Email, first name and last name must have a value.';
                            exit_with_status_code(400);
                        }
                        else {
                            $updated_user = User::update($user_id, $email, $first_name, $last_name);
                            if($updated_user) {
                                echo 'Successfully updated user.';
                                exit_with_status_code(201);
                            }
                            else {
                                echo 'Failed to update user.';
                                exit_with_status_code(400);
                            }
                        }

                    case 'GET':

                        $user = User::get($user_id);

                        if($user) {
                            echo $user->to_json();
                            exit_with_status_code(200);
                        }
                        else {
                            echo 'User not found.';
                            exit_with_status_code(404);
                        }

                    case 'DELETE':

                        $result = User::delete($user_id);

                        if($result) {
                            echo 'Successfully deleted user';
                            exit_with_status_code(200);
                        }
                        else {
                            echo 'The user you are attempting to delete doesn\'t exist';
                            exit_with_status_code(404);
                        }

                    default:
                        exit_with_status_code(405);
                }
            }
        }
        
        /**
         * Path: /user
         * 
         * Parameters: email, first_name, last_name, password
         */
        else {
            switch($verb) {
                    
                case 'POST':
                    
                    $email = $_REQUEST['email'];
                    $first_name = $_REQUEST['first_name'];
                    $last_name = $_REQUEST['last_name'];
                    $password = $_REQUEST['password'];
                    
                    if(empty($email) || empty($first_name) || empty($last_name) || empty($password)) {
                        echo 'Email, password, first name and last name can\'t be empty.';
                        exit_with_status_code(400);
                    }
                    else {
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $user = User::create($email, $first_name, $last_name, $password);
                            if($user) {
                                echo 'User successfully created.';
                                exit_with_status_code(201);
                            }
                            else {
                                echo 'Error creating user.';
                                exit_with_status_code(400);
                            }
                        }
                        else {
                            echo 'Please enter a valid email address.';
                            exit_with_status_code(400);
                        }
                    }
                    
                case 'PUT':
                    
                    echo 'Operation not supported';
                    exit_with_status_code(405);
                    
                case 'GET':
                    
                    $users = User::get();
                    
                    if($users) {
                        foreach($users as $user) {
                            echo $user->to_json();
                        }
                        exit_with_status_code(200);
                    }
                    else {
                        echo 'Error getting users.';
                        exit_with_status_code(500);
                    }
                    
                case 'DELETE':
                    
                    echo 'Cannot delete all users at once.';
                    
                default:
                    exit_with_status_code(405);
            }
        }
        
        break;
    
    default:
        // Not found
        break;
    
}

/**
 * Parses a message for links, images, emotes, and makes the message html safe
 */
function parse_message($message) {
    $message_message = $message;
    
    // Turn all tags into html safe
    $message_message = str_replace('<', '&lt;', $message_message);
    $message_message = str_replace('>', '&gt;', $message_message);

    // Parse the message for emotes, images and links //

    // Links

    $links = explode('http', $message_message);

    foreach($links as $link) {
        if(empty($link)) continue;

        $str = explode(' ', $link)[0];

        // Image
        $img_extension = substr($str, -4);

        if($img_extension == '.jpg' || $img_extension == '.gif' || $img_extension == '.png') {
            $message_message = str_replace('http' . $str, '<img class="embedded-image" src="http'.$str.'">', $message_message);
        }
        // Normal link
        else {
            $message_message = str_replace('http' . $str, '<a href="http'.$str.'" target="_blank">http'.$str.'</a>', $message_message);
        }
    }

    // Emotes

    $emote_code = array(
        ':)',
        ':(',
        ':D',
        ':O',
        ':l',
        'Kappa',
        'chrisLeTricked',
        'jpHelp',
        'danFiesta',
        'paulYesPlease',
        'ollieWasted',
        'jordanBlueSteel',
        'qtpTilt',
        'vapeNation',
    );

    $emote_images = array(
        '<img class="emote" title=":)" src="img/emote/happy.png">',
        '<img class="emote" title=":(" src="img/emote/sad.png">',
        '<img class="emote" title=":D" src="img/emote/very_happy.png">',
        '<img class="emote" title=":O" src="img/emote/surprised.png">',
        '<img class="emote" title=":l" src="img/emote/straight_face.png">',
        '<img class="emote" title="Kappa" src="img/emote/grey_face_no_space.png">',
        '<img class="emote" title="chrisLeTricked" src="img/emote/chrisLeTricked.jpg">',
        '<img class="emote" title="jpHelp" src="img/emote/jpHelp.jpg">',
        '<img class="emote" title="danFiesta" src="img/emote/danFiesta.jpg">',
        '<img class="emote" title="paulYesPlease" src="img/emote/paulYesPlease.jpg">',
        '<img class="emote" title="ollieWasted" src="img/emote/ollieWasted.jpg">',
        '<img class="emote" title="jordanBlueSteel" src="img/emote/jordanBlueSteel.jpg">',
        '<img class="emote" title="qtpTilt" src="img/emote/qtpTilt.png">',
        '<img class="emote" title="vapeNation" src="img/emote/vapeNation.png">',
    );

    $message_message = str_replace($emote_code, $emote_images, $message_message);
    
    return $message_message;
}
























