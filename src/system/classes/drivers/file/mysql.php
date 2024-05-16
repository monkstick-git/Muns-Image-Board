<?php

class file_driver_mysql
{

    public $mysql;
    public $mysql_slave;
    public $Driver;

    public $Encoded;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $mysql;
        $this->mysql = $mysql;
        global $mysql_slaves;
        $this->mysql_slave = $mysql_slaves;
    }

    public function set($UniqueID, $content)
    {
        $this->Encoded = Compress($content);
        # Chunk $content into 1024000 byte (1mb) $chunks
        $chunks = str_split($this->Encoded, 1024000);

        # Split $UniqueID on _
        $ID = explode("_", $UniqueID)[1];
        #$FileID = $UniqueID;
        # Loop through each chunk
        foreach ($chunks as $index => $chunk) {
            $created = $this->mysql->safe(date("Y-m-d H:i:s"));
            # Insert the chunk
            $this->mysql->insert("
            INSERT INTO `files-chunk` 
                (`file_id`, `chunk`, `chunk_no`, `created`)
            VALUES
                ('$ID', '$chunk', '$index', '$created');
        ");
        }
    }

    public function get($UniqueID)
    {

        $id = explode("_", $UniqueID)[1];
        # loop over chunks in DB
        $chunks = $this->mysql_slave->query("SELECT * FROM `files-chunk` WHERE `file_id` = '$id'", true);

        $chunks = array_map(function ($chunk) {
            return $chunk['chunk'];
        }, $chunks);

        return Expand(implode("", $chunks));
    }

    public function delete($UniqueID)
    {
        $id = explode("_", $UniqueID)[1];
        # Make sure ID is a valid number and is sanitized
        $id = $this->mysql->safe($id);
        $this->mysql->insert("DELETE FROM `files-chunk` WHERE `file_id` = '$id'");
    }

}