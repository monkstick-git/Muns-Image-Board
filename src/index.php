<?php
// Include the bootstrap file for necessary initializations
require_once './system/bootstrap.php';
require_once './system/Router.php';

// Initialize the router
$Router = new Router();
$Router->getRoute($_SERVER['REQUEST_URI']);

// Log the route being accessed
logger('Route: ' . $Router->Controller . '/' . $Router->Method);

// Check if the route is allowed
if ($Router->isAllowedRoute()) {
    // Sanitize the controller and method
    $ControllerName = "Controller" . ucfirst(strtolower($Router->Controller)); // Example: "User" -> "ControllerUser"
    $ControllerFileName = ucfirst(strtolower($Router->Controller)); // Example: "user" -> "User"
    $Method = strtolower($Router->Method);

    // Autoload the controller file if it exists
    $controllerPath = ROOT . '/Controllers/' . $ControllerFileName . '.php';

    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        if (class_exists($ControllerName)) {
            $Controller = new $ControllerName;

            // Check if the method exists in the controller
            if (method_exists($Controller, $Method)) {
                $Controller->$Method(); // Call the method dynamically
            } else {
                // Handle the case where the method does not exist
                logger("Method $Method not found in controller $ControllerName");
                show404();
            }
        } else {
            // Handle the case where the controller class does not exist
            logger("Controller class $ControllerName not found.");
            show404();
        }
    } else {
        // Handle the case where the controller file does not exist
        logger("Controller file $controllerPath not found.");
        show404();
    }
} else {
    // Route is not allowed, load 404 page
    logger("Route: " . $_SERVER['REQUEST_URI'] . " is not allowed.");
    show404();
}

// Function to handle showing the 404 page
function show404() {
    require_once ROOT . '/Controllers/404.php';
    $Controller = new Controller404();
    $Controller->Index(); // Call the default index method for 404
    exit();
}
