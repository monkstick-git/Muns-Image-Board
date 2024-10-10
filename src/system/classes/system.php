<?php

/**
 * The system class handles core system-level functionality, such as
 * authentication, caching, and redirection within the application.
 */
class system
{
    public $cache = array("null" => "null");

    /**
     * Constructor for the system class.
     *
     * Currently, no initialization is required.
     */
    public function __construct()
    {
    }

    /**
     * Redirects the user to a specified location.
     *
     * @param string $location The URL to redirect to.
     */
    public function redirect($location)
    {
        header("Location: $location");
        exit(); // Ensure script execution stops after the redirect
    }

    /**
     * Ensures the user is authenticated.
     *
     * If the user is not authenticated, they are redirected to the login page.
     */
    public function beAuthenticated()
    {
        # Check if username is set in the session
        $Username = $_SESSION['User']->username ?? false;

        if (!$Username) {
            $this->redirect('/User/login');
        }else{
            return true;
        }
    }

    /**
     * Ensures the user is an administrator.
     *
     * If the user is not authenticated, they are redirected to the login page.
     * If the user is not an admin, a 403 Forbidden header is returned.
     *
     * @return bool True if the user is an admin, false otherwise.
     */
    public function beAdmin()
    {
        if (!isset($_SESSION['User'])) {
            $this->redirect('/User/login');
        }

        if ($GLOBALS['User']->is_admin() == false) {
            header('HTTP/1.0 403 Forbidden');
            return false;
        }

        return true;
    }

    /**
     * Caches data in Redis with a specified time-to-live (TTL).
     *
     * @param string $input The key for the cache entry.
     * @param mixed $data The data to be cached.
     * @param int $ttl Time-to-live for the cache entry in seconds. Default is 60 seconds.
     */
    public function cache($input, $data, $ttl = 60)
    {
        $id = base64_encode($input);

        if (Registry::get('redis')->get($id) === null) {
            mlog("Adding to cache: $input");
            Registry::get('redis')->set($id, $data, 'EX', $ttl);
        }
    }

    /**
     * Retrieves cached data from Redis.
     *
     * @param string $query The key for the cache entry.
     * @return mixed The cached data, or null if the data is not found.
     */
    public function cache_get($query)
    {
        $id = base64_encode($query);

        if (($cached_Obj = Registry::get('redis')->get($id)) !== null) {
            mlog("Returning from cache: $id");
            return $cached_Obj;
        }

        return null;
    }

    /**
     * Checks if a specific entry exists in the local cache array.
     *
     * @param string $input The key to check in the local cache.
     * @return mixed The cached data if it exists, or false if not found.
     */
    public function checkCache($input)
    {
        $encodedInput = base64_encode($input);

        if (isset($this->cache[$encodedInput])) {
            return $this->cache[$encodedInput];
        }

        return false;
    }
}
