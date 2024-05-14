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

    public function set(int $id, $content, $Hash)
    {

        $this->Encoded = Compress($content);
        # Chunk $content into 1024000 byte (1mb) $chunks
        $DestinationDir = $this->createDirectories($Hash);

        # Write the file to disk
        $file = fopen($DestinationDir . $Hash, "w");
        fwrite($file, $this->Encoded);
        fclose($file);

    }

    public function get($id, $hash)
    {
        $DestinationDir = $this->Dest;
        $DestinationDir .= $hash[0] . "/" . $hash[1] . "/" . $hash[2] . "/";
        $file = fopen($DestinationDir . $hash, "r");
        $content = fread($file, filesize($DestinationDir . $hash));
        fclose($file);
        return Expand($content);
    }

}