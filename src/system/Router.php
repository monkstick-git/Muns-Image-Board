<?php

class Router
{
    public $Controller;
    public $Method;
    public $Query;
    public $Routes;
    public $RouteTranslations;

    public function __construct()
    {
        $this->Routes = json_decode(file_get_contents(ROOT . '/system/routes.json'), true);
        Registry::set('Routes', $this->Routes);
        $this->RouteTranslations = json_decode(file_get_contents(ROOT . '/system/routeTranslations.json'), true);
        // These Translations will be used so if /User/Files gets changed to say, /Files/User, the alias will still work.
        Registry::set('RouteTranslations', $this->RouteTranslations); // A WIP to map route aliases to actual routes. E.g: "MyFiles" => "User/Files"
    }

    public function getRoute($requestUri)
    {
        $requestUri = trim($requestUri, '/');

        // Default to Home/Index if no URI is provided
        if (empty($requestUri)) {
            $this->Controller = 'Home';
            $this->Method = 'Index';
            return;
        }

        // Check if this is an API request
        if ($this->isApiRequest($requestUri)) {
            $this->handleApiRoute($requestUri);
            return;
        }

        // Handle non-API routes (e.g., /User/login)
        $this->handleWebRoute($requestUri);
    }

    private function isApiRequest($requestUri)
    {
        return strpos($requestUri, 'api/') === 0;  // Checks if request starts with 'api/'
    }

    private function handleApiRoute($requestUri)
    {
        // Extract API version and endpoint from the URI
        $parts = explode('/', $requestUri);
        $version = ucfirst($parts[1]);  // e.g., v1 or v2
        $this->Controller = 'Api' . ucfirst($version);  // Dynamically load API version controller
        $this->Method = !empty($parts[2]) ? ucfirst($parts[2]) : 'Index';  // Method or Index
        $this->Query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        # Remove the Query from the Method
        $this->Method = explode('?', $this->Method)[0];
    }

    private function handleWebRoute($requestUri)
    {
        // Handle web routes (e.g., /User/login)
        $path = parse_url($requestUri, PHP_URL_PATH);
        $this->Query = parse_url($requestUri, PHP_URL_QUERY);

        $path = trim($path, '/');
        $parts = explode('/', $path);
        $this->Controller = !empty($parts[0]) ? ucfirst($parts[0]) : 'Home';  // Default to Home
        $this->Method = isset($parts[1]) ? ucfirst($parts[1]) : 'Index';  // Default to Index
    }

    public function isAllowedRoute()
    {
        $Destination = $this->Controller . '/' . $this->Method;
        if (isset($this->Routes[$Destination])) {
            return true;
        } else {
            echo "Route: $Destination is not allowed.";
            return false;
        }
    }
}
