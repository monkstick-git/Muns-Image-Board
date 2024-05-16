<?php

class log_driver_local {

    public function __construct()
    {

    }

    public function log($message, $severity = "info", $IP = null, $id)
    {
        # Get current date, no time
        $date = date("Y-m-d");
        $formattedMessage = $this->formatLog($message, $severity, $IP, $id);
        try {
            $file = fopen("/var/www/default/htdocs/httpdocs/logs/$date.log", "a");
            fwrite($file, $formattedMessage . "\n");
            fclose($file);
        } catch (Exception $e) {
            logger("Error saving log: " . $e->getMessage());
        }

    }

/**
 * Format the log message before saving it
 * @param $message
 */
    private function formatLog($message, $severity, $IP, $id)
    {
        $Now = date("Y-m-d H:i:s");
        # Include the microsecond
        $Now .= "." . substr(microtime(), 2, 4);
        # Ensure $severity is uppercase and ends with a colon
        $severity = rtrim($severity, ":");
        $severity = strtoupper($severity);
        $message = "[$Now] [$id] [$IP] [$severity]: $message";
        return "$message";
    }
}