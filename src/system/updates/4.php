<?php
$Description = "Add the option to save files as public or private";
$Version = "1.0.3";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = Registry::get('SqlSlaves')->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  ### Start of Updates ###
  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Add the 'driver' column to the files-metadata table
  $update = "
ALTER TABLE `files-metadata` ADD `public` BOOLEAN NULL DEFAULT 0 AFTER `driver`;
  ";

  Registry::get('Sql')->insert($update);

  $update = "
UPDATE `files-metadata` SET `public` = 0 WHERE `public` IS NULL;
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
