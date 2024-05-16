<?php

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

  public $CSRF;

  private $Permissions;

  public function __construct()
  {
    $this->CSRF = new csrf();
    $this->Permissions = new permissions();
  }

  public function has_permission($Permission)
  {
    if ($this->id == null) {
      mlog("User ID is NULL, setting to 0 (Guest)");
      $this->id = 0; # Guest user
    }

    return $this->Permissions->get($Permission, $this->id);
  }

  public function set_permission($Permission)
  {
    return $this->Permissions->set($Permission, $this->id);
  }

  /**
   * Get all permissions for the user
   * @return array
   */
  public function getPermissions(){

    if ($this->id == null) {
      mlog("User ID is NULL, setting to 0 (Guest)");
      $this->id = 0; # Guest user
    }

    return $this->Permissions->getAll($this->id);

  }

  public function is_admin()
  {
    global $mysql_slaves;
    $query = $mysql_slaves->query("SELECT `role` FROM `users` WHERE `id` = '$this->id'");
    $query = $query[0];
    if ($query['role'] == "admin") {
      return true;
    } else {
      return false;
    }
  }

  public function get_user_by_api($api)
  {
    global $mysql_slaves;
    $id = $mysql_slaves->safe($api);
    $id = $mysql_slaves->query("SELECT * FROM `users` WHERE `api` = '$id'");
    $result = $id[0];
    $this->id = $result['id'];
    $this->name = $result['name'];
    $this->surname = $result['surname'];
    $this->email = $result['email'];
    $this->username = $result['username'];
    $this->password = $result['password'];
    $this->Datecreated = $result['created'];
    $this->bio = $result['bio'];
    $this->apiKey = $result['api'];
  }

  public function get_user_by_id($id)
  {
    if (isset($id)) {
      global $mysql_slaves;
      $id = $mysql_slaves->safe($id);
      $id = $mysql_slaves->query("SELECT * FROM `users` WHERE `id` = '$id'");
      $result = $id[0];
      $this->id = $result['id'];
      $this->name = $result['name'];
      $this->surname = $result['surname'];
      $this->email = $result['email'];
      $this->username = $result['username'];
      $this->password = $result['password'];
      $this->Datecreated = $result['created'];
      $this->bio = $result['bio'];
      $this->apiKey = $result['api'];
    } else {
      mlog("Trying to get ID of NULL");
      die;
    }
  }

  public function generate_api_key()
  {
    return md5(uniqid(rand(), true));
  }

  public function create_user()
  {
    global $mysql;
    $Username = $mysql->safe($this->username);
    $Password = password_hash($this->password, PASSWORD_DEFAULT);
    $apiKey = $mysql->safe($this->generate_api_key());
    $result = $mysql->insert("INSERT INTO `users` (`username`, `password`, `api`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) VALUES ('$Username', '$Password', '$apiKey', 'null', 'null', 'null', 'user', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");

    mlog("✅ User created with new ID of: $result");
    $this->id = $result;

    # Next, set the default permissions for the user (which is SYSTEM.CAN_LOGIN, SYSTEM.CAN_LOGOUT, FILE.READ_OWN, FILE.WRITE_OWN, FILE.DELETE_OWN)
    $this->set_permission("SYSTEM.CAN_LOGIN");
    $this->set_permission("SYSTEM.CAN_LOGOUT");
    $this->set_permission("FILE.READ_OWN");
    $this->set_permission("FILE.WRITE_OWN");
    $this->set_permission("FILE.DELETE_OWN");


    # If insert was successful, get the user id
    if ($result) {
      mlog("Registered new user $result ⚠️⚠️⚠️");
      $this->get_user_by_id($result);
      return true;
    } else {
      mlog("Error creating user: " . $mysql->error);
      return false;
    }
  }

  public function password_complexity_check($Password)
  {
    $Count = strlen($Password);
    $Upper = preg_match('/[A-Z]/', $Password);
    $Lower = preg_match('/[a-z]/', $Password);
    $Number = preg_match('/[0-9]/', $Password);
    if ($Count < 8 || !$Upper || !$Lower || !$Number) {
      return false;
    } else {
      return true;
    }
  }

  public function setPassword($Password)
  {
    global $mysql;
    if ($this->password_complexity_check($Password)) {
      $Password = password_hash($Password, PASSWORD_DEFAULT);
      $id = $mysql->safe($this->id);
      $result = $mysql->insert("UPDATE `users` SET `password` = '$Password' WHERE `id` = '$id'");
      if ($result) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function check_if_username_exists($Username)
  {
    global $mysql_slaves;

    $Username = $mysql_slaves->safe($Username);
    $Username = $mysql_slaves->query("SELECT * FROM `users` WHERE `username` = '$Username'", false);
    if (count($Username) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function get_user_by_username($Username)
  {
    global $mysql_slaves;
    $Username = $mysql_slaves->safe($Username);
    $Username = $mysql_slaves->query("SELECT * FROM `users` WHERE `username` = '$Username'");
    $result = $Username[0];
    $this->id = $result['id'];
    $this->name = $result['name'];
    $this->surname = $result['surname'];
    $this->email = $result['email'];
    $this->username = $result['username'];
    $this->password = $result['password'];
    $this->Datecreated = $result['created'];
    $this->bio = $result['bio'];
    $this->apiKey = $result['api'];
  }

  public function login()
  {
    $this->loggedIn = true;
  }

  public function logout()
  {
    $this->loggedIn = false;
    session_start();
    session_destroy();

    header('Location: /');
  }

  public function getSpaceUsed()
  {
    global $mysql_slaves;
    $id = $mysql_slaves->safe($this->id);
    $id = $mysql_slaves->query("SELECT SUM(`size`) AS `size` FROM `files-metadata` WHERE `owner` = '$id'");
    $result = $id[0];
    return $result['size'];
  }

  public function getImageCount()
  {
    global $mysql_slaves;
    $id = $mysql_slaves->safe($this->id);
    $id = $mysql_slaves->query("SELECT COUNT(`id`) AS `count` FROM `files-metadata` WHERE `owner` = '$id'");
    $result = $id[0];
    return $result['count'];
  }
}
