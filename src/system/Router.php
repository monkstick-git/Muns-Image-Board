<?php

# Router file to handle all application routes
# This file is included in the index.php file

class Router
{
    public $Controller;
    public $Method;
    public $Query;
    public $Routes; # Array of allowed routes from the routes.json file

    public function __construct()
    {
        $this->Routes = json_decode(file_get_contents(ROOT . '/system/routes.json'), true);
    }

    # Load the route from the request URI
    public function getRoute($requestUri)
    {
        // Trim slashes from the requested URL
        $requestUri = trim($requestUri, '/');

        // If no specific URI is given, default to Home/Index
        if (empty($requestUri)) {
            $this->Controller = 'Home';
            $this->Method = 'Index';
            return;
        }

        # Get the controller and method from the path (e.g., /User/login)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        # Get the query string if any (e.g., ?id=1&name=John)
        $this->Query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

        # Clean up the path (remove leading and trailing slashes)
        $path = trim($path, '/');

        # Split the path into controller and method
        $parts = explode('/', $path);
        $this->Controller = !empty($parts[0]) ? ucfirst($parts[0]) : 'Home';  // Default to Home if not provided
        $this->Method = isset($parts[1]) ? ucfirst($parts[1]) : 'Index';  // Default to Index if method not provided
    }

    public function isAllowedRoute(){
        $Destination = $this->Controller . '/' . $this->Method;
        if (isset($this->Routes[$Destination])) {
            return true;
        } else {
            return false;
        }
    }
}
