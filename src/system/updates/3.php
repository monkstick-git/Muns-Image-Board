<?php
$Description = "Update all file metadata to include a driver option";
$Version = "1.0.2";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = $mysql_slaves->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  ### Start of Updates ###
  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Add the 'driver' column to the files-metadata table
  $update = "
ALTER TABLE `files-metadata` ADD `driver` VARCHAR(255) NULL DEFAULT NULL AFTER `owner`;
  ";

  $mysql->insert($update);

  $update = "
UPDATE `files-metadata` SET `driver` = 'mysql' WHERE `driver` IS NULL;
";

  $mysql->insert($update);

  # Update the Updates Table
  ## Commenting out to test updates ###
  $update = "
 INSERT INTO updates (version, description, created) VALUES ('$Version','$Description', '" . date("Y-m-d H:i:s") . "');
 ";
  $mysql->insert($update);

  ### End of Updates ###
} else {
  echo "Update Already Applied<br>";
}
