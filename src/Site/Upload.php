<?php
include '../system/bootstrap.php';
$render->render_template('navbar');


if ($_SESSION['user']->loggedIn) {
  if (isset($_FILES['filesToUpload'])) {
    $Files = reArrayFiles($_FILES['filesToUpload']); # Change the array so each file is ordered
    foreach ($Files as $FileChunk) {
      $file = new file();
      $file->setFromUpload($FileChunk);
      $fileType = explode("/", $file->FileType)[0];

      if ($fileType == "image") {
        unset($file);
        $image = new image();
        $image->setFromUpload($FileChunk);
        $image->thumbnail = $image->CreateImageThumbNail();
        $image->set();
      } else {
        $file->set();
      }
      echo 'success';
    }
  }
  $render->render_template('upload-form');
} else {
  $render->render_template('login');
}
