<?php
$Description = "Initialise version 1.0.0";
$Version = "1.0.0";

# Check if Update is already applied
$query = Registry::get('SqlSlaves')->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Update the Updates Table
  $update = "
    INSERT INTO updates (version, description, created) VALUES ('$Version','$Description', '" . date("Y-m-d H:i:s") . "');
  ";

  Registry::get('Sql')->insert($update);
} else {
  echo "Update Already Applied<br>";
}
