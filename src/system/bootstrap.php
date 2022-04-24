<?php
# Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");

# Include Settings and core Functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

# Include Classes
require_once ROOT . '/system/classes/renderer.php';
require_once ROOT . '/system/classes/sql.php';
require_once ROOT . '/system/classes/user.php';
require_once ROOT . '/system/classes/file.php';
global $mysql;
$mysql = new sql(
  $settings['databases']['default']['host'],
  $settings['databases']['default']['user'],
  $settings['databases']['default']['pass'],
  $settings['databases']['default']['name']
);

session_start();

$whitelisted_pages = array('/User/login', '/User/register', '/User/logout');
#$page = str_replace("/", "", $_SERVER['DOCUMENT_URI']);
$page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
define('PAGE', $page);

if ((isset($_SESSION['logged_in']) == true)) {
  if($_SESSION['logged_in'] == true){
    $User = new user();
    $User->get_user_by_id($_SESSION['user_id']);
    $GLOBALS['User'] = $User;
  }
}else{
  $User = new user();
  $GLOBALS['User'] = $User;
}

# Site Layout
$Buffer = "";
# Site Header gets rendered on construction.
$render = new render();


