<?php
# Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");

# Include Settings and core Functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

# Include Classes
require_once ROOT . '/system/classes/sql.php';
require_once ROOT . '/system/classes/user.php';
require_once ROOT . '/system/classes/file.php';
require_once ROOT . '/system/classes/system.php';

global $mysql;
$mysql = new sql(
  $settings['databases']['default']['host'],
  $settings['databases']['default']['user'],
  $settings['databases']['default']['pass'],
  $settings['databases']['default']['name']
);

$User = new user();
$GLOBALS['User'] = $User;

global $system;
$system = new system();
