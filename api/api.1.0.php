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

                                case 'PUT':

                                case 'GET':

                                case 'DELETE':
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
                                    
                                    $created_message = Message::create($room_id, $user_id, $message_message, $timestamp);
                                    echo $created_message->to_json();
                                    
                                    exit_with_status_code(201);
                                    
                                case 'PUT':break;

                                case 'GET':
                                    
                                    $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
                                    
                                    $room = Room::get($room_id);
                                    
                                    $messages = $room->get_messages();
                                    
                                    if($limit >= sizeof($messages)) {
                                        // Return nothing and exit
                                        exit_with_status_code(200);
                                    }
                                    
                                    $sliced = array_slice($messages, $limit);
                                    
                                    foreach($messages as $msg) {
                                        echo $msg->to_json();
                                    }
                                    
                                    exit_with_status_code(200);

                                case 'DELETE':
                            }
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
                    
                    $room_name = $_REQUEST['name'];
                    
                    if(empty($room_name)) {
                        echo 'Room name cannot be null.';
                        exit_with_status_code(400);
                    }
                    else {
                        $room = Room::create($room_name);
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
                    
                    if(empty($email) || empty($first_name) || empty($last_name)) {
                        echo 'Email, first name and last name can\'t be empty.';
                        exit_with_status_code(400);
                    }
                    else {
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
