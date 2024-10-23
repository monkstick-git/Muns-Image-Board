<?php
include 'bootstrap.php';

# Check if the CLI is being used
if (php_sapi_name() != "cli") {
    echo "This script can only be run from the command line";
    exit;
}

$Updates = glob(ROOT . '/system/updates/*.php');
# Sort $Updates by name - Preserving natural numbers
natsort($Updates);

foreach ($Updates as $update) {
    require_once $update;
}
