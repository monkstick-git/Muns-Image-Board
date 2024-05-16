<?php
include '../../system/bootstrap.php';
$render->render_template('navbar');

# Displays details of a file
$hash = filter_input(INPUT_GET, 'id');
$file = new file();
$file->get($hash);
if (explode("/", $file->FileType)[0] === "image") {
  $type = $file->FileType;
  $FileHash = $file->FileHash;
  $render->render_template('image-details', array(
    'image' => $file
  )
  );
} else {
  mlog("Invalid image type: " . $file->FileType);
}
