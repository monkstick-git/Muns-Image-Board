<?php
$Description = "Add Indexs to the database";
$Version = "1.0.6";

global $mysql;
global $mysql_slaves;

# Check if Update is already applied
$query = Registry::get('SqlSlaves')->query("SELECT * FROM `updates` WHERE `version` = '$Version'", false);
if (count($query) == 0) {

  ### Start of Updates ###
  echo "Updating Database to version $Version<br>";
  echo "Description: $Description<br>";

  # Add the index to the files-chunk table
  $update = "
    CREATE INDEX idx_file_id_chunk_no ON `files-chunk` (`file_id`, `chunk_no`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE UNIQUE INDEX idx_hash ON `files-metadata` (`hash`);
    CREATE INDEX idx_owner ON `files-metadata` (`owner`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_file_id ON `files-thumbnail` (`file_id`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_user_id ON `permissions-system` (`user_id`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_short_url ON `urls` (`short_url`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_created ON `updates` (`created`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE UNIQUE INDEX idx_username ON `users` (`username`);
    CREATE UNIQUE INDEX idx_email ON `users` (`email`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_id_hash ON `files-metadata` (`id`, `hash`);
  ";

  Registry::get('Sql')->insert($update);

  $update = "
    CREATE INDEX idx_owner ON `files-metadata` (`owner`);
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
