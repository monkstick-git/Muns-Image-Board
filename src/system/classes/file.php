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
  public $thummbnail;

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

  public function CreateImageThumbNail(){
    $src = $this->unblob($this->encoded);
    $image = imagecreatefromstring($src);
    $width = imagesx($image);
    $height = imagesy($image);
    $new_width = ($width / 6);
    $new_height = ($height / 6);
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    ob_start();
    imagejpeg($tmp_img);
    $image_string = ob_get_contents();
    ob_end_clean();
    return ($image_string);
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

    # CreateImageThumbNail
    $this->thumbnail = $this->CreateImageThumbNail();
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
        INSERT INTO `files-metadata` 
          (`filetype`, `created`, `modified`, `size`, `name`, `owner`)
        VALUES
          ( '$FileType', '$created', '$modified', '$FileSize', '$FileName', '$Owner');
      ");
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
        ");
      }

      # Insert Thumbnail
      $thumbnail = $this->mysql->safe($this->blob($this->thumbnail));
      $this->mysql->query("
        INSERT INTO `files-thumbnail` 
          (`file_id`, `thumbnail`)
        VALUES
          ('$FileID', '$thumbnail');");
    }
  }

  public function loadMinimal($id){
    $data = $this->mysql->query("SELECT filetype,created,modified,size,name,owner FROM `files-metadata` WHERE `id` = '$id'");
    $data = $data->fetch_assoc();
    #$this->content = ($this->unblob($data['content']));
    $this->filetype = $data['filetype'];
    $this->created = $data['created'];
    $this->modified = $data['modified'];
    $this->size = $data['size'];
    $this->FileName = $data['name'];
    $this->owner = $data['owner'];
  }

  public function get($id)
  {
    $data = $this->mysql->query("SELECT * FROM `files-metadata` WHERE `id` = '$id'");
    $data = $data->fetch_assoc();
    #$this->content = ($this->unblob($data['content']));
    $this->filetype = $data['filetype'];
    $this->created = $data['created'];
    $this->modified = $data['modified'];
    $this->size = $data['size'];
    $this->FileName = $data['name'];
    $this->owner = $data['owner'];

    # loop over chunks in DB
    $chunks = $this->mysql->query("SELECT * FROM `files-chunk` WHERE `file_id` = '$id'");
    $chunks = $chunks->fetch_all(MYSQLI_ASSOC);
    $chunks = array_map(function ($chunk) {
      return $chunk['chunk'];
    }, $chunks);
    $chunks = implode("", $chunks);
    $this->content = $this->unblob($chunks);

    # Get Thumbnail
    $thumbnail = $this->mysql->query("SELECT * FROM `files-thumbnail` WHERE `file_id` = '$id'");
    $thumbnail = $thumbnail->fetch_assoc();
    $this->thumbnail = $this->unblob($thumbnail['thumbnail']);
    #print_r($this->thummbnail);
  }

  public function delete()
  {
  }

  public function get_files_ids_by_owner($id, $FileType = "image", $Sorting = "`modified` DESC")
  {
    $id = $this->mysql->safe($id);
    $FileType = $this->mysql->safe($FileType);
    $data = $this->mysql->query("SELECT `id` FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $Sorting");
    $files = array();
    while ($row = $data->fetch_assoc()) {
      $files[] = $row;
    }

    return $files;

  }

  public function get_files_by_owner($id, $FileType = "image", $Sorting = "`modified` DESC")
  {
    $id = $this->mysql->safe($id);
    $FileType = $this->mysql->safe($FileType);
    $data = $this->mysql->query("SELECT * FROM `files-metadata` WHERE `owner` = '$id' AND `filetype` LIKE '%$FileType%' ORDER BY $Sorting");
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
