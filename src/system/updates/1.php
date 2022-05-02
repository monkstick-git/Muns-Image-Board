<?php
$Description = "Initialise version 1.0.0";
$Version = "1.0.0";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = $mysql_slaves->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Update the Updates Table
  $update = "
    INSERT INTO updates (version, description, created) VALUES ('$Version','$Description', '" . date("Y-m-d H:i:s") . "');
  ";

  $mysql->insert($update);
} else {
  echo "Update Already Applied<br>";
}
