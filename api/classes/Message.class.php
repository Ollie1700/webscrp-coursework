<?php

class Message {
	
	private $message_id;
    private $room_id;
    private $user_id;
    private $message_message;
    private $message_raw_content;
    private $message_timestamp;
    private $message_deleted;
    
    public function __construct($message_id, $room_id, $user_id, $message_message, $message_raw_content, $message_timestamp, $message_deleted = 0) {
        $this->message_id = $message_id;
        $this->room_id = $room_id;
        $this->user_id = $user_id;
        $this->message_message = $message_message;
        $this->message_raw_content = $message_raw_content;
        $this->message_timestamp = $message_timestamp;
        $this->message_deleted = $message_deleted;
    }
    
    public function get_message_id() {
        return $this->message_id;
    }
    
    public function get_user_id() {
        return $this->user_id;
    }
    
    public function get_room_id() {
        return $this->room_id;
    }
    
    public function get_message_message() {
        return $this->message_message;
    }
    
    public function get_message_raw_content() {
        return $this->message_raw_content;
    }
    
    public function get_message_timestamp() {
        return $this->message_timestamp;
    }
    
    public function is_message_deleted() {
        return $this->message_deleted;
    }
    
    public function set_message_deleted($del) {
        $this->message_deleted = $del;
    }
    
    public function get_sender_name() {
        global $db;
        $sql = $db->prepare("SELECT user_first_name, user_last_name FROM user WHERE user_id=?");
        $sql->execute(array($this->user_id));
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['user_first_name'] . ' ' . $result['user_last_name'];
    }
    
    public function to_json() {
        return json_encode(
            array(
                'message_id' => $this->message_id,
                'room_id' => $this->room_id,
                'user_id' => $this->user_id,
                'message_message' => $this->message_message,
                'message_raw_content' => $this->message_raw_content,
                'message_timestamp' => $this->message_timestamp,
                'message_sender_name' => $this->get_sender_name(),
                'message_deleted' => intval($this->message_deleted),
            )
        );
    }
    
    public function delete_message() {
        global $db;
        $sql = $db->prepare("UPDATE message SET message_deleted=? WHERE message_id=?");
        $success = $sql->execute(array(1, $this->message_id));
        if($success && $sql->rowCount()) {
            $this->message_deleted = 1;
            return true;
        }
        return false;
    }
	
    public static function create($room_id, $user_id, $message_message, $message_raw_content, $message_timestamp) {
        global $db;
        $sql = $db->prepare("INSERT INTO message(`room_id`, `user_id`, `message_message`, `message_raw_content`, `message_timestamp`) VALUES(?, ?, ?, ?, ?)");
        $success = $sql->execute(array($room_id, $user_id, $message_message, $message_raw_content, $message_timestamp));
        return $success && $sql->rowCount() ? new Message($db->lastInsertId(), $room_id, $user_id, $message_message, $message_raw_content, $message_timestamp) : false;
    }
    
    public static function update($message_id, $user_id, $message_message, $message_raw_content, $message_timestamp) {
        global $db;
        $sql = $db->prepare("UPDATE message SET user_id=?, message_message=?, message_raw_content=?, message_timestamp=? WHERE message_id = ?");
        $success = $sql->execute(array($user_id, $message_message, $message_raw_content, $message_timestamp, $message_id));
        $results = $sql->fetch(PDO::FETCH_ASSOC);
            
        return $success && $sql->rowCount() ? new Message($message_id, $results['room_id'], $user_id, $message_message, $message_raw_content, $message_timestamp) : false;
    }
    
    public static function get($message_id = false) {
        global $db;
        
        if($message_id) {
            $sql = $db->prepare("SELECT * FROM message WHERE message_id=?");
            $success = $sql->execute(array($message_id));
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if($success && $sql->rowCount()) {
                $msg = new Message($row['message_id'], $row['room_id'], $row['user_id'], $row['message_message'], $row['message_raw_content'], $row['message_timestamp'], $row['message_deleted']);
                return $msg;
            }
            return false;
        }
        else {
            $sql = $db->prepare("SELECT * FROM message");
            $success = $sql->execute();
            $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
            
            $messages_array = array();
            
            foreach($rows as $row) {
                $msg = new Message($row['message_id'], $row['room_id'], $row['user_id'], $row['message_message'], $row['message_raw_content'], $row['message_timestamp'], $row['message_deleted']);
                $msg->set_message_deleted($row['message_deleted']);
                $messages_array[] = $msg;
            }
            
            return $success && $sql->rowCount() ? $messages_array : false;
        }
    }
    
    public static function delete($message_id) {
        global $db;
        $sql = $db->prepare("DELETE FROM message WHERE message_id=?");
        $affected_rows = $sql->execute(array($message_id));
        return $affected_rows;
    }
    
}


























