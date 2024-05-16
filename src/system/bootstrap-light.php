<?php
# Bootstrap-light is a stripped down version of the bootstrap.php file
# It is used for scripts that do not require the site layout
# such as API endpoints and scripts that run in the background/cli

# Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");
$isCLI = (php_sapi_name() == "cli") ? true : false;

# Manually load the logging class before anything else
require_once ROOT . '/system/classes/log.php';
# Now ensure there is always a logger available
global $logger;
$logger = new log();

$logger->log("Bootstrap-light loading begin");

require_once ROOT . '/vendor/autoload.php';
$redis = new Predis\Client('tcp://redis:6379');

# Include Settings and core Functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

if ($isCLI == false) {
  # Get the clients real IP address. Check for headers set by a proxy etc
  $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

  # If server is in a docker container, set the IP to the docker container IP
  if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
  }

  # Check for Cloudflare headers
  if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
  }
}

# Make $settings accessible globally
global $settings;

# Include all .php files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach ($classes as $class) {
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

if ($isCLI == false) {
  $whitelisted_pages = array('/User/login', '/User/register', '/User/logout');
  $page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
  define('PAGE', $page);
}

if ($isCLI == false) {
  session_start();
  # Check if a session is already set
  if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $User = new user();
    $GLOBALS['User'] = $User;
    $_SESSION['User'] = $User;
  } else {
    $User = $_SESSION['User'];
    $GLOBALS['User'] = $User;
  }


  if ((isset($_SESSION['logged_in']) == true)) {
    if ($_SESSION['logged_in'] == true) {
      $User->get_user_by_id($_SESSION['user_id']);
    }
  }
}
