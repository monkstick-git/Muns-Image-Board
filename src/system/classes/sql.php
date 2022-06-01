<?php

class sql
{
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_name;
  public $mysql;
  public $cache = true;

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

  public function insert($query)
  {
    # Inserts should never be cached
    $return = false;
    $this->connect();
    try {
      $result = "";
      $this->mysql->query($query);
      if ($this->mysql->insert_id) {
        return $this->mysql->insert_id;
      } else {
        $return = true;
      }

      return $return;
    } catch (Exception $e) {
      logger($e->getMessage());
      return false;
    }
  }

  public function query($query, $cache = true, $ttl = 3600)
  {
    if($this->cache == false){
      $cache = false;
    }
    $return = array();
    #echo $query . "<br>";
    $this->connect();
    try {
      global $system;
      $result = "";
      if ($cache == true) {
        $cachedData = $system->cache_get($query);
        if ($cachedData) {
          $return = unserialize($cachedData);
        } else {
          logger("Query: $query");
          $result = ($this->mysql->query($query));
          $return = array();
          if ($this->mysql->insert_id) {
          } else {
            while ($row = $result->fetch_assoc()) {
              $return[] = $row;
            }
            if ($cache == true) {
              $system->cache($query, serialize($return), $ttl);
            }
          }
        }
        #logger(print_r($result, true));
      } else {
        $result = ($this->mysql->query($query));
        if ($this->mysql->insert_id) {
        } else {
          $return = array();
          while ($row = $result->fetch_assoc()) {
            $return[] = $row;
          }
          if ($cache == true) {
            $system->cache($query, serialize($return));
          }
        }
      }

      return $return;
    } catch (Exception $e) {
      logger($e->getMessage());
      return false;
    }
  }

  public function insert_id()
  {
    return $this->mysql->insert_id;
  }

  public function safe($str)
  {
    $this->connect();
    return $this->mysql->real_escape_string($str);
  }
}
