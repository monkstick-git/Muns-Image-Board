<?php
include '../../system/bootstrap.php';
$render->render_template('navbar');

# Displays details of a file
$hash = filter_input(INPUT_GET, 'id');
$file = new file();
$file->get($hash);
if (explode("/", $file->FileType)[0] === "image") {
  echo $file->FileHash;
  $type = $file->FileType;
  $FileHash = $file->FileHash;
  $render->render_template('image-details', array(
    'image' => $file
  )
  );
} else {
  echo "we're dealing with a file";
}
