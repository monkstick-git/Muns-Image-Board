<?php
# Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");

# Include Settings and core Functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

# Include all .php files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach($classes as $class){
  require_once $class;
}

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
