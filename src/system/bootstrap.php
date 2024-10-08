<?php

/**
 * Bootstrap File
 *
 * This file initializes the application environment, sets configuration options,
 * loads essential classes, and handles session management.
 */

// Check if the site is being accessed via the api and then exit early



// Set PHP memory limits
ini_set('memory_limit', '1024M'); # Set to larger value if larger uploads are expected
ini_set('post_max_size', '1024M'); # Set to larger value if larger uploads are expected

// Define the root path of the application
define('ROOT', "/var/www/default/htdocs/httpdocs/");
include_once ROOT . '/system/translation.php';

$cssIncludes = array();
$jsIncludes = array();

// Include the necessary CSS and JS files
$cssIncludes[] = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css";
$cssIncludes[] = "https://unpkg.com/filepond@4.31.4/dist/filepond.min.css";
$cssIncludes[] = "https://vjs.zencdn.net/7.24.1/video-js.min.css";
$cssIncludes[] = "/assets/css/normalize.css";
$cssIncludes[] = "/assets/css/style.css";

$jsIncludes[] = "/assets/js/jquery-3.7.1.min.js";
$jsIncludes[] = "/assets/js/site.js";
$jsIncludes[] = "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js";
$jsIncludes[] = "https://unpkg.com/filepond@4.31.4/dist/filepond.min.js";
$jsIncludes[] = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js";
$jsIncludes[] = "https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js";
$jsIncludes[] = "https://vjs.zencdn.net/7.24.1/video.min.js";

// Get the client's real IP address, accounting for proxies and Cloudflare
$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

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
    //return 0;
    $trace = debug_backtrace();
    $Class = $trace[1]['class'] ?? "Unknown";
    $Function = $trace[1]['function'] ?? "Unknown";
    $Info = "$Class::$Function";

    global $logger;
    $logger->log($Info . ": " . $Message, $Severity);
}

mlog("Bootstrap-Full loading begin");

// Load Composer's autoloader
require_once ROOT . '/vendor/autoload.php';

// Initialize Redis client
$redis = new Predis\Client('tcp://redis:6379');

// Include settings and core functions
require_once ROOT . '/system/settings.php';
require_once ROOT . '/system/functions.php';
require_once ROOT . '/system/viewSnippets.php';

// Include core controller
require_once ROOT . '/Controllers/Controller.php';

// Make $settings accessible globally
global $settings;

// Include all PHP files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach ($classes as $class) {
    require_once $class;
}

// Initialize session cache if not set
if (!isset($_SESSION['cache'])) {
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

$page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
define('PAGE', $page);

# TODO: Move all the user login logic to a separate class

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

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $User->get_user_by_id($_SESSION['user_id']);
}

// Check if the 'render' flag is set in the request, determining whether to render the site layout
if (isset($_REQUEST['r']) && $_REQUEST['r'] == "0") {
    // Do nothing, rendering is disabled
} else {
    if (isset($_REQUEST['api'])) {
        $render = false;
    } else {
        // Site Layout
        $Buffer = "";
        #$render = new render();
    }
}