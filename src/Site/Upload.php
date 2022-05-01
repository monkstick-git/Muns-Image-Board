<?php
include '../system/bootstrap.php';
$render->render_template('navbar');

if ($_SESSION['user']->loggedIn) {
  if (isset($_FILES['fileToUpload'])) {
    $file = new file();
    $file->loadObjectFromUpload($_FILES['fileToUpload']);
    $fileType = explode("/", $file->FileType)[0];

    if ($fileType == "image") {
      unset($file);
      $image = new image();
      $image->loadObjectFromUpload($_FILES['fileToUpload']);
      $image->thumbnail = $image->CreateImageThumbNail();
      $image->save();
    }else{
      $file->save();
    }
  }
  $render->render_template('upload-form');
} else {
  $render->render_template('login');
}
