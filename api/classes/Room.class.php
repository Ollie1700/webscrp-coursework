<?php

require_once 'Message.class.php';

class Room {
	
	private $room_id;
	private $room_name;
    private $room_admin_id;
    
    public function __construct($room_id, $room_name) {
        $this->room_id = $room_id;
        $this->room_name = $room_name;
    }
	
    public function get_room_id() {
        return $this->room_id;
    }
    
    public function get_room_name() {
        return $this->room_name;
    }
    
    public function get_messages() {
        global $db;
        $sql = $db->prepare("SELECT * FROM message WHERE room_id=?");
        $sql->execute(array($this->room_id));
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        $messages_array = array();
        
        foreach($rows as $row) {
            $messages_array[] = new Message($row['message_id'], $row['room_id'], $row['user_id'], $row['message_message'], $row['message_raw_content'], $row['message_timestamp'], $row['message_deleted']);
        }
        
        return $messages_array;
    }
    
    public function get_room_admin_id() {
        global $db;
        $sql = $db->prepare("SELECT `room_admin_id` FROM room WHERE room_id=?");
        $sql->execute(array($this->room_id));
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $rows[0]['room_admin_id'];
    }
    
    public function get_user_status() {
        global $db;
        $sql = $db->prepare("SELECT user_in_room.user_id, user_in_room.user_status, user.user_first_name, user.user_last_name FROM user_in_room INNER JOIN user ON user_in_room.user_id=user.user_id AND user_in_room.room_id=?");
        $sql->execute(array($this->room_id));
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    
    public function to_json() {
        return json_encode(
            array(
                'room_id' => $this->room_id,
                'room_name' => $this->room_name,
                'room_admin_id' => $this->get_room_admin_id(),
                'user_status' => $this->get_user_status(),
            )
        );
    }
    
    public static function create($room_name, $admin_id) {
        global $db;
        $sql = $db->prepare("INSERT INTO room(`room_name`, `room_admin_id`) VALUES(?, ?)");
        $success = $sql->execute(array($room_name, $admin_id));
        return $success && $sql->rowCount() ? new Room($db->lastInsertId(), $room_name) : false;
    }
    
    public static function update($room_id, $room_name) {
        global $db;
        $sql = $db->prepare("UPDATE room SET room_name=? WHERE room_id=?");
        $success = $sql->execute(array($room_name, $room_id));   
        return $success && $sql->rowCount() ? new Room($room_id, $room_name) : false;
    }
    
    public static function get($room_id = false) {
        global $db;
        
        if($room_id) {
            $sql = $db->prepare("SELECT * FROM room WHERE room_id=?");
            $success = $sql->execute(array($room_id));
            
            if($success && $sql->rowCount()) {
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                return new Room($row['room_id'], $row['room_name']);
            }
            
            return false;
        }
        else {
            $sql = $db->prepare("SELECT * FROM room");
            $success = $sql->execute();
            
            if($success && $sql->rowCount()) {
                $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

                $rooms_array = array();

                foreach($rows as $row) {
                    $rooms_array[] = new Room($row['room_id'], $row['room_name']);
                }

                return $rooms_array;
            }
            
            return false;
        }
    }
    
    public static function delete($room_id) {
        global $db;
        $sql = $db->prepare("DELETE FROM room WHERE room_id=?");
        $sql->execute(array($room_id));
        return $sql->rowCount();
    }
    
}