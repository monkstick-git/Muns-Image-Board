<?php

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
  public $encoded;
  public $decoded;

  public function __construct()
  {
    global $mysql;
    $this->mysql = $mysql;
  }

  public function get_file_from_url($url)
  {
    # Download file from URL
    $file = file_get_contents($url);
    return $file;
  }

  # Returns base64 + compressed blob
  public function blob($data)
  {
    # Get size of $data
    $this->FileSize = strlen($data);
    # Compress the data
    $data = gzcompress($data);
    # Base64 encode the data (convert to binary)
    $data = base64_encode($data);
    # Return the data
    return $data;
  }

  # Returns uncompressed + decoded base64 blob
  public function unblob($data)
  {
    # Base64 decode the data
    $data = base64_decode($data);
    # Decomppress the data
    $data = gzuncompress($data);
    # Return the data
    return $data;
  }

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

  public function loadObjectFromUpload($UploadObject)
  {
    # Encode File
    $this->encoded = $this->blob(file_get_contents($UploadObject['tmp_name']));

    if (!$this->validateImage($this->encoded)) {
      return false;
    }

    # Set Owner
    $this->Owner = $GLOBALS['User']->id;
    # Set FileType
    $this->FileType = $UploadObject['type'];
    # Set FileName
    $this->FileName = $UploadObject['name'];
    # Set FileSize
    $this->FileSize = strlen(file_get_contents($UploadObject['tmp_name']));
    # Set Created Date
    $this->Created = date("Y-m-d H:i:s");
    # Set Modified Date
    $this->Modified = date("Y-m-d H:i:s");
  }

  public function save()
  {
    if ($this->encoded) {
      $content = $this->mysql->safe($this->encoded);
      $FileType = $this->mysql->safe($this->FileType);
      $created = $this->mysql->safe($this->Created);
      $modified = $this->mysql->safe(date("Y-m-d H:i:s"));
      $FileSize = $this->mysql->safe($this->FileSize);
      $FileName = $this->mysql->safe($this->FileName);
      $Owner = $this->mysql->safe($this->Owner);
      $this->mysql->query("
        INSERT INTO `files` 
          (`content`, `filetype`, `created`, `modified`, `size`, `name`, `owner`)
        VALUES
          ('$content', '$FileType', '$created', '$modified', '$FileSize', '$FileName', '$Owner');
      ");
    }
  }

  public function get($id)
  {
    $data = $this->mysql->query("SELECT * FROM `files` WHERE `id` = '$id'");
    $data = $data->fetch_assoc();
    $this->content = ($this->unblob($data['content']));
    $this->filetype = $data['filetype'];
    $this->created = $data['created'];
    $this->modified = $data['modified'];
    $this->size = $data['size'];
    $this->FileName = $data['name'];
    $this->owner = $data['owner'];
  }

  public function delete()
  {
  }

  public function get_files_ids_by_owner($id, $FileType = "image", $Sorting = "`modified` DESC")
  {
    $id = $this->mysql->safe($id);
    $FileType = $this->mysql->safe($FileType);
    $data = $this->mysql->query("SELECT `id` FROM `files` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $Sorting");
    $files = array();
    while ($row = $data->fetch_assoc()) {
      $files[] = $row;
    }

    return $files;

  }

  public function get_owner($id)
  {
  }
}
