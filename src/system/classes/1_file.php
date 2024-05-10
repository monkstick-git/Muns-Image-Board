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
  public $PublicFile = false;
  public $FilePath;
  public $Owner;
  public $Mysql;
  public $Mysql_Slave;
  public $Encoded;
  public $Decoded;
  #public $thummbnail; # This shouldn't tecnically exist here as it's specific to images.
  public $FileHash;
  public $Driver;

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

    # First, we need to figure out if we're dealing with a file ID, or a file Hash
    if (strlen($id) == 32) { # If the ID is 32 characters long, it's a hash
      $id = $this->Mysql_Slave->safe($id);
      $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `hash` = '$id'", true);
    } else { # Otherwise, it's probably an ID. Confirm with a int check
      $id = (int) $id;
      $id = $this->Mysql_Slave->safe($id);
      $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `id` = '$id'", true);
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
  }

  public function set()
  {
    logger("✅ Saving file: " . $this->FileID);
    # If a driver hasn't been set, use the default in settings
    if ($this->Driver === null) {
      global $settings;
      $this->Driver = $settings['fileDriver'];
    }

    # Save the file MetaData First
    $this->Mysql->insert("
    INSERT INTO `files-metadata` 
      (`filetype`, `created`, `modified`, `size`, `name`, `owner`, `hash`, `driver`)
    VALUES
      ( '$this->FileType', '$this->Created', '$this->Modified', '$this->FileSize', '$this->FileName', '$this->Owner', '$this->FileHash', '$this->Driver' );
  ");

    # Once Metadate is saved, Save the blob to the driver
    $this->FileID = $this->Mysql->insert_id();
    #$this->Content = 
    $this->_saveFileToDriver();
  }

  private function _saveFileToDriver()
  {

    # Load the required driver on demand, depending on what driver was used to upload the file.
    # Sanity check $this->Driver before trying to load the driver.

    if (isset($this->Driver) && file_exists(ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php")) {
      require_once ROOT . '/system/classes/drivers/file/' . $this->Driver . ".php";
    } else {
      logger("Error Finding Driver: " . $this->Driver);
    }

    $file = new ("file_driver_" . $this->Driver); # <--- This is awesome
    return $file->set($this->FileID, $this->Content);
  }

  public function get($id)
  {
    $this->getFileMetadata($id);
    logger("❌ getting file: " . $this->FileID);
    $File = $this->_getFileFromDriver($this->FileID); # This should be a complete file object, not individual blobs, encrypted, compressed etc
    $this->Content = $File;
    return $File;
  }

  public function update()
  {
    logger("✅ Updating file: " . $this->FileID);
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
      $data = $this->Mysql_Slave->query("SELECT * FROM `files-metadata` WHERE `owner` = '$userID' AND `filetype` LIKE '%$FileType%' ORDER BY $fileSort $limitString", true, 5);
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
      logger("Error Finding Driver: " . $this->Driver);
    }

    $file = new ("file_driver_" . $this->Driver); # <--- This is awesome
    return $file->get($id);
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
    logger("Saving as type: " . $this->FileType);
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
