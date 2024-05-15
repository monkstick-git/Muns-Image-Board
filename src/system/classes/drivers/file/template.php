<?php

class file_driver_DRIVERNAME
{

    public $Driver;
    public $Encoded;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    public function set(int $id, $content, $Hash)
    {

        $this->Encoded = Compress($content); # Run the file though Compress first
        # Write the file to somewhere
        # Write File;
    }

    public function get($id, $Hash)
    {
        $Data = ""; # Load the Data from somewhere
        return Expand($Data); # Return the data, making sure to run it through Expand
    }

    public function delete($id, $Hash)
    {
        # Delete the file
    }

}