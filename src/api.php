<?php include './system/bootstrap.php';
ob_clean();

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
        echo "https://xn--tu8h.foo.wales/images/raw/" . $image->FileHash . "." . $type;
        header("Content-Type: $image->FileType");
        #header("Content-Length: " . ob_get_length());
        ob_end_flush();
        die;
      } else {
        http_response_code(400);
        echo "Only images are allowed to be uploaded to the API";
      }
    }
  }
} else {
}
