<?php
include 'bootstrap.php';

/**
 * This script sets up the necessary database schemas for the application
 * and inserts default data, including an admin user.
 */

// Create Users Table
$mysql->insert("
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api` varchar(255) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `bio` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");

// Create Files Metadata Table
$mysql->insert("
CREATE TABLE IF NOT EXISTS `files-metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `name` varchar(255),
  `filetype` varchar(255),
  `size` int(11),
  `created` datetime,
  `modified` datetime,
  `owner` int(11),
  `driver` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");

// Create Permissions Table
$mysql->insert("
CREATE TABLE IF NOT EXISTS `permissions-system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permissions` json,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");

// Generate API key for the default admin user
$tmpUser = new user();
$api = $tmpUser->generate_api_key();

// Create Default Admin User
$mysql->insert("
INSERT INTO `users` 
(`id`, `username`, `password`, `api`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) 
VALUES 
(1, 'admin', '" . password_hash("admin", PASSWORD_DEFAULT) . "', '$api', 'monkstick@gmail.com', 'Monk', 'Stick', 'admin', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')
");

// Create Files Chunk Table with foreign key to files-metadata
$mysql->insert("
CREATE TABLE IF NOT EXISTS `files-chunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11),
  `chunk` longblob,
  `chunk_no` int(11),
  `created` datetime,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`file_id`) REFERENCES `files-metadata`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");

// Create Thumbnails Table
$mysql->insert("
CREATE TABLE IF NOT EXISTS `files-thumbnail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11),
  `thumbnail` longblob,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`file_id`) REFERENCES `files-metadata`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");

// Create the Updates Table
$mysql->insert("
CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `description` text,
  `created` datetime NOT NULL DEFAULT NOW(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");
