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
        echo $settings['site_url'] . "images/raw/" . $image->FileHash . "." . $type . "?api=1";
        header("Content-Type: $image->FileType");
        #header("Content-Length: " . ob_get_length());
        ob_end_flush();
        die;
      } else {
        $exploded = explode(".", $_FILES['data']['name']);
        $count = count($exploded);
        $fileTypeGuess =  $exploded[$count - 1];
        #die;
        $file = new file();
        $file->loadObjectFromUpload($_FILES['data']);
        $file->save();
        $type = $fileTypeGuess; #explode("/", $file->FileType)[1];
        ob_clean();
        ob_start();
        echo $settings['site_url'] . "Site/download/" . $file->FileHash . "." . $type . "?api=1";
        header("Content-Type: $file->FileType");
        #die;
      }
    }else{
      # Throw Unauthorised
      header("HTTP/1.0 401 Unauthorized");
      die;
    }
  }
} else {
}
