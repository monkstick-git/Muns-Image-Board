<?php

/**
 * Creates a File Object
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
  public $mysql;
  public $mysql_slave;
  public $encoded;
  public $decoded;
  public $thummbnail;
  public $FileHash;

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

  /**
   * Download a file from a URL.
   * This is commonly used to load via an upload
   *
   * @param string $url
   * @return binary
   */
  public function get_file_from_url($url)
  {
    $file = file_get_contents($url);
    return $file;
  }

  /**
   * Convert string / binary to compressed base64
   *
   * @param string $data
   * @return string
   */
  public function blob($data)
  {
    # Get size of $data
    $this->FileSize = strlen($data);
    # Compress the data
    $data = gzcompress($data, 1);
    # Base64 encode the data (convert to binary)
    $data = base64_encode($data);
    # Return the data
    return $data;
  }

  /**
   * base64 decode and uncompress data
   *
   * @param string $data
   * @return string
   */
  public function unblob($data)
  {
    # Base64 decode the data
    $data = base64_decode($data);
    # Decomppress the data
    $data = gzuncompress($data);
    # Return the data
    return $data;
  }

  /**
   * Test if loaded data is a valid image
   *
   * @return boolean
   */
  public function validateImage()
  {
    $content = $this->unblob($this->encoded);
    $image = imagecreatefromstring($content);
    if ($image) {
      return true;
    } else {
      logger("Error validating image: " . $this->mysql->error);
      return false;
    }
  }

  /**
   * Get file extension from a path
   *
   * @param string $path
   * @return string $mime
   */
  public function getObjectType($path)
  {
    $mime = mime_content_type($path);
    # Convert certain filetypes to better names
    $conversionTable = array(
      "image/x-ms-bmp" => "image/bmp",
      "image/x-windows-bmp" => "image/bmp",
      "image/jpeg" => "image/jpg",
      "image/pjpeg" => "image/jpg"
    );
    if (array_key_exists($mime, $conversionTable)) {
      $mime = $conversionTable[$mime];
    }

    # Return the mime type of the file
    return $mime;
  }

  /**
   * Load file from a path
   * @param mixed $UploadObject
   * @return void
   */
  public function loadObjectFromUpload($UploadObject)
  {
    # Set Owner
    $this->Owner = $GLOBALS['User']->id;
    # Set FileType
    $this->FileType = $this->getObjectType($UploadObject['tmp_name']);
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
  }


  /**
   * Save file to database
   */
  public function save()
  {
    $this->encoded = $this->blob(file_get_contents($this->FilePath));
    if ($this->encoded) {
      $content = $this->mysql->safe($this->encoded);
      $FileType = $this->mysql->safe($this->FileType);
      $created = $this->mysql->safe($this->Created);
      $modified = $this->mysql->safe(date("Y-m-d H:i:s"));
      $FileSize = $this->mysql->safe($this->FileSize);
      $FileName = $this->mysql->safe($this->FileName);
      $Owner = $this->mysql->safe($this->Owner);
      $FileHash = $this->mysql->safe($this->FileHash);
      $this->mysql->query("
        INSERT INTO `files-metadata` 
          (`filetype`, `created`, `modified`, `size`, `name`, `owner`, `hash`)
        VALUES
          ( '$FileType', '$created', '$modified', '$FileSize', '$FileName', '$Owner', '$FileHash' );
      ", false);
      # Chunk $content into 1024000 byte (1mb) $chunks
      $chunks = str_split($content, 1024000);
      # Get the last insert ID
      $FileID = $this->mysql->insert_id();
      $this->FileID = $FileID;
      # Loop through each chunk
      foreach ($chunks as $index => $chunk) {
        $created = $this->mysql->safe(date("Y-m-d H:i:s"));
        # Insert the chunk
        $this->mysql->query("
          INSERT INTO `files-chunk` 
            (`file_id`, `chunk`, `chunk_no`, `created`)
          VALUES
            ('$FileID', '$chunk', '$index', '$created');
        ", false);
      }
    }
  }

  /**
   * Load Minimal File Metadata from database
   * @param hash $hash
   */
  public function loadMinimal($hash)
  {
    $data = ($this->mysql_slave->query("SELECT filetype,created,modified,size,name,owner FROM `files-metadata` WHERE `hash` = '$hash'", true))[0];
    #$this->content = ($this->unblob($data['content']));
    $this->filetype = $data['filetype'];
    $this->created = $data['created'];
    $this->modified = $data['modified'];
    $this->size = $data['size'];
    $this->FileName = $data['name'];
    $this->owner = $data['owner'];
  }

  /**
   * Loads a file from the database
   *
   * @param int $id
   * @return void
   */
  public function get($id)
  {
    $data = $this->mysql_slave->query("SELECT * FROM `files-metadata` WHERE `id` = '$id'", true);
    $data = $data[0];
    $this->filetype = $data['filetype'];
    $this->created = $data['created'];
    $this->modified = $data['modified'];
    $this->size = $data['size'];
    $this->FileName = $data['name'];
    $this->owner = $data['owner'];
    $this->FileID = $id;
    $this->FileHash = $data['hash'];
    $this->FileType = $data['filetype'];
    
    # loop over chunks in DB
    $chunks = $this->mysql_slave->query("SELECT * FROM `files-chunk` WHERE `file_id` = '$id'", true);
    $chunks = ($chunks);
    $chunks = array_map(function ($chunk) {
      return $chunk['chunk'];
    }, $chunks);
    $chunks = implode("", $chunks);
    $this->content = $this->unblob($chunks);

    # Get Thumbnail
    $thumbnail = $this->mysql_slave->query("SELECT * FROM `files-thumbnail` WHERE `file_id` = '$id'", true);
    $thumbnail = $thumbnail[0];
    $this->thumbnail = $this->unblob($thumbnail['thumbnail']);
    #print_r($this->thummbnail);
  }

  /**
   * Loads a file from the database
   *
   * @param hash $hash
   * @return void
   */
  public function get_by_hash($hash)
  {
    $hash = $this->mysql_slave->safe($hash);
    $id = $this->mysql_slave->query("SELECT * FROM `files-metadata` WHERE `hash` = '$hash'", true);
    $fileID = ($id[0]['id']);
    $this->get($fileID);
  }

  /**
   * Delete a file from the database
   */
  public function delete()
  {
  }


  /**
   * Get a list of all file ID's a user owns
   * @param int $id
   * @param string $FileType
   * @param string $Sorting 'modified' DESC, 'created' ASC, 'size' DESC
   * @return array $files
   */
  public function get_files_ids_by_owner($id, $FileType = "image", $Sorting = "`modified` DESC")
  {
    $id = $this->mysql_slave->safe($id);
    $FileType = $this->mysql_slave->safe($FileType);
    $data = $this->mysql_slave->query("SELECT `id` FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $Sorting", true, 5);
    $files = array();
    foreach ($data as $key => $value) {
      $files[] =  $value;
    }

    return $files;
  }

  /**
   * Get a list of all files a user owns
   * @param int $id UserID
   * @param string $FileType
   * @param string $Sorting 'modified' DESC, 'created' ASC, 'size' DESC
   * @param int $limit default 100
   * @return array $files
   */
  public function get_files_by_owner($id, $FileType = "image", $Sorting = "`modified` DESC", $limit = 100)
  {
    $id = $this->mysql_slave->safe($id);
    $FileType = $this->mysql_slave->safe($FileType);
    $limitString = "LIMIT $limit";
    $data = $this->mysql_slave->query("SELECT * FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $Sorting $limitString", true, 5);

    $files = array();
    foreach ($data as $key => $value) {
      $files[] = $value;
    }


    return $files;
  }

  public function get_all_owners_files($FileType = "image", $Sorting = "`modified` DESC", $limit = 100)
  {
    $FileType = $this->mysql_slave->safe($FileType);
    $limitString = "LIMIT $limit";
    $data = $this->mysql_slave->query("SELECT * FROM `files-metadata` WHERE `filetype` LIKE '%$FileType%' ORDER BY $Sorting $limitString", true, 5);
    $files = array();
    foreach ($data as $key => $value) {
      $files[] =  $value;
    }

    return $files;
  }

  public function count_total_items($id, $FileType = "image")
  {
    if ($id === "all") {
      $query = "SELECT COUNT(*) FROM `files-metadata` WHERE `filetype` LIKE '%$FileType%'";
    } else {
      $query = "SELECT COUNT(*) FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%'";
    }
    $id = $this->mysql_slave->safe($id);
    $FileType = $this->mysql_slave->safe($FileType);
    $data = $this->mysql_slave->query($query, true, 5);
    return $data[0]['COUNT(*)'];
  }

  public function get_file_details()
  {
  }

  public function get_owner($id)
  {
  }
}
