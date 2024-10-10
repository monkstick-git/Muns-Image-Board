<?php

class sql_slaves extends sql
{

  public $hosts =  array();

  public function __construct()
  {
  }

  /* Adds a host to the array of mysql slaves that are available
  * @param array $host
  */
  public function addHost($host)
  {
    $this->hosts[] = $host;
  }

  /* Returns a random host from the array of mysql slaves that are available
  * @return array $host
  */
  public function selectRandomHost()
  {
    $host = $this->hosts[array_rand($this->hosts)];
    return $host;
  }

  public function connect()
  {
    $RandomHost = $this->selectRandomHost();
    mlog("Slave Connecting to: " . $RandomHost['host'] . " - " . $RandomHost['name'] . " - " . $RandomHost['user'] . " - " . $RandomHost['pass']);
    #$this->mysql = new mysqli($this->selectRandomHost()['host'], $this->selectRandomHost()['user'], $this->selectRandomHost()['pass'], $this->selectRandomHost()['name']);
    $this->mysql = new mysqli($RandomHost['host'], $RandomHost['user'], $RandomHost['pass'], $RandomHost['name']);
    $this->mysql->set_charset('utf8');
  }

}
