<?php
include 'bootstrap.php';
# This file creates the needed schemas for the application
# and inserts the default data.

# Create Users Table
$mysql->insert("CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$tmpUser = new user();
$api = $tmpUser->generate_api_key();
# Create default admin user
$mysql->insert("INSERT INTO `users` (`id`, `username`, `password`, `api`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) VALUES
(1, 'admin', '" . password_hash("admin", PASSWORD_DEFAULT) . "','$api', 'monkstick@gmail.com', 'Monk', 'Stick', 'admin', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");

# Create Files Metadata Table
$mysql->insert("CREATE TABLE IF NOT EXISTS `files-metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `name` varchar(255),
  `filetype` varchar(255),
  `size` int(11),
  `created` datetime,
  `modified` datetime,
  `owner` int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

# Create Files Chunk Table with foreign key to files-metadata
$mysql->insert("CREATE TABLE IF NOT EXISTS `files-chunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11),
  `chunk` longblob,
  `chunk_no` int(11),
  `created` datetime,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

# Create thumbnail Table
$mysql->insert("CREATE TABLE IF NOT EXISTS `files-thumbnail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11),
  `thumbnail` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

# Create the Updates Table
$mysql->insert("CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `description` text,
  `created` datetime NOT NULL DEFAULT NOW(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1", false);
