<?php
# Execute ALL maintenance scripts

include '../system/bootstrap-light.php';
global $system;

# Execute all "clean_*.php" files in the util directory
$files = glob(ROOT . '/util/maint_*.php');
foreach ($files as $file) {
  require_once $file;
}
