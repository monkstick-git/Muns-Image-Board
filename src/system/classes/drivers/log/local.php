<?php

/**
 * Local Log Driver
 *
 * This class handles logging to local files. Logs are saved with a timestamp, severity level, IP address, and a unique identifier.
 */
class log_driver_local
{
    /**
     * Constructor
     *
     * Initializes the local log driver. Currently, no special initialization is required.
     */
    public function __construct()
    {
    }

    /**
     * Logs a message to a local file.
     *
     * @param string $message The log message.
     * @param string $severity The severity level of the log (default is "info").
     * @param string|null $IP The IP address associated with the log (default is null).
     * @param string $id A unique identifier for the log entry.
     */
    public function log($message, $severity = "info", $IP = null, $id)
    {
        $date = date("Y-m-d"); // Current date without time
        $formattedMessage = $this->formatLog($message, $severity, $IP, $id);
        $logFilePath = "/var/www/default/htdocs/httpdocs/logs/$date.log";

        try {
            $file = fopen($logFilePath, "a");
            fwrite($file, $formattedMessage . "\n");
            fclose($file);
        } catch (Exception $e) {
            logger("Error saving log: " . $e->getMessage());
        }
    }

    /**
     * Formats the log message before saving it.
     *
     * @param string $message The log message.
     * @param string $severity The severity level of the log.
     * @param string|null $IP The IP address associated with the log.
     * @param string $id A unique identifier for the log entry.
     * @return string The formatted log message.
     */
    private function formatLog($message, $severity, $IP, $id)
    {
        $Now = date("Y-m-d H:i:s"); // Current timestamp
        $Now .= "." . substr(microtime(), 2, 4); // Append microseconds

        // Ensure severity is uppercase and ends with a colon
        $severity = strtoupper(rtrim($severity, ":"));

        // Format the log message
        $formattedMessage = "[$Now] [$id] [$IP] [$severity]: $message";
        return $formattedMessage;
    }
}
