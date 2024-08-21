<?php
# This script will check the local file driver
# for orphaned files and delete them
# An orphaned file is a file that exists in the file driver
# but does not have a corresponding metadata entry

require_once '../system/bootstrap-light.php';
global $system;

mlog("Executing maintenance script: " . basename(__FILE__));

# Check if the script is being ran via the CLI
if ((php_sapi_name() == "cli") == false) {
  echo "This script is only available via the CLI";
  exit;
}

$filesLocation = "/var/www/default/htdocs/httpdocs/uploads"; # TODO: Set the location of the files directory via settings

# Iterate recursively through the files directory
$files = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($filesLocation, RecursiveDirectoryIterator::SKIP_DOTS),
  RecursiveIteratorIterator::CHILD_FIRST
);

$DeletedFiles = array();
foreach ($files as $file) {
  # We're not interested in directories yet
  if ($file->isDir()) {
    continue;
  }
  
  # Get the filename
  $fileID = $file->getFilename();
  mlog("Checking file: " . $fileID);
  $fileClass = new file();
  if ($fileClass->get($fileID) == false) {
    mlog("❌ File not found in metadata: " . $fileID);
    $DeletedFiles[] = $fileID;
    unlink($file->getPathname());
  }
}

# Next, check for any empty directories and remove them
$files = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($filesLocation, RecursiveDirectoryIterator::SKIP_DOTS),
  RecursiveIteratorIterator::CHILD_FIRST
);
$DeletedDirs = array();
foreach ($files as $file) {
  if ($file->isDir() && count(scandir($file)) == 2) {
    mlog("Removing empty directory: " . $file->getPathname());
    $DeletedDirs[] = $file->getPathname();
    rmdir($file->getPathname());
  }
}
echo "Cleanup complete\n";
foreach ($DeletedFiles as $file) {
  echo "Deleted File: " . $file . "\n";
}
echo "Deleted Files: " . count($DeletedFiles) . "\n";

foreach ($DeletedDirs as $dir) {
  echo "Deleted Directory: " . $dir . "\n";
}
echo "Deleted Directories: " . count($DeletedDirs) . "\n";
