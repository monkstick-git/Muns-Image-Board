<?php

class file_driver_local
{
    public $Driver;

    public $Encoded;

    # TODO: This should be read from the settings file 
    public $Dest = "/var/www/default/htdocs/httpdocs/uploads/";

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    private function createDirectories($fileName)
    {
        # For each character in the filename hash, create a directory
        $hash = $fileName;
        $DestinationDir = $this->Dest;

        # Create a directory for the first 3 characters of the hash
        $DestinationDir .= $hash[0] . "/" . $hash[1] . "/" . $hash[2] . "/";
        if (!file_exists($DestinationDir)) {
            mkdir($DestinationDir, 0777, true);
        }

        return $DestinationDir;
    }

    public function set($UniqueID, $content)
    {
        logger("📂 Recieved file to save: $UniqueID");
        $this->Encoded = Compress($content);
        # Chunk $content into 1024000 byte (1mb) $chunks
        $DestinationDir = $this->createDirectories($UniqueID);

        # Write the file to disk
        $file = fopen($DestinationDir . $UniqueID, "w");
        fwrite($file, $this->Encoded);
        fclose($file);

    }

    public function get($UniqueID)
    {
        $DestinationDir = $this->Dest;
        $DestinationDir .= $UniqueID[0] . "/" . $UniqueID[1] . "/" . $UniqueID[2] . "/";
        logger("📂 Trying to read the file: $UniqueID");
        if (!file_exists($DestinationDir . $UniqueID)) {
            logger("File not found: $UniqueID");
            return false;
        }
        $file = fopen($DestinationDir . $UniqueID, "r");
        logger("📂 File opened: $UniqueID");
        $content = fread($file, filesize($DestinationDir . $UniqueID));
        logger("📂 File read: $UniqueID");
        fclose($file);
        logger("📂 File closed: $UniqueID");
        return Expand($content);
    }

    public function delete($UniqueID)
    {
        $DestinationDir = $this->Dest;
        $DestinationDir .= $UniqueID[0] . "/" . $UniqueID[1] . "/" . $UniqueID[2] . "/";
        # test if the file exists first
        if (!file_exists($DestinationDir . $UniqueID)) {
            logger("File not found to delete: $UniqueID");
            return true;
        }else{
            return unlink($DestinationDir . $UniqueID);
        }
        
    }

}