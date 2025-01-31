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
require_once ROOT . '/system/settings.php';
include_once ROOT . '/system/translation.php';
require_once ROOT . '/system/functions.php';
// Load the registry which will store all global variables
include_once ROOT . '/system/registry.php';
Registry::set('settings', $settings);
Registry::set('api', $API); // Set the API flag in the registry from index.php

# Include the Models
require_once ROOT . '/Models/User.php';
require_once ROOT . '/Models/Sql.php';
require_once ROOT . '/Models/SqlSlaves.php';
require_once ROOT . '/Models/File.php';
require_once ROOT . '/Models/Permissions.php';
require_once ROOT . '/Models/Image.php';
require_once ROOT . '/Models/Session.php';
require_once ROOT . '/Models/Short.php';

if (!Registry::get('api', false)) { // If the site is not being accessed via the API, load the javascript and css files
    // Include the necessary CSS and JS files
    $cssIncludes[] = "/assets/css/bootstrap.min.css"; 
    $cssIncludes[] = "/assets/css/normalize.css";
    $cssIncludes[] = "/assets/css/style.css?1";
    Registry::set('cssIncludes', $cssIncludes);

    $jsIncludes[] = "/assets/js/site.js";
    $jsIncludes[] = "/assets/js/bootstrap.bundle.min.js";
    Registry::set('jsIncludes', $jsIncludes);
}


// Get the client's real IP address, accounting for proxies and Cloudflare
// This sets the Registry variable 'ClientIP' to the client's real IP address
getClientRealIP();

// Manually load the logging class before anything else
require_once ROOT . '/system/classes/log.php';

// Initialize global logger
Registry::set('logger', new log($_SERVER['REMOTE_ADDR']));

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

    Registry::get('logger')->log($Info . ": " . $Message, $Severity);
}

mlog("Bootstrap loading begin");

// Load Composer's autoloader
require_once ROOT . '/vendor/autoload.php';

// Initialize Redis client
Registry::set('redis', new Predis\Client('tcp://redis:6379'));

// Include core controller
require_once ROOT . '/Controllers/Controller.php';

// Include all PHP files in the system/classes/ directory
$classes = glob(ROOT . '/system/classes/*.php');
foreach ($classes as $class) {
    if (Registry::get('api',exitOnError: false)) {
        if (strpos($class, 'renderer.php') !== false) { // Skip the renderer class as it is not needed for the api
            continue;
        }
    }
    require_once $class;
}

// Initialize session cache if not set
if (!isset($_SESSION['cache'])) {
    $_SESSION['cache'] = array();
}

// Initialize global MySQL connections
Registry::set('Sql', new sql(
    Registry::get('settings')['databases']['writer']['host'],
    Registry::get('settings')['databases']['writer']['user'],
    Registry::get('settings')['databases']['writer']['pass'],
    Registry::get('settings')['databases']['writer']['name']
));
Registry::get('Sql')->cache = Registry::get('settings')['cache'];

Registry::set('SqlSlaves', new sql_slaves());
Registry::get('SqlSlaves')->cache = Registry::get('settings')['cache'];

foreach (Registry::get('settings')['databases']['slaves'] as $slave) {
    $FormattedHost = array(
        "host" => $slave['host'],
        "user" => $slave['user'],
        "pass" => $slave['pass'],
        "name" => $slave['name']
    );
    Registry::get('SqlSlaves')->addHost($FormattedHost);
}

// Initialize the system object
Registry::set('system', new system());
logger("System Loaded");

$page = str_replace(".php", "", $_SERVER['DOCUMENT_URI']);
define('PAGE', $page);

# TODO: Move all the user login logic to a separate class

// Start the session and manage user session data
session_start();

# Check if there's any user data in the session
if (isset($_SESSION['User'])) {
    $User = $_SESSION['User'];
    mlog("User session loaded from session data");
} else {
    $User = new user();
    $_SESSION['User'] = $User;
    mlog("User session initialized for the first time");
}


// Check if the user is logged in and load user data
if (!empty($_SESSION['User']) && !empty($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == true) {
        $User->get_user_by_id($_SESSION['user_id']);
    }
}

Registry::set('User', $User);

# At this point, the registry should contain the following Classes and arrays:
# - settings (Array: from settings.php)
# - cssIncludes (Array: from bootstrap.php)
# - jsIncludes (Array: from bootstrap.php)
# - logger (Log Class)
# - redis (Predis\Client)
# - Sql (Sql Class)
# - SqlSlaves (SqlSlaves Class)
# - system (system Class)
# - User (User Class)
# - ClientIP (String: Client's real IP address)