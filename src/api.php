<?php include './system/bootstrap.php';
ob_clean();

# Api for uploading images
# This is a simple API that allows users to upload images to the site and get a link back to the image
# The API is protected by an API key that is passed in the request
# The API key is used to authenticate the user and ensure that only authenticated users can upload images
# Your API key can be found at /User/profile

# To authenticate and upload an image, you need to send a POST request to the API with the following parameters:
# 1. data - The image file to upload
# 2. api - The API key to authenticate the user

# Example using curl: curl https://YOUR.DOMAIN/api.php -F "data=@./somefile.jpg" -F "api=YOUR_API_KEY" 

if (isset($_FILES['data'])) {
  if (isset($_REQUEST['api'])) {
    $GLOBALS['User']->get_user_by_api($_REQUEST['api']);
    if ($GLOBALS['User']->id) {
      $file = new file();
      $file->loadObjectFromUpload($_FILES['data']);
      $fileType = explode("/", $file->FileType)[0];
      # File has served it's purpose now - delete it to free up resource
      unset($file);

      # Ensure only images are uploaded to the API ( for now )
      if ($fileType == "image") {
        $image = new image();
        $image->loadObjectFromUpload($_FILES['data']);
        $image->thumbnail = $image->CreateImageThumbNail();
        $image->save();
        $type = explode("/", $image->FileType)[1];

        ob_clean();
        ob_start();
        echo $settings['site_url'] . "images/raw/" . $image->FileHash . "." . $type . "?api=1";
        header("Content-Type: $image->FileType");
        ob_end_flush();
        die;
      } else {
        $exploded = explode(".", $_FILES['data']['name']);
        $count = count($exploded);
        $fileTypeGuess =  $exploded[$count - 1];
        $file = new file();
        $file->loadObjectFromUpload($_FILES['data']);
        $file->save();
        $type = $fileTypeGuess; #explode("/", $file->FileType)[1];
        ob_clean();
        ob_start();
        echo $settings['site_url'] . "Site/download/" . $file->FileHash . "." . $type . "?api=1";
        header("Content-Type: $file->FileType");
      }
    } else {
      # Throw Unauthorised
      header("HTTP/1.0 401 Unauthorized");
      die;
    }
  }
} else {
}
