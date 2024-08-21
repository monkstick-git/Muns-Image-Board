<?php

/**
 * Bootstrap-Light
 *
 * A stripped-down version of the bootstrap file for scripts that do not require
 * the full site layout, such as API endpoints and background/CLI scripts.
 */

// Define Root Path
define('ROOT', "/var/www/default/htdocs/httpdocs/");

// Determine if the script is running in the CLI
$isCLI = (php_sapi_name() == "cli");

// Manually load the logging class before anything else
require_once ROOT . '/system/classes/log.php';

// Initialize global logger
global $logger;
$logger = new log($_SERVER['REMOTE_ADDR']);

/**
 * Logs a message with additional context such as the calling class and function.
 *
 * @param string $Message The message to log.
 * @param string $Severity The severity level of the log (e.g., info, error).
 */
function mlog($Message, $Severity = "info")
{
    $trace = debug_backtrace();
    $Class = $trace[1]['class'] ?? "Unknown";
    $Function = $trace[1]['function'] ?? "Unknown";
    $Info = "$Class::$Function";

    global $logger;
    $logger->log($Info . ": " . $Message, $Severity);
}

mlog("Bootstrap-Light loading begin");

// Load Composer's autoloader
require_once ROOT . '/vendor/autoload.php';

// Initialize Redis client
$redis = new Predis\Client('tcp://redis:6379');

// Include settings and core functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';

// Handle web-specific tasks if not running in CLI
if (!$isCLI) {
    // Get the client's real IP address, accounting for proxies and Cloudflare
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
    }

    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
}

// Make $settings accessible globally
global $settings;

// Include all PHP files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach ($classes as $class) {
    require_once $class;
}

// Initialize session cache if not set
if (!$isCLI && !isset($_SESSION['cache'])) {
    $_SESSION['cache'] = array();
}

// Initialize global MySQL connections
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

// Initialize the system object
global $system;
$system = new system();
logger("System Loaded");

// Handle web-specific tasks if not running in CLI
if (!$isCLI) {
    $whitelisted_pages = array('/User/login', '/User/register', '/User/logout');
    $page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
    define('PAGE', $page);

    // Start the session and manage user session data
    session_start();
    if (!isset($_SESSION['visited'])) {
        $_SESSION['visited'] = true;
        $User = new user();
        $GLOBALS['User'] = $User;
        $_SESSION['User'] = $User;
    } else {
        $User = $_SESSION['User'];
        $GLOBALS['User'] = $User;
    }

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $User->get_user_by_id($_SESSION['user_id']);
    }
}
