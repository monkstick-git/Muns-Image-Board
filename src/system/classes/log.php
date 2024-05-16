<?php

class log
{
    private $Driver = "local";
    public $IP;
    private $RandomID;

    public function __construct($IP = null)
    {
        $this->IP = $IP;
        $this->RandomID = uniqid();
    }

    public function log($message, $severity = "info"){
        # Get the function or class that called this function
        // $trace = debug_backtrace();
        // $caller = $trace[1];
        // $message = $caller['class'] . "::" . $caller['function'] . "(): " . $message;
        $this->_logFromDriver($message, $severity, $this->IP);
    }

    private function _logFromDriver($message, $severity = "info", $IP = null)
    {
        # Load the required driver on demand, depending on what driver was used to save logs
        # Sanity check $this->Driver before trying to load the driver.

        if (isset($this->Driver) && file_exists(ROOT . '/system/classes/drivers/log/' . $this->Driver . ".php")) {
            require_once ROOT . '/system/classes/drivers/log/' . $this->Driver . ".php";
        } else {
            logger("Error Finding Driver: " . $this->Driver);
        }

        $logger = new ("log_driver_" . $this->Driver); # <--- This is awesome
        $logger->log($message, $severity, $IP, $this->RandomID);
    }
}