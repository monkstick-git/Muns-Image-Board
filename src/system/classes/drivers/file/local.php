<?php

/**
 * Local File Driver
 *
 * This class handles file operations for the local file system.
 * It provides methods to save, retrieve, and delete files locally,
 * and manages the directory structure based on the file's unique ID.
 */
class file_driver_local
{
    public $Driver;
    public $Encoded;

    /**
     * Destination directory for file storage.
     * 
     * @var string
     */
    public $Dest = "/var/www/default/htdocs/httpdocs/uploads/";

    /**
     * Constructor
     *
     * Initializes the local file driver.
     */
    public function __construct()
    {
    }

    /**
     * Creates a nested directory structure based on the file name hash.
     *
     * @param string $fileName The file name or hash used to generate directories.
     * @return string The full path to the created directory.
     */
    private function createDirectories($fileName)
    {
        $hash = $fileName;
        $DestinationDir = $this->Dest;

        // Create a directory structure based on the first three characters of the hash
        $DestinationDir .= $hash[0] . "/" . $hash[1] . "/" . $hash[2] . "/";
        if (!file_exists($DestinationDir)) {
            mkdir($DestinationDir, 0777, true);
        }

        return $DestinationDir;
    }

    /**
     * Saves the file content locally, compressed and stored in a nested directory.
     *
     * @param string $UniqueID The unique ID of the file.
     * @param string $content The file content to be saved.
     */
    public function set($UniqueID, $content)
    {
        mlog("📂 Received file to save: $UniqueID");
        $this->Encoded = Compress($content);

        $DestinationDir = $this->createDirectories($UniqueID);

        // Write the compressed file to disk
        $filePath = $DestinationDir . $UniqueID;
        $file = fopen($filePath, "w");
        fwrite($file, $this->Encoded);
        fclose($file);

        mlog("📂 File saved: $filePath");
    }

    /**
     * Retrieves the file content from the local storage.
     *
     * @param string $UniqueID The unique ID of the file.
     * @return string|bool The decompressed file content, or false if the file is not found.
     */
    public function get($UniqueID)
    {
        $DestinationDir = $this->Dest;
        $DestinationDir .= $UniqueID[0] . "/" . $UniqueID[1] . "/" . $UniqueID[2] . "/";
        $filePath = $DestinationDir . $UniqueID;

        mlog("📂 Trying to read the file: $UniqueID");

        if (!file_exists($filePath)) {
            mlog("File not found: $UniqueID");
            return false;
        }

        $file = fopen($filePath, "r");
        $content = fread($file, filesize($filePath));
        fclose($file);

        mlog("📂 File read and closed: $UniqueID");

        return Expand($content);
    }

    /**
     * Deletes the file from local storage.
     *
     * @param string $UniqueID The unique ID of the file.
     * @return bool True if the file was successfully deleted, false otherwise.
     */
    public function delete($UniqueID)
    {
        $DestinationDir = $this->Dest;
        $DestinationDir .= $UniqueID[0] . "/" . $UniqueID[1] . "/" . $UniqueID[2] . "/";
        $filePath = $DestinationDir . $UniqueID;

        // Check if the file exists before attempting to delete
        if (!file_exists($filePath)) {
            mlog("File not found to delete: $UniqueID");
            return true; // File doesn't exist, so technically it's "deleted"
        } else {
            $result = unlink($filePath);
            if ($result) {
                mlog("File deleted: $filePath");
            } else {
                mlog("Failed to delete file: $filePath");
            }
            return $result;
        }
    }
}
