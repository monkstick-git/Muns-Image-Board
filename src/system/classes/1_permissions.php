<?php

class permissions
{

    public $mysql_slaves;

    public function __construct()
    {
        global $mysql_slaves;
        $this->mysql_slaves = $mysql_slaves;
    }

    public function get(string $Permission, int $UserID)
    {

        # Do some checks on $Permission. It should be a string and look like this: TYPE.PERMISSION
        # Example: SYSTEM.CAN_LOGIN

        $Permission = explode(".", $Permission);
        $Type = $Permission[0];
        $Permission = $Permission[1];

        if ($UserID == null) {
            mlog("User ID is NULL, setting to 0 (Guest)");
            $UserID = 0; # Guest user
        }

        $query = $this->mysql_slaves->query("SELECT `permissions` FROM `permissions-system` WHERE `user_id` = '$UserID' AND JSON_EXTRACT(`permissions`, '$.$Type.$Permission') = 1", true, 30); # A small cache of 30 seconds to prevent too many queries

        if ($query) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Set a permission for the user
     * @param string $Permission Permission to set
     * @param int $id User ID to set the permission for
     * @param int $value (optional) Default is 1
     */
    public function set(string $Permission, int $id, $value = 1)
    {

        $hasPermissions = false; # Set the default value to false. If the user has permissions, this will be set to true.

        # Convert the permission to an array if it is not already
        # Split the Permission into type and permission
        $Permission = explode(".", $Permission);
        $Type = $Permission[0];
        $Permission = $Permission[1];
        $NewPermission = array();
        $NewPermission[$Type][$Permission] = 1;


        global $mysql_slaves;
        # Get all the current permissions as an array
        $CurrentPermissions = $this->getAll($id);
        $CurrentPermissions = json_decode($CurrentPermissions, true);
        mlog("Current Permissions is of type: " . gettype($CurrentPermissions));

        # Check if the user has any permissions (false)
        if ($CurrentPermissions == false) {
            mlog("User does not have any permissions - assuming false");
            $hasPermissions = false;
            $CurrentPermissions = array();
        } else {
            mlog("User has permissions: " . json_encode($CurrentPermissions));
            $hasPermissions = true;
        }

        # Add the new permissions to the array
        #$NewPermissions = array_merge($CurrentPermissions, $NewPermission);
        $NewPermissions = $CurrentPermissions;
        $NewPermissions[$Type][$Permission] = 1;

        # Convert the array to a json object
        $NewPermissions = json_encode($NewPermissions);
        mlog("Setting permissions for user $id to $NewPermissions");

        global $mysql;
        if ($hasPermissions) {
            # Update the permissions
            $query = $mysql->insert("UPDATE `permissions-system` SET `permissions` = '$NewPermissions' WHERE `user_id` = '$id'");
            if ($query) {
                return true;
            }
        } else {
            # Insert the permissions
            $query = $mysql->insert("INSERT INTO `permissions-system` (`id`, `user_id`, `permissions`) VALUES (NULL, '$id', '$NewPermissions')");
            if ($query) {
                return true;
            }
        }

        # If we get here, something went wrong
        mlog("Failed to set permissions for user $id");
        return false;

    }

    /**
     * Get all permissions for the user
     * @param int $id
     * @return array
     * @example $User->getPermissions($id);
     */
    public function getAll(int $id)
    {
        global $mysql_slaves;
        $this->mysql_slaves = $mysql_slaves;

        $query = $this->mysql_slaves->query("SELECT `permissions` FROM `permissions-system` WHERE `user_id` = '$id'");
        if ($query) {
            return ($query[0]['permissions']);
        } else {
            return false;
        }
    }

}