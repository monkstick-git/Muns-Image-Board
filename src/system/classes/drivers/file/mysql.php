<?php

/**
 * MySQL File Driver
 *
 * This class handles file storage using a MySQL database. It provides methods to save,
 * retrieve, and delete files, where the file content is stored in chunks within the database.
 */
class file_driver_mysql
{
    public $mysql;
    public $mysql_slave;
    public $Driver;
    public $Encoded;

    /**
     * Constructor initializes the MySQL connections.
     */
    public function __construct()
    {
        global $mysql;
        $this->mysql = $mysql;
        global $mysql_slaves;
        $this->mysql_slave = $mysql_slaves;
    }

    /**
     * Saves the file content to the MySQL database in chunks.
     *
     * @param string $UniqueID The unique ID of the file.
     * @param string $content The file content to be saved.
     */
    public function set($UniqueID, $content)
    {
        $this->Encoded = Compress($content);

        // Split the encoded content into 1MB chunks
        $chunks = str_split($this->Encoded, 1024000);

        // Extract the file ID from the UniqueID
        $ID = explode("_", $UniqueID)[1];

        // Loop through each chunk and save it to the database
        foreach ($chunks as $index => $chunk) {
            $created = $this->mysql->safe(date("Y-m-d H:i:s"));
            $this->mysql->insert("
                INSERT INTO `files-chunk` 
                (`file_id`, `chunk`, `chunk_no`, `created`)
                VALUES
                ('$ID', '$chunk', '$index', '$created');
            ");
        }
    }

    /**
     * Retrieves the file content from the MySQL database.
     *
     * @param string $UniqueID The unique ID of the file.
     * @return string The decompressed file content.
     */
    public function get($UniqueID)
    {
        // Extract the file ID from the UniqueID
        $id = explode("_", $UniqueID)[1];

        // Retrieve all chunks associated with the file ID
        $chunks = $this->mysql_slave->query("SELECT * FROM `files-chunk` WHERE `file_id` = '$id'", true);

        // Combine all chunks into a single string
        $chunks = array_map(function ($chunk) {
            return $chunk['chunk'];
        }, $chunks);

        // Decompress and return the file content
            return Expand(implode("", $chunks)); // Decompress and unencode the content
        
    }

    /**
     * Deletes the file from the MySQL database.
     *
     * @param string $UniqueID The unique ID of the file.
     */
    public function delete($UniqueID)
    {
        // Extract the file ID from the UniqueID
        $id = explode("_", $UniqueID)[1];

        // Ensure the ID is valid and sanitized
        $id = $this->mysql->safe($id);

        // Delete all chunks associated with the file ID
        $this->mysql->insert("DELETE FROM `files-chunk` WHERE `file_id` = '$id'");
    }
}
