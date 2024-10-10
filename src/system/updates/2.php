<?php
$Description = "Update all users where the api key is empty";
$Version = "1.0.1";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = Registry::get('SqlSlaves')->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {
  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";
  $update = "
SELECT * FROM munboard.users where api = ' ';
";
  $Users = array();

  $Users = Registry::get('Sql')->query($update, false);
  $TempUser = new user();

  foreach ($Users as $User) {
    $Username = $User['username'];
    echo "Updating $Username<br>";
    $apiKey = $mysql->safe($TempUser->generate_api_key());
    $id = $User['id'];
    $api = $apiKey;
    $update = "
    UPDATE munboard.users SET api = '$api' WHERE id = '$id';
    ";
    Registry::get('Sql')->insert($update); # Remember to add FALSE to the end of the query to prevent the query from being cached
  }

  # Update the Updates Table
  $update = "
INSERT INTO updates (version, description, created) VALUES ('$Version','$Description', '" . date("Y-m-d H:i:s") . "');
";

  Registry::get('Sql')->insert($update);
}else{
  echo "Update Already Applied<br>";
}
