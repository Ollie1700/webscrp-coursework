<?php

class User {
	
    private $user_id;
    private $user_email;
    private $user_first_name;
    private $user_last_name;
    private $user_profile_pic = false; // User doesn't have a profile pic by default
    
    private $friends_list;
    private $rooms_list;
    
    public function __construct($user_id, $user_email, $user_first_name, $user_last_name, $load_friends_list = true) {
        global $db;
        
        // Initialise variables
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->user_first_name = $user_first_name;
        $this->user_last_name = $user_last_name;
        $this->friends_list = array();
        $this->rooms_list = array();
        
        // Get this user's friends list
        if($load_friends_list) {
            $sql = $db->prepare("SELECT `user_id_2` FROM friends WHERE user_id_1=?");
            $sql->execute(array($user_id));
            while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $this->friends_list[] = User::get($row['user_id_2'], false);
            }
        }
        
        // Get this user's room list
        $sql = $db->prepare("SELECT `room_id` FROM user_in_room WHERE user_id=?");
        $sql->execute(array($user_id));
        while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $this->rooms_list[] = Room::get($row['room_id']);
        }
        
        // Get this user's profile pic
        $sql = $db->prepare("SELECT `user_profile_pic` FROM user WHERE user_id=?");
        $success = $sql->execute(array($user_id));
        if($success && $sql->rowCount()) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $this->user_profile_pic = $row['user_profile_pic'];
        }
    }
    
    public function get_user_id() {
        return $this->user_id;
    }
    
    public function get_user_email() {
        return $this->user_email;
    }
    
    public function get_user_first_name() {
        return $this->user_first_name;
    }
    
    public function get_user_last_name() {
        return $this->user_last_name;
    }
    
    public function get_friends_list() {
        return $this->friends_list;
    }
    
    public function get_rooms_list() {
        return $this->rooms_list;
    }
    
    public function get_user_profile_pic() {
        return $this->user_profile_pic;
    }
    
    public function get_user_profile_pic_img() {
        // We add a dummy query string on to the image to prevent the browser from using
        // the cached version of the image. The reason for this is that if we upload a new
        // profile picture, it won't dynamically change client-side because it just takes the
        // cached version of the image.
        return '<img src="uploads/profile_pics/' . $this->user_profile_pic . '?dummy='.time().'">';
    }
    
    public function to_json() {
        return json_encode(
            array(
                'user_id' => $this->user_id,
                'user_email' => $this->user_email,
                'user_first_name' => $this->user_first_name,
                'user_last_name' => $this->user_last_name,
                'friends_list' => $this->friends_list,
                'rooms_list' => $this->rooms_list,
                'user_profile_pic' => $this->user_profile_pic,
                'user_profile_pic_img' => $this->get_user_profile_pic_img(),
            )
        );
    }
    
    public function set_profile_pic($profile_pic) {
        global $db;
        $sql = $db->prepare("UPDATE user SET user_profile_pic=? WHERE user_id=?");
        $success = $sql->execute(array($profile_pic, $this->user_id));
        if($success) {
            $this->user_profile_pic = $profile_pic;
            return $this->get_user_profile_pic_img();
        }
        return false;
    }
    
    public function join_room_by_name($room_name) {
        global $db;
        $sql = $db->prepare("SELECT room_id FROM room WHERE room_name=?");
        $success = $sql->execute(array($room_name));
        if($success && $sql->rowCount()) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            return $this->join_room($row['room_id']);
        }
        return false;
    }
    
    public function join_room($room_id) {
        global $db;
        $sql = $db->prepare("INSERT INTO user_in_room(`user_id`, `room_id`) VALUES(?, ?)");
        $success = $sql->execute(array($this->user_id, $room_id));
        if($success && $sql->rowCount()) {
            $new_room = Room::get($room_id);
            $this->rooms_list[] = $new_room;
            return $new_room;
        }
        return false;
    }
    
    public function leave_room($room_id) {
        global $db;
        $sql = $db->prepare("DELETE FROM user_in_room WHERE room_id=? AND user_id=?");
        $success = $sql->execute(array($room_id, $this->user_id));
        if($success && $sql->rowCount()) {
            foreach($this->rooms_list as $key => $room) {
                if($room_id == $room->get_room_id()) {
                    $temp = $this->rooms_list[$key];
                    $left_room = new Room($temp->get_room_id(), $temp->get_room_name());
                    unset($this->rooms_list[$key]);
                    return $left_room;
                }
            }
        }
        return false;
    }
    
    public function add_friend_by_email($friend_email) {
        global $db;
        $sql = $db->prepare("SELECT user_id FROM user WHERE user_email=?");
        $success = $sql->execute(array($friend_email));
        if($success && $sql->rowCount()) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            return $this->add_friend($row['user_id']);
        }
        return false;
    }
    
    public function add_friend($friend_id) {
        global $db;
        $sql = $db->prepare("INSERT INTO friends(`user_id_1`, `user_id_2`) VALUES(?, ?)");
        $success = $sql->execute(array($this->user_id, $friend_id));
        if($success && $sql->rowCount()) {
            $friend = User::get($friend_id);
            $this->friends_list[] = $friend;
            return $friend;
        }
        return false;
    }
    
    public function remove_friend_by_email($friend_email) {
        global $db;
        $sql = $db->prepare("SELECT user_id FROM user WHERE user_email=?");
        $success = $sql->execute(array($friend_email));
        if($success && $sql->rowCount()) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            return $this->remove_friend($row['user_id']);
        }
        return false;
    }
    
    public function remove_friend($friend_id) {
        global $db;
        $sql = $db->prepare("DELETE FROM friends WHERE user_id_1=? AND user_id_2=?");
        $success = $sql->execute(array($this->user_id, $friend_id));
        if($success && $sql->rowCount()) {
            foreach($this->friends_list as $key => $friend) {
                if($friend_id == $friend->get_user_id()) {
                    unset($this->friends_list[$key]);
                    break;
                }
            }
            return true;
        }
        return false;
    }
    
    public static function login($user_email, $password) {
        global $db;
        
        $sql = $db->prepare("SELECT * FROM user WHERE user_email=?");
        $success = $sql->execute(array($user_email));
        
        if($success && $sql->rowCount()) {
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['user_password'])) {
                $user = new User($row['user_id'], $row['user_email'], $row['user_first_name'], $row['user_last_name']);
                $_SESSION['user_id'] = $user->get_user_id();
                return $user;
            }
        }
        return false;
    }
    
    public static function create($user_email, $user_first_name, $user_last_name, $user_password) {
        global $db;
        $sql = $db->prepare("INSERT INTO user(`user_email`, `user_first_name`, `user_last_name`, `user_password`) VALUES(?, ?, ?, ?)");
        $success = $sql->execute(array($user_email, $user_first_name, $user_last_name, password_hash($user_password, PASSWORD_DEFAULT)));
        return $success && $sql->rowCount() ? new User($db->lastInsertId(), $user_email, $user_first_name, $user_last_name) : false;
    }
    
    public static function update($user_id, $user_email, $user_first_name, $user_last_name) {
        global $db;
        $sql = $db->prepare("UPDATE user SET user_email=?, user_first_name=?, user_last_name=? WHERE user_id=?");
        $success = $sql->execute(array($user_email, $user_first_name, $user_last_name, $user_id));
        return $success && $sql->rowCount() ? new User($user_id, $user_email, $user_first_name, $user_last_name) : false;
    }
    
    public static function get($user_id = false, $load_friends_list = true) {
        global $db;
        
        if($user_id) {
            $sql = $db->prepare("SELECT * FROM user WHERE user_id=?");
            $success = $sql->execute(array($user_id));
            
            if($success && $sql->rowCount()) {
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                return new User($row['user_id'], $row['user_email'], $row['user_first_name'], $row['user_last_name'], $load_friends_list);
            }
            
            return false;
        }
        else {
            $sql = $db->prepare("SELECT * FROM user");
            $success = $sql->execute();
            
            if($success && $sql->rowCount()) {
                $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
                
                $users_array = array();
                
                foreach($rows as $row) {
                    $users_array[] = new User($row['user_id'], $row['user_email'], $row['user_first_name'], $row['user_last_name'], $load_friends_list);
                }
                
                return $users_array;
            }
            
            return false;
        }
    }
    
    public static function delete($user_id) {
        global $db;
        $sql = $db->prepare("DELETE FROM user WHERE user_id=?");
        $sql->execute(array($user_id));
        return $sql->rowCount();
    }
	
}


































