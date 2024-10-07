<?php

class imageHelper extends helper
{
    public function loadImage($ArgumentList)
    {
        try {
            $id = $ArgumentList['id'];
            $file = new image();
            if ($file->get($id) == false) {
                return false;
            } else {
                return $file;
            }
        } catch (Exception $e) {
            return false;
        }

    }

    public function loadType($ArgumentList)
    {
        if ((isset($ArgumentList['type']))) {
            $type = $ArgumentList['type'];
        } else {
            $type = false;
        }

        return $type;
    }

    public function permissionCheck($userID, $Image)
    {
        // Permission check (always allow for now)
        return true;
    }

    public function displayDeniedImage()
    {
        header("Content-Type: Image/PNG");
        # set header to real filename. Use "attachment" to force download instead of view
        header("Content-Disposition: inline; filename=denied.png");

        # Load image from /assets/Images/denied.webp
        $content = file_get_contents(ROOT . '/assets/Images/denied.png');
        echo $content;
        die();
    }

    public function displayImage($Image)
    {
        header("Content-Type: $Image->FileType");
        # set header to real filename. Use "attachment" to force download instead of view
        header("Content-Disposition: inline; filename=\"$Image->FileName\"");

        echo $Image->Content;
    }

    public function displayThumbnail($Image)
    {
        logger("Displaying Thumbnail");
        // Thumbnail logic
        if (isset($Image->Thumbnail) && $Image->Thumbnail != "") {

        } else {
            logger("Creating Thumbnail");
            $Image->ThumbNail = $Image->CreateImageThumbNail();
            $Image->update();
            // Wait for the update to complete before continuing. This is a hacky way to ensure the thumbnail is created and commited before it is displayed
            //sleep(seconds: 1);
        }

        header("Content-Type: $Image->FileType");
        # set header to real filename. Use "attachment" to force download instead of view
        header("Content-Disposition: inline; filename=\"$Image->FileName\"");

        echo $Image->Thumbnail;
    }
}