<?php
$Description = "Add a quota column to the users table to limit the amount of storage a user can use";
$Version = "1.0.4";

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
ALTER TABLE `users` ADD `quota` int(11) DEFAULT 100;
  ";

  Registry::get('Sql')->insert($update);

  $update = "
UPDATE `users` SET `quota` = 100 WHERE `quota` IS NULL;
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
