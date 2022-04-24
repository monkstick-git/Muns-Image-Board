<?php

class sql
{
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_name;
  public $mysql;

  public function __construct($db_host, $db_user, $db_pass, $db_name)
  {
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
  }

  public function connect()
  {
    $this->mysql = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
    $this->mysql->set_charset('utf8');
  }

  public function query($query)
  {
    $this->connect();
    try {
      logger($query);
      $result = ($this->mysql->query($query));
      return $result;
    } catch (Exception $e) {
      logger($e->getMessage());
      return false;
    }
  }

  public function insert_id(){
    return $this->mysql->insert_id; 
  }

  public function safe($str)
  {
    $this->connect();
    return $this->mysql->real_escape_string($str);
  }
}
