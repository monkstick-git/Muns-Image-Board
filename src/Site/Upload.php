<?php
include '../system/bootstrap.php';
$render->render_template('navbar');


if ($_SESSION['user']->loggedIn) {
  if (isset($_FILES['filesToUpload'])) {
    # Check if $_POST['public'] is set to 1, if not set it to 0
    if (!isset($_POST['public'])) {
      logger("✅File privacy is not set, defaulting to private");
      $Privacy = false; # Default to private
    }else{
      logger("✅Upload File privacy is set to: " . $_POST['public']);
      $Privacy = $_POST['public']; # Set to users choice
      # Convert Privacy "true" and "false" strings to boolean
      $Privacy = filter_var($Privacy, FILTER_VALIDATE_BOOLEAN);
      logger(gettype($Privacy));
      logger("🔴 PRIVACY SETTING IS: " . $Privacy);
    }

    logger("File privacy is set to: " . $Privacy);
    # Convert the boolean to an int
    if($Privacy == true){
      logger("✅ File is uploaded with public privacy");
      $Privacy = 1;
    }else{
      logger("🔒 File is uploaded with private privacy");
      $Privacy = 0;
    }

    $Files = reArrayFiles($_FILES['filesToUpload']); # Change the array so each file is ordered
    foreach ($Files as $FileChunk) {
      $file = new file();
      $file->PublicFile = $Privacy;
      $file->setFromUpload($FileChunk);
      $fileType = explode("/", $file->FileType)[0];

      if ($fileType == "image") {
        unset($file);
        $image = new image();
        $image->PublicFile = $Privacy;
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
