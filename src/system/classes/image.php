<?php

class image extends file
{

  public $Thumbnail;

  public function __construct()
  {
    parent::__construct();
  }


  public function CreateImageThumbNail()
  {
    $src = $this->Content;
    // $fileIsHeic = Maestroerror\HeicToJpg::isHeic($this->FilePath);
    // if ($fileIsHeic) {
    //   $src = Maestroerror\HeicToJpg::convert("$this->FilePath")->get();
    // }


    $image = imagecreatefromstring($src);
    # Minimum Width and Height
    $min_width = 512;
    $min_height = 512;
    # Get current dimensions
    $width = imagesx($image);
    $height = imagesy($image);
    # Calculate aspect ratio
    $aspect_ratio = $width / $height;
    # Calculate new dimensions
    if ($min_width > $width) {
      $new_width = $min_width;
      $new_height = $new_width / $aspect_ratio;
    } else {
      $new_width = $min_width;
      $new_height = $new_width / $aspect_ratio;
    }

    #$new_width = ($width / 6);
    #$new_height = ($height / 6);
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    ob_start();
    imagejpeg($tmp_img);
    $image_string = ob_get_contents();
    ob_end_clean();
    return ($image_string);
  }
  # CreateImageThumbNail
  #$this->thumbnail = $this->CreateImageThumbNail();


  public function set()
  {
    parent::set();
    # Insert Thumbnail
    mlog("Creating Thumbnail for Image: $this->FileName");
    $thumbnail = $this->Mysql->safe($this->CreateImageThumbNail());
    $this->Mysql->insert("
        INSERT INTO `files-thumbnail` 
          (`file_id`, `thumbnail`)
        VALUES
          ('$this->FileID', '$thumbnail');");
  }

  public function get($id)
  {
    if(parent::get($id) == false) {
      # Return early if the parent class fails.
      # There's not going to be a thumbnail if the file doesn't exist
      return false;
    }

    # This bit I'm still unsure about
    mlog("Getting Thumbnail for Image: $this->FileID");
    # Get the thumbnail as this is an image
    $Data = $this->Mysql->query("
      SELECT `thumbnail` 
      FROM `files-thumbnail` 
      WHERE `file_id` = '$this->FileID';", true);

    if ($Data) {
      $this->Thumbnail = $Data[0]['thumbnail'];
    } else {
      $this->Thumbnail = null;
    }

    return true;
  }

  # This function only exists to add a thumbnail to an image for now
  public function update()
  {
    parent::update();
    # Insert Thumbnail
    $thumbnail = $this->Mysql->safe($this->CreateImageThumbNail());
    $this->Mysql->insert("
        INSERT INTO `files-thumbnail` 
          (`file_id`, `thumbnail`)
        VALUES
          ('$this->FileID', '$thumbnail');");
  }

}


