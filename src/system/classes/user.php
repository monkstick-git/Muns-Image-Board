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

    /**
     * Checks if the user is an administrator.
     *
     * @return bool True if the user is an admin, false otherwise.
     */
    public function is_admin()
    {
        global $mysql_slaves;
        $query = $mysql_slaves->query("SELECT `role` FROM `users` WHERE `id` = '$this->id'");
        $query = $query[0];
        return ($query['role'] == "admin");
    }

    /**
     * Retrieves user information by API key.
     *
     * @param string $api The API key associated with the user.
     */
    public function get_user_by_api($api)
    {
        global $mysql_slaves;
        $id = $mysql_slaves->safe($api);
        $id = $mysql_slaves->query("SELECT * FROM `users` WHERE `api` = '$id'");
        $result = $id[0];
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
            global $mysql_slaves;
            $id = $mysql_slaves->safe($id);
            $id = $mysql_slaves->query("SELECT * FROM `users` WHERE `id` = '$id'");
            $result = $id[0];
            $this->populate_user_data($result);
        } else {
            mlog("Trying to get ID of NULL");
            die;
        }
    }

    public function getAllUsers(){
        global $mysql_slaves;
        $Users = $mysql_slaves->query("SELECT * FROM `users`");
        $UserArray = array();
        foreach($Users as $User){
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
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->surname = $data['surname'];
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->Datecreated = $data['created'];
        $this->bio = $data['bio'];
        $this->apiKey = $data['api'];
        $this->quota = $data['quota'];
        $this->totalSpaceUsed = $this->getSpaceUsed();
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
        global $mysql;
        $Username = $mysql->safe($this->username);
        $Password = password_hash($this->password, PASSWORD_DEFAULT);
        $apiKey = $mysql->safe($this->generate_api_key());
        $result = $mysql->insert("INSERT INTO `users` (`username`, `password`, `api`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) VALUES ('$Username', '$Password', '$apiKey', 'null', 'null', 'null', 'user', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");

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
        global $mysql;
        if ($this->password_complexity_check($Password)) {
            $Password = password_hash($Password, PASSWORD_DEFAULT);
            $id = $mysql->safe($this->id);
            $result = $mysql->insert("UPDATE `users` SET `password` = '$Password' WHERE `id` = '$id'");
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
        global $mysql_slaves;
        $Username = $mysql_slaves->safe($Username);
        $Username = $mysql_slaves->query("SELECT * FROM `users` WHERE `username` = '$Username'", false);
        return count($Username) > 0;
    }

    /**
     * Retrieves user information by username.
     *
     * @param string $Username The username.
     */
    public function get_user_by_username($Username)
    {
        global $mysql_slaves;
        $Username = $mysql_slaves->safe($Username);
        $Username = $mysql_slaves->query("SELECT * FROM `users` WHERE `username` = '$Username'");
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
        global $mysql;
        $id = $mysql->safe($this->id);
        $name = $mysql->safe($this->name);
        $surname = $mysql->safe($this->surname);
        $bio = $mysql->safe($this->bio);

        $query = "UPDATE `users` SET `name` = '$name', `surname` = '$surname', `bio` = '$bio' WHERE `id` = '$id'";
        return (bool) $mysql->insert($query);
    }

    /**
     * Logs the user in by setting the loggedIn property to true.
     */
    public function login()
    {
        $this->loggedIn = true;
    }

    /**
     * Logs the user out by destroying the session and redirecting to the homepage.
     */
    public function logout()
    {
        $this->loggedIn = false;
        session_start();
        session_destroy();
        header('Location: /');
    }

    /**
     * Retrieves the total space used by the user's files.
     *
     * @return int The total space used in bytes.
     */
    public function getSpaceUsed(): int
    {
        global $mysql_slaves;
        $id = $mysql_slaves->safe($this->id);
        $id = $mysql_slaves->query("SELECT SUM(`size`) AS `size` FROM `files-metadata` WHERE `owner` = '$id'");
        $result = $id[0];
        $SpaceUsed = $result['size'];
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
        global $mysql_slaves;
        $id = $mysql_slaves->safe($this->id);
        $id = $mysql_slaves->query("SELECT COUNT(`id`) AS `count` FROM `files-metadata` WHERE `owner` = '$id'");
        $result = $id[0];
        return $result['count'];
    }
}
