<?php
# This script will check the mysql file driver
# for orphaned files and delete them

require_once '../system/bootstrap-light.php';

global $system;
mlog("Executing maintenance script: " . basename(__FILE__));
# Check if the script is being ran via the CLI
if ((php_sapi_name() == "cli") == false) {
  echo "This script is only available via the CLI";
  exit;
}

global $mysql;
$data = $mysql->query("SELECT id,file_id FROM `files-chunk`");
$files = array();
foreach ($data as $file) {
  $files[$file['file_id']][] = $file['id'];
}

$DeletedFiles = array();
foreach ($files as $fileID => $chunks) {
  $fileClass = new file();
  if ($fileClass->get($fileID) == false) {
    mlog("❌ File not found in metadata: " . $fileID);
    $DeletedFiles[] = $fileID;
    mlog("Executing: DELETE FROM `files-chunk` WHERE `file_id` = '$fileID'");
    $mysql->insert("DELETE FROM `files-chunk` WHERE `file_id` = '$fileID'");
  }
}

#print_r($files);


echo "Cleanup complete\n";
foreach ($DeletedFiles as $file) {
  echo "Deleted File/Chunk ID: " . $file . "\n";
}
echo "Deleted Files: " . count($DeletedFiles) . "\n";
