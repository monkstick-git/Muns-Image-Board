<?php
/**
 * Registry Class - This class is used to store variables that are used across the system in the future.
 * This is to replace the global variables/GLOBALS that are used in the system.
 * 
 */
class Registry {
    private static $instances = [];

    // Static method to set a value in the registry
    public static function set($key, $value) {
        # Ensure that the key is not already set in the registry
        if (isset(self::$instances[$key])) {
            # Find what class is trying to set the key
            $trace = debug_backtrace();
            $Class = $trace[0]['class'] ?? "Unknown";
            $Function = $trace[0]['function'] ?? "Unknown";
            $Info = "$Class::$Function";
            throw new Exception("Key $key already exists in the registry. Attempted to set by $Info");
        }else{
            self::$instances[$key] = $value;
        }
    }

    // Static method to get a value from the registry
    public static function get($key, $exitOnError = true) {
        # Ensure that the key exists in the registry
        if (!isset(self::$instances[$key])) {
            if($exitOnError == true){
                throw new Exception("Key $key does not exist in the registry");
            }else{
                return false;
            }
        }else{
            return self::$instances[$key];
        }
    }

    public static function update($key, $value) {
        # Ensure that the key exists in the registry
        if (!isset(self::$instances[$key])) {
            throw new Exception("Key $key does not exist in the registry");
        }else{
            self::$instances[$key] = $value;
        }
    }

    public static function viewKeys() {
        return array_keys(self::$instances);
    }
}
