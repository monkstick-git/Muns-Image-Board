<?php
# This script will check the thumbnails table
# for orphaned files and delete them

require_once '../system/bootstrap-light.php';

global $system;
# Check if the script is being ran via the CLI
if ((php_sapi_name() == "cli") == false) {
  echo "This script is only available via the CLI";
  exit;
}

global $mysql;
$data = $mysql->query("SELECT id,file_id FROM `files-thumbnail`");
$files = array();
foreach ($data as $file) {
  $files[$file['file_id']][] = $file['id'];
}

$DeletedFiles = array();
foreach ($files as $fileID => $chunks) {
  $fileClass = new file();
  if ($fileClass->get($fileID) == false) {
    logger("❌ File not found in metadata: " . $fileID);
    $DeletedFiles[] = $fileID;
    logger("Executing: DELETE FROM `files-thumbnail` WHERE `file_id` = '$fileID'");
    $mysql->insert("DELETE FROM `files-thumbnail` WHERE `file_id` = '$fileID'");
  }
}

#print_r($files);


echo "Cleanup complete\n";
foreach ($DeletedFiles as $file) {
  echo "Deleted File/Chunk ID: " . $file . "\n";
}
echo "Deleted Files: " . count($DeletedFiles) . "\n";
