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

  public function __construct()
  {
  }

  public function get_user_by_id($id)
  {
    global $mysql;
    $id = $mysql->safe($id);
    $id = $mysql->query("SELECT * FROM `users` WHERE `id` = '$id'");
    $result = $id->fetch_assoc();
    $this->id = $result['id'];
    $this->name = $result['name'];
    $this->surname = $result['surname'];
    $this->email = $result['email'];
    $this->username = $result['username'];
    $this->password = $result['password'];
  }

  public function create_user()
  {
    global $mysql;
    $Username = $mysql->safe($this->username);
    $Password =  password_hash($this->password, PASSWORD_DEFAULT);
    $result = $mysql->query("INSERT INTO `users` (`username`, `password`, `email`, `name`, `surname`, `role`, `active`, `created`, `modified`) VALUES ('$Username', '$Password', 'null', 'null', 'null', 'user', 1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");

    # If insert was successful, get the user id
    if ($result) {
      $this->get_user_by_id($mysql->insert_id);
      return true;
    } else {
      logger("Error creating user: " . $mysql->error);
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

  public function check_if_username_exists($Username)
  {
    global $mysql;
    $Username = $mysql->safe($Username);
    $Username = $mysql->query("SELECT * FROM `users` WHERE `username` = '$Username'");
    if ($Username->num_rows > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function get_user_by_username($Username)
  {
    global $mysql;
    $Username = $mysql->safe($Username);
    $Username = $mysql->query("SELECT * FROM `users` WHERE `username` = '$Username'");
    $result = $Username->fetch_assoc();
    $this->id = $result['id'];
    $this->name = $result['name'];
    $this->surname = $result['surname'];
    $this->email = $result['email'];
    $this->username = $result['username'];
    $this->password = $result['password'];
  }

  public function login(){
    $this->loggedIn = true;
  }

  public function logout(){
    $this->loggedIn = false;
    session_start();
    session_destroy();
    
    header('Location: /');    
  }
}
