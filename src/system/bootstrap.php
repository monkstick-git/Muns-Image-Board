<?php
# Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");
require_once ROOT . '/vendor/autoload.php';
$redis = new Predis\Client('tcp://redis:6379');


# Include Settings and core Functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

# Make $settings accessible globally
global $settings;

#require_once ROOT . '/vendor/predis/autoload.php';

# Include all .php files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach ($classes as $class) {
  logger("Loaded: " . $class);
  require_once $class;
}
if (false == isset($_SESSION['cache'])) {
  $_SESSION['cache'] = array();
}
global $mysql;
$mysql = new sql(
  $settings['databases']['writer']['host'],
  $settings['databases']['writer']['user'],
  $settings['databases']['writer']['pass'],
  $settings['databases']['writer']['name']
);
$mysql->cache = $settings['cache'];

global $mysql_slaves;
$mysql_slaves = new sql_slaves();
$mysql_slaves->cache = $settings['cache'];

foreach ($settings['databases']['slaves'] as $slave) {

  $FormattedHost = array(
    "host" => $slave['host'],
    "user" => $slave['user'],
    "pass" => $slave['pass'],
    "name" => $slave['name']
  );
  $mysql_slaves->addHost($FormattedHost);
}

global $system;
$system = new system();
logger("System Loaded");
// session_set_cookie_params([
//   'path' => '/',
//   'domain' => $_SERVER['HTTP_HOST'],
//   'httponly' => true,
//   'samesite' => 'lax'
// ]);
session_start();

$whitelisted_pages = array('/User/login', '/User/register', '/User/logout');
#$page = str_replace("/", "", $_SERVER['DOCUMENT_URI']);
$page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
define('PAGE', $page);

if ((isset($_SESSION['logged_in']) == true)) {
  if ($_SESSION['logged_in'] == true) {
    $User = new user();
    $User->get_user_by_id($_SESSION['user_id']);
    $GLOBALS['User'] = $User;
    $_SESSION['User'] = $User;
  }
} else {
  $User = new user();
  $GLOBALS['User'] = $User;
}

if (isset($_REQUEST['api'])) {

} else {
  # Site Layout
  $Buffer = "";
  # Site Header gets rendered on construction.
  $render = new render();
}



