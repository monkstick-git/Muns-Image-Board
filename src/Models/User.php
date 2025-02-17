<?php

/**
 * The user class represents a user in the system and handles
 * operations related to user management, such as authentication,
 * permission handling, and data retrieval.
 */
class user
{
    public $name;
    public $surname;
    public $email;
    public $username;
    public $id;
    public $password;
    public $loggedIn = false;
    public $Datecreated;
    public $bio;
    public $apiKey;
    public $role;
    public $quota;
    public $CSRF;
    public $totalSpaceUsed;
    private $Permissions;

    /**
     * Constructor initializes CSRF protection and permissions management.
     */
    public function __construct()
    {
        $this->CSRF = new csrf();
        $this->Permissions = new permissions();
    }

    /**
     * Checks if the user has a specific permission.
     *
     * @param string $Permission The permission to check.
     * @return bool True if the user has the permission, false otherwise.
     */
    public function has_permission($Permission)
    {
        if ($this->id == null) {
            mlog("User ID is NULL, setting to 0 (Guest)");
            $this->id = 0; // Guest user
        }

        return $this->Permissions->get($Permission, $this->id);
    }

    /**
     * Sets a specific permission for the user.
     *
     * @param string $Permission The permission to set.
     * @return bool True if the permission was set successfully, false otherwise.
     */
    public function set_permission($Permission)
    {
        return $this->Permissions->set($Permission, $this->id);
    }

    /**
     * Retrieves all permissions for the user.
     *
     * @return array An array of all permissions associated with the user.
     */
    public function getPermissions()
    {
        if ($this->id == null) {
            mlog("User ID is NULL, setting to 0 (Guest)");
            $this->id = 0; // Guest user
        }

        return $this->Permissions->getAll($this->id);
    }

    public function getAllPermissions($UserID = null)  {
        if ($UserID == null) {
            mlog("User ID is NULL, setting to self");
            $UserID = $this->id ?? 0; // Guest user if self is also null
        }
        error_log("Getting all permissions for user ID: $UserID");
        $Permissions = $this->Permissions->getAll(id: $UserID);
        $PermissionsEncoded = json_decode($Permissions, true);
        return $PermissionsEncoded;
    }

    /**
     * Checks if the user is an administrator.
     *
     * @return bool True if the user is an admin, false otherwise.
     */
    public function is_admin()
    {
        $id = Registry::get('SqlSlaves')->safe($this->id);
        $query = Registry::get('SqlSlaves')->query("SELECT `role` FROM `users` WHERE `id` = '$id' LIMIT 1");
        $query = $query[0] ?? false;
        $Role = $query['role'] ?? false;

        if (!$Role) {
            return false;
        } else {
            if ($Role == "admin") {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Retrieves user information by API key.
     *
     * @param string $api The API key associated with the user.
     */
    public function get_user_by_api($api)
    {

        $id = Registry::get('SqlSlaves')->safe($api);
        $id = Registry::get('SqlSlaves')->query("SELECT * FROM `users` WHERE `api` = '$id' LIMIT 1");
        $result = $id[0] ?? false;
        if (!$result) {
            return false;
        }
        $this->populate_user_data($result);
    }

    /**
     * Retrieves user information by user ID.
     *
     * @param int $id The user ID.
     */
    public function get_user_by_id($id)
    {
        if (isset($id)) {
            $id = Registry::get('SqlSlaves')->safe($id);
            $id = Registry::get('SqlSlaves')->query("SELECT * FROM `users` WHERE `id` = '$id'");
            $result = $id[0];
            $this->populate_user_data($result);
        } else {
            mlog("Trying to get ID of NULL");
            die;
        }
    }

    public function getAllUsers()
    {
        $Users = Registry::get('SqlSlaves')->query("SELECT * FROM `users`");
        $UserArray = array();
        foreach ($Users as $User) {
            $UserArray[] = new user();
            $UserArray[count($UserArray) - 1]->populate_user_data($User);
        }

        return $UserArray;

    }

    /**
     * Populates the user object with data from the database.
     *
     * @param array $data The data array from the database.
     */
    public function populate_user_data($data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->surname = $data['surname'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->Datecreated = $data['created'] ?? null;
        $this->bio = $data['bio'] ?? null;
        $this->apiKey = $data['api'] ?? null;
        $this->quota = $data['quota'] ?? null;
        $this->totalSpaceUsed = $this->getSpaceUsed() ?? null;
    }

    /**
     * Generates a new API key.
     *
     * @return string The generated API key.
     */
    public function generate_api_key()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Creates a new user in the database.
     *
     * @return bool True if the user was created successfully, false otherwise.
     */
    public function create_user()
    {
        $Username = Registry::get('Sql')->safe($this->username);
        $Password = password_hash($this->password, PASSWORD_DEFAULT);
        $apiKey = Registry::get('Sql')->safe($this->generate_api_key());
        $result = Registry::get('Sql')->insert("INSERT INTO `users` (`username`, `password`, `api`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) VALUES ('$Username', '$Password', '$apiKey', 'null', 'null', 'null', 'user', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");

        mlog("✅ User created with new ID of: $result");
        $this->id = $result;

        // Set default permissions for the new user
        $this->set_permission("SYSTEM.CAN_LOGIN");
        $this->set_permission("SYSTEM.CAN_LOGOUT");
        $this->set_permission("FILE.READ_OWN");
        $this->set_permission("FILE.WRITE_OWN");
        $this->set_permission("FILE.DELETE_OWN");

        if ($result) {
            mlog("Registered new user $result ⚠️⚠️⚠️");
            $this->get_user_by_id($result);
            return true;
        } else {
            mlog("Error creating user: " . $this->username);
            return false;
        }
    }

    /**
     * Checks if a password meets the complexity requirements.
     *
     * @param string $Password The password to check.
     * @return bool True if the password meets the requirements, false otherwise.
     */
    public function password_complexity_check($Password)
    {
        $Count = strlen($Password);
        $Upper = preg_match('/[A-Z]/', $Password);
        $Lower = preg_match('/[a-z]/', $Password);
        $Number = preg_match('/[0-9]/', $Password);
        return !($Count < 8 || !$Upper || !$Lower || !$Number);
    }

    /**
     * Sets a new password for the user.
     *
     * @param string $Password The new password.
     * @return bool True if the password was set successfully, false otherwise.
     */
    public function setPassword($Password)
    {
        if ($this->password_complexity_check($Password)) {
            $Password = password_hash($Password, PASSWORD_DEFAULT);
            $id = Registry::get('Sql')->safe($this->id);
            $result = Registry::get('Sql')->insert("UPDATE `users` SET `password` = '$Password' WHERE `id` = '$id'");
            return (bool) $result;
        }
        return false;
    }

    /**
     * Checks if a username already exists in the database.
     *
     * @param string $Username The username to check.
     * @return bool True if the username exists, false otherwise.
     */
    public function check_if_username_exists($Username)
    {

        $Username = Registry::get('SqlSlaves')->safe($Username);
        $Username = Registry::get('SqlSlaves')->query("SELECT * FROM `users` WHERE `username` = '$Username'", false);
        return count($Username) > 0;
    }

    /**
     * Retrieves user information by username.
     *
     * @param string $Username The username.
     */
    public function get_user_by_username($Username)
    {

        $Username = Registry::get('SqlSlaves')->safe($Username);
        $Username = Registry::get('SqlSlaves')->query("SELECT * FROM `users` WHERE `username` = '$Username'");
        $result = $Username[0];
        $this->populate_user_data($result);
    }

    /**
     * Updates the user information in the database.
     *
     * @return bool True if the update was successful, false otherwise.
     */
    public function update()
    {
        $id = Registry::get('Sql')->safe($this->id);
        $name = Registry::get('Sql')->safe($this->name);
        $surname = Registry::get('Sql')->safe($this->surname);
        $bio = Registry::get('Sql')->safe($this->bio);

        $query = "UPDATE `users` SET `name` = '$name', `surname` = '$surname', `bio` = '$bio' WHERE `id` = '$id'";
        return (bool) Registry::get('Sql')->insert($query);
    }

    /**
     * Logs the user in by setting the loggedIn property to true.
     */
    public function login()
    {
        logger("▶▶▶ User logged in: " . $this->username);
        $this->loggedIn = true;
    }

    /**
     * Logs the user out by destroying the session and redirecting to the homepage.
     */
    public function logout()
    {
        $this->loggedIn = false;
        session_start();
        session_unset();
        unset($_SESSION);
        session_destroy();
        return;
    }

    /**
     * Retrieves the total space used by the user's files.
     *
     * @return int The total space used in bytes.
     */
    public function getSpaceUsed(): int
    {

        $id = Registry::get('SqlSlaves')->safe($this->id);
        $userSpaceUsed = Registry::get('SqlSlaves')->query("SELECT SUM(`size`) AS `size` FROM `files-metadata` WHERE `owner` = '$id'");
        $result = $userSpaceUsed[0];
        $SpaceUsed = isset($result['size']) ? $result['size'] : null;
        # Check to ensure SpaceUsed is not null before returning
        if ($SpaceUsed == null) {
            mlog("SpaceUsed is null for userID: $id, returning 0");
            $this->totalSpaceUsed = 0;
            return 0;
        }

        $this->totalSpaceUsed = $SpaceUsed;
        return $SpaceUsed;
    }

    /**
     * Retrieves the total number of images owned by the user.
     *
     * @return int The number of images owned by the user.
     */
    public function getImageCount()
    {

        $id = Registry::get('SqlSlaves')->safe($this->id);
        $id = Registry::get('SqlSlaves')->query("SELECT COUNT(`id`) AS `count` FROM `files-metadata` WHERE `owner` = '$id'");
        $result = $id[0];
        return $result['count'];
    }
}
