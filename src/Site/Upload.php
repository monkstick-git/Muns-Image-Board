<?php
include '../system/bootstrap.php';
$render->render_template('navbar');


if ($_SESSION['user']->loggedIn) {
  if (isset($_FILES['filesToUpload'])) {
    $Files = reArrayFiles($_FILES['filesToUpload']);
    foreach ($Files as $FileChunk) {
      $file = new file();
      $file->loadObjectFromUpload($FileChunk);
      $fileType = explode("/", $file->FileType)[0];

      if ($fileType == "image") {
        unset($file);
        $image = new image();
        $image->loadObjectFromUpload($FileChunk);
        $image->thumbnail = $image->CreateImageThumbNail();
        $image->save();
      } else {
        $file->save();
      }
      echo 'success';
    }
  }
  $render->render_template('upload-form');
} else {
  $render->render_template('login');
}
