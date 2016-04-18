<?php

require_once 'api/config.php';

$sql = '
    -- phpMyAdmin SQL Dump
    -- version 4.2.11
    -- http://www.phpmyadmin.net
    --
    -- Host: 127.0.0.1
    -- Generation Time: Apr 05, 2016 at 11:29 AM
    -- Server version: 5.6.21
    -- PHP Version: 5.6.3

    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
    SET time_zone = "+00:00";


    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
    /*!40101 SET NAMES utf8 */;

    --
    -- Database: `chatapp`
    --

    -- --------------------------------------------------------

    CREATE DATABASE IF NOT EXISTS '.$DB_NAME.';

    USE '.$DB_NAME.';

    --
    -- Table structure for table `friends`
    --

    CREATE TABLE IF NOT EXISTS `friends` (
    `friendship_id` int(11) NOT NULL,
      `user_id_1` int(11) NOT NULL,
      `user_id_2` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

    -- --------------------------------------------------------

    --
    -- Table structure for table `message`
    --

    CREATE TABLE IF NOT EXISTS `message` (
    `message_id` int(11) NOT NULL,
      `room_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `message_message` varchar(1024) NOT NULL,
      `message_raw_content` varchar(1024) NOT NULL,
      `message_timestamp` varchar(32) NOT NULL,
      `message_deleted` tinyint(1) NOT NULL DEFAULT \'0\'
    ) ENGINE=InnoDB AUTO_INCREMENT=444 DEFAULT CHARSET=latin1;

    -- --------------------------------------------------------

    --
    -- Table structure for table `room`
    --

    CREATE TABLE IF NOT EXISTS `room` (
    `room_id` int(11) NOT NULL,
      `room_admin_id` int(11) NOT NULL,
      `room_name` varchar(32) NOT NULL
    ) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=latin1;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user`
    --

    CREATE TABLE IF NOT EXISTS `user` (
    `user_id` int(11) NOT NULL,
      `user_email` varchar(32) NOT NULL,
      `user_first_name` varchar(32) NOT NULL,
      `user_last_name` varchar(32) NOT NULL,
      `user_password` varchar(256) NOT NULL,
      `user_profile_pic` varchar(256) NOT NULL
    ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

    -- --------------------------------------------------------

    --
    -- Table structure for table `user_in_room`
    --

    CREATE TABLE IF NOT EXISTS `user_in_room` (
    `relationship_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `room_id` int(11) NOT NULL,
      `user_status` int(11) NOT NULL
    ) ENGINE=InnoDB AUTO_INCREMENT=361 DEFAULT CHARSET=latin1;

    --
    -- Indexes for dumped tables
    --

    --
    -- Indexes for table `friends`
    --
    ALTER TABLE `friends`
     ADD PRIMARY KEY (`friendship_id`), ADD KEY `user_id_1` (`user_id_1`), ADD KEY `user_id_2` (`user_id_2`);

    --
    -- Indexes for table `message`
    --
    ALTER TABLE `message`
     ADD PRIMARY KEY (`message_id`), ADD KEY `room_id` (`room_id`), ADD KEY `user_id` (`user_id`);

    --
    -- Indexes for table `room`
    --
    ALTER TABLE `room`
     ADD PRIMARY KEY (`room_id`);

    --
    -- Indexes for table `user`
    --
    ALTER TABLE `user`
     ADD PRIMARY KEY (`user_id`);

    --
    -- Indexes for table `user_in_room`
    --
    ALTER TABLE `user_in_room`
     ADD PRIMARY KEY (`relationship_id`), ADD KEY `user_id` (`user_id`), ADD KEY `room_id` (`room_id`);

    --
    -- AUTO_INCREMENT for dumped tables
    --

    --
    -- AUTO_INCREMENT for table `friends`
    --
    ALTER TABLE `friends`
    MODIFY `friendship_id` int(11) NOT NULL AUTO_INCREMENT;
    --
    -- AUTO_INCREMENT for table `message`
    --
    ALTER TABLE `message`
    MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=444;
    --
    -- AUTO_INCREMENT for table `room`
    --
    ALTER TABLE `room`
    MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=326;
    --
    -- AUTO_INCREMENT for table `user`
    --
    ALTER TABLE `user`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
    --
    -- AUTO_INCREMENT for table `user_in_room`
    --
    ALTER TABLE `user_in_room`
    MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=361;
    --
    -- Constraints for dumped tables
    --

    --
    -- Constraints for table `friends`
    --
    ALTER TABLE `friends`
    ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id_1`) REFERENCES `user` (`user_id`),
    ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_id_2`) REFERENCES `user` (`user_id`);

    --
    -- Constraints for table `message`
    --
    ALTER TABLE `message`
    ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);

    --
    -- Constraints for table `user_in_room`
    --
    ALTER TABLE `user_in_room`
    ADD CONSTRAINT `user_in_room_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    ADD CONSTRAINT `user_in_room_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);

    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

';

try {
    $dbh = new PDO('mysql:host=' . $HOST . ';', $USERNAME, $PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbh->exec($sql);
    
    echo '
            <div style="width:400px;position:absolute;left:50%;top:50%;transform: translate(-50%, -100%);padding:10px;border-radius:5px;border:2px black solid;">
                
                <h1 style="text-align:center">
                    Success!
                </h1>
                <hr>
                <p style="text-align:center">
                    Nice one! You\'ve successfully installed Yapper!
                </p>
                <p style="text-align:center">
                    <a href="index.php">Time to get started!</a>
                </p>
                
            </div>
        ';
    
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}