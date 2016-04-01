<?php

// Start the session
session_start();

// CONFIG

$HOST = 'localhost';
$DB_NAME = 'chatapp';
$USERNAME = 'root';
$PASSWORD = '';

// Set up a log method (we're gonna need it)
function log_this($message) {
    $fd = fopen('log.txt', "a");
    fwrite($fd, '[' . date('j M y, H:i:s') . '] ' . $message . PHP_EOL);
    fclose($fd);
}

// Exit with http status code
function exit_with_status_code($code) {
    http_response_code($code);
    ob_end_flush();
    exit();
}

// Initialise the database
$db = new PDO('mysql:host=' . $HOST . ';dbname=' . $DB_NAME . ';charset=utf8', $USERNAME, $PASSWORD);

// Class imports
require_once 'classes/Message.class.php';
require_once 'classes/Room.class.php';
require_once 'classes/User.class.php';

// Initialise the user if there is one
if(isset($_SESSION['user_id'])) {
    $CURRENT_USER = User::get($_SESSION['user_id']);
}
