<?php

/**
 * The image class extends the file class to handle image-specific functionality,
 * such as creating and managing image thumbnails.
 */
class image extends file
{
    public $Thumbnail;

    /**
     * Constructor that initializes the parent file class.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Creates a thumbnail for the image.
     *
     * This function resizes the original image to a minimum width of 512 pixels,
     * maintaining the aspect ratio, and returns the thumbnail as a JPEG image string.
     *
     * @return string The JPEG image string of the thumbnail.
     */
    public function CreateImageThumbNail()
    {
        $src = $this->Content;

        // Convert HEIC images to JPEG (commented out until necessary)
        // $fileIsHeic = Maestroerror\HeicToJpg::isHeic($this->FilePath);
        // if ($fileIsHeic) {
        //     $src = Maestroerror\HeicToJpg::convert("$this->FilePath")->get();
        // }

        $image = imagecreatefromstring($src);

        // Define minimum dimensions
        $min_width = 512;
        $min_height = 512;

        // Get current dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Calculate aspect ratio and new dimensions
        $aspect_ratio = $width / $height;
        if ($min_width > $width) {
            $new_width = $min_width;
            $new_height = $new_width / $aspect_ratio;
        } else {
            $new_width = $min_width;
            $new_height = $new_width / $aspect_ratio;
        }

        // Create a new true color image
        $tmp_img = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Output the thumbnail image as a JPEG string
        ob_start();
        imagejpeg($tmp_img);
        $image_string = ob_get_contents();
        ob_end_clean();
        $this->Thumbnail = $image_string;
        return $image_string;
    }

    /**
     * Sets the image data in the database, including the thumbnail.
     *
     * This function inserts the image data into the database by calling the parent set() method,
     * and then generates and stores the thumbnail.
     */
    public function set()
    {
        parent::set();

        // Insert the thumbnail into the database
        mlog("Creating Thumbnail for Image: $this->FileName");
        $thumbnail = $this->Mysql->safe($this->CreateImageThumbNail());
        $this->Mysql->insert("
            INSERT INTO `files-thumbnail` 
              (`file_id`, `thumbnail`)
            VALUES
              ('$this->FileID', '$thumbnail');
        ");
    }

    /**
     * Retrieves the image data and its thumbnail from the database.
     *
     * This function retrieves the image data by calling the parent get() method,
     * and then retrieves the thumbnail associated with the image.
     *
     * @param int $id The ID of the image.
     * @return bool Returns true if the image and thumbnail are successfully retrieved, false otherwise.
     */
    public function get($id)
    {
        if (parent::get($id) == false) {
            // Return early if the parent class fails.
            // There's not going to be a thumbnail if the file doesn't exist
            return false;
        }

        // Get the thumbnail for the image
        mlog("Getting Thumbnail for Image: $this->FileID");
        $Data = $this->Mysql->query("
            SELECT `thumbnail` 
            FROM `files-thumbnail` 
            WHERE `file_id` = '$this->FileID';
        ", true);

        if ($Data) {
            $this->Thumbnail = $Data[0]['thumbnail'];
        } else {
            $this->Thumbnail = null;
        }

        return true;
    }

    /**
     * Updates the image data in the database, including the thumbnail.
     *
     * This function updates the image data by calling the parent update() method,
     * and then generates and stores the new thumbnail.
     */
    public function update()
    {
        parent::update();

        // Insert the updated thumbnail into the database
        $thumbnail = $this->Mysql->safe($this->CreateImageThumbNail());
        $this->Mysql->insert("
            INSERT INTO `files-thumbnail` 
              (`file_id`, `thumbnail`)
            VALUES
              ('$this->FileID', '$thumbnail');
        ");
    }
}
