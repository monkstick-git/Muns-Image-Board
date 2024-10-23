<?php
$Description = "Add new URLs table for storing the long and short URLs";
$Version = "1.0.5";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = Registry::get('SqlSlaves')->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  ### Start of Updates ###
  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Add the 'quota' column to the users table, default 100MB
  $update = "
    Create TABLE `urls` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `long_url` text NOT NULL,
      `short_url` varchar(255) NOT NULL,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `user_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      CONSTRAINT `urls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  ";

  Registry::get('Sql')->insert($update);
  
  # Update the Updates Table
  ## Commenting out to test updates ###
  $update = "
 INSERT INTO updates (version, description, created) VALUES ('$Version','$Description', '" . date("Y-m-d H:i:s") . "');
 ";
  Registry::get('Sql')->insert($update);

  ### End of Updates ###
} else {
  echo "Update Already Applied<br>";
}
