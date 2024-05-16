<?php
# This script will clean up any orphaned metadata by 
# checking if the file exists in the file driver
# and deleting the metadata if the file does not exist

require_once '../system/bootstrap-light.php';

global $system;
# Check if the script is being ran via the CLI
if ((php_sapi_name() == "cli") == false) {
  echo "This script is only available via the CLI";
  exit;
}

$files = new file();
$FileArray = $files->Find(null, null, "`id` DESC",100000);
$DeletedFiles = array();

foreach ($FileArray as $file) {
  $LoadedFile = new file();
  $LoadedFile->get($file['id']);

  # Check if the file exists in the file driver
    if (isset($LoadedFile->Content) == false || $LoadedFile->Content == "") {
        logger("❌ File not found in file driver: " . $file['id']);
        $DeletedFiles[] = $file['id'];
        $LoadedFile->delete();
    }
}

echo "Cleanup complete\n";
foreach ($DeletedFiles as $file) {
  echo "Deleted File: " . $file . "\n";
}
echo "Deleted Files: " . count($DeletedFiles) . "\n";
