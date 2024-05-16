<?php

/**
 * Creates a File Object.  This acts as a wrapper around the file data, and provides a way to interact with the file data.
 * This class is responsible for loading the correct driver for the file, and returning the file data in a usable format.
 */
class file
{
  public $FileID;
  public $FileName;
  public $FileSize;
  public $FileType;
  public $PublicFile; # 0 = Private, 1 = Public
  public $FilePath;
  public $Owner;
  public $Mysql;
  public $Mysql_Slave;
  public $Encoded;
  public $Decoded;
  #public $thummbnail; # This shouldn't tecnically exist here as it's specific to images.
  public $FileHash;
  public $Driver;
  public $UniqueID;

  /**
   * The raw blob of the file
   */
  public $Content;

  public $Created;
  public $Modified;

  /**
   * Constructor
   */
  public function __construct()
  {
    global $mysql;
    $this->Mysql = $mysql;
    global $mysql_slaves;
    $this->Mysql_Slave = $mysql_slaves;
  }

  public function getFileMetadata($id)
  {
    # As of now, I only want to accept an $id as a uniqueID. That means both the hash and the ID.
    if (!is_numeric($id) && strpos($id, "_") !== false) {
      # If the ID contains an underscore, it's a UniqueID. We can extract the ID from it.
      $imageID = explode("_", $id)[1];
      $imageID = $this->Mysql_Slave->safe($imageID);
      $hash = explode("_", $id)[0];
      $hash = $this->Mysql_Slave->safe($hash);
      mlog("Extracted ID: " . $imageID);
      mlog("Extracted Hash: " . $hash);
    } else {
      mlog("Invalid ID: " . $id);
      die("Invalid ID: " . $id);
    }
    $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `id` = '$imageID' AND `hash` = '$hash'", true);


    if ($data == false) {
      # No image found
      mlog("No file found with ID: " . $id);
      return false;
    }
    #$data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `id` = '$id'", true);
    $data = $data[0];
    $this->Created = $data['created'];
    $this->Modified = $data['modified'];
    $this->FileSize = $data['size'];
    $this->FileName = $data['name'];
    $this->Owner = $data['owner'];
    $this->FileID = $data['id'];
    $this->FileHash = $data['hash'];
    $this->FileType = $data['filetype'];
    $this->Driver = $data['driver'];
    $this->PublicFile = $data['public'];
    $this->UniqueID = $data['hash'] . "_" . $data['id'];
    return true;
  }

  public function delete($softDelete = false)
  {
    mlog("❌ Deleting file: " . $this->UniqueID);
    $id = $this->Mysql->safe($this->FileID);
    $this->Mysql->insert("DELETE FROM `files-metadata` WHERE `id` = '$id'");

    $this->_deleteFileFromDriver();
  }

  public function set()
  {
    mlog("✅ Saving file: " . $this->FileID);
    # If a driver hasn't been set, use the default in settings
    if ($this->Driver === null) {
      global $settings;
      $this->Driver = $settings['fileDriver'];
    }

    # Convert $this->PublicFile to an int
    // if($this->PublicFile == true){
    //   mlog("File is public");
    //   $this->PublicFile = 1;
    // }else{
    //   mlog("File is private");
    //   $this->PublicFile = 0;
    // }

    mlog("🔴The PublicFile is set to: " . $this->PublicFile);

    # Save the file MetaData First
    $this->Mysql->insert("
    INSERT INTO `files-metadata` 
      (`filetype`, `created`, `modified`, `size`, `name`, `owner`, `hash`, `driver`, `public`)
    VALUES
      ( '$this->FileType', '$this->Created', '$this->Modified', '$this->FileSize', '$this->FileName', '$this->Owner', '$this->FileHash', '$this->Driver', '$this->PublicFile' );
  ");

    # Once Metadate is saved, Save the blob to the driver
    mlog("File Metadata Saved, Getting the ID of the file...");
    $this->FileID = $this->Mysql->insert_id();
    mlog("Done! - File ID: " . $this->FileID);
    $this->UniqueID = $this->FileHash . "_" . $this->FileID;
    #$this->Content = 
    $this->_saveFileToDriver();
  }


  private function _deleteFileFromDriver()
  {
    # Load the required driver on demand, depending on what driver was used to upload the file.
    # Sanity check $this->Driver before trying to load the driver.

    if (isset($this->Driver) && file_exists(ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php")) {
      require_once ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php";
    } else {
      mlog("Error Finding Driver: " . $this->Driver);
    }

    $file = new ("file_driver_" . $this->Driver); # <--- This is awesome
    return $file->delete($this->UniqueID);
  }

  private function _saveFileToDriver()
  {

    # Load the required driver on demand, depending on what driver was used to upload the file.
    # Sanity check $this->Driver before trying to load the driver.

    if (isset($this->Driver) && file_exists(ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php")) {
      require_once ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php";
    } else {
      mlog("Error Finding Driver: " . $this->Driver);
    }

    $file = new ("file_driver_" . $this->Driver); # <--- This is awesome
    return $file->set($this->UniqueID, $this->Content);
  }

  public function get($id)
  {
    if ($this->getFileMetadata($id) == false) {
      mlog("Failed to get file metadata for: " . $id);
      return false;
    }
    mlog("❌ getting file: " . $this->UniqueID);
    # Check if the file is public or not
    // This logic needs reworking, disabled for now
    // if($this->PublicFile == 0){
    //   # If the file is private, check if the user is the owner
    //   if($this->Owner != $GLOBALS['User']->id){
    //     mlog("🔒 File is private and user is not the owner");
    //     return false;
    //   }
    // }else{
    //   mlog("🔓 File is public");
    // }
    $File = $this->_getFileFromDriver($this->UniqueID); # This should be a complete file object, not individual blobs, encrypted, compressed etc
    $this->Content = $File;
    mlog("✅ 2_file File loaded: " . $this->UniqueID);
    mlog("✅ File Size: " . strlen($File));

    return $this; # Return the file object
  }

  public function update()
  {
    mlog("✅ Updating file: " . $this->UniqueID);
    $this->Modified = date("Y-m-d H:i:s");
    # Save the file MetaData First
    $this->Mysql->insert("
    UPDATE `files-metadata` 
      SET `filetype` = '$this->FileType', `created` = '$this->Created', `modified` = '$this->Modified', `size` = '$this->FileSize', `name` = '$this->FileName', `owner` = '$this->Owner', `hash` = '$this->FileHash', `driver` = '$this->Driver'
      WHERE `id` = '$this->FileID';
    ");

    # We cant update files yet
    # TODO: Implement file updates
    #$this->_updateFileToDriver($this->FileID);
  }

  /**
   * Find files based on the owner, type, order and limit.
   * @param int $owner The owner of the file or NULL for all files of all users
   * @param string $type The type of file to find
   * @param string $order The order to return the files in
   * @param int $limit The amount of files to return.
   * @return array An array of file objects
   */
  public function Find($userID = null, $fileType = null, $fileSort = "`id` DESC", $limit = 1)
  {
    $id = $this->Mysql_Slave->safe($userID);
    $FileType = $this->Mysql_Slave->safe($fileType);
    $limitString = "LIMIT $limit";
    # If userID is set to null, return all users files
    if ($userID == null) {
      $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `filetype` LIKE '%$FileType%' ORDER BY $fileSort $limitString", true, 5);
    } else {
      $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $fileSort $limitString", true, 5);
    }


    $files = array();
    foreach ($data as $key => $value) {
      $files[] = $value;
    }


    return $files;
  }

  private function _getFileFromDriver($id)
  {
    # Load the required driver on demand, depending on what driver was used to upload the file.
    # Sanity check $this->Driver before trying to load the driver.

    if (isset($this->Driver) && file_exists(ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php")) {
      require_once ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php";
    } else {
      mlog("Error Finding Driver: " . $this->Driver);
    }

    $file = new ("file_driver_" . $this->Driver); # <--- This is awesome
    return $file->get($this->UniqueID);
  }


  /**
   * Count the total number of files a user owns
   * @param int $id The user ID
   * @param string $FileType The type of file to count
   * @return int The number of files
   */
  public function Count($userID, $FileType = "image")
  {

    $id = $this->Mysql_Slave->safe($userID);
    $FileType = $this->Mysql_Slave->safe($FileType);
    $data = $this->Mysql_Slave->query("SELECT COUNT(*) as count FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%'", true, 5);
    return $data[0]['count'];

  }

  public function setFromUpload($UploadObject)
  {
    # Set Owner
    $this->Owner = $GLOBALS['User']->id;
    # Set FileType
    $this->FileType = $this->getObjectType($UploadObject['tmp_name']);
    mlog("Saving as type: " . $this->FileType);
    # Set FileName
    $this->FileName = $UploadObject['name'];
    # Set FileSize
    $this->FileSize = strlen(file_get_contents($UploadObject['tmp_name']));
    # Set Created Date
    $this->Created = date("Y-m-d H:i:s");
    # Set Modified Date
    $this->Modified = date("Y-m-d H:i:s");
    # Set file hash
    $this->FileHash = md5(file_get_contents($UploadObject['tmp_name']));
    # Set FilePath
    $this->FilePath = $UploadObject['tmp_name'];

    $this->Content = file_get_contents($UploadObject['tmp_name']);
    #$this->UniqueID = $this->FileHash . "_" . $this->FileID;
  }

  public function getObjectType($path)
  {
    $mime = mime_content_type($path);
    # Convert certain filetypes to better names
    $conversionTable = array(
      "image/x-ms-bmp" => "image/bmp",
      "image/x-windows-bmp" => "image/bmp",
      "image/jpeg" => "image/jpg",
      "image/pjpeg" => "image/jpg",
      "image/png" => "image/png",
      "image/gif" => "image/gif",
      "image/tiff" => "image/tiff",
      "image/x-tiff" => "image/tiff",
      "image/x-targa" => "image/tga",
      "image/x-tga" => "image/tga",
      "image/x-bmp" => "image/bmp"
    );
    if (array_key_exists($mime, $conversionTable)) {
      $mime = $conversionTable[$mime];
    } else {
      //$exploded = explode(".", $path);
      //$count = count($exploded);
      //$fileTypeGuess =  $exploded[$count - 1];
      //$mime = $fileTypeGuess;
      $mime = $mime;
    }

    # Return the mime type of the file
    return $mime;
  }

}
