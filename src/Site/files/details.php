<?php
include '../../system/bootstrap.php';
$render->render_template('navbar');

# Displays details of a file
$hash = filter_input(INPUT_GET, 'id');
$file = new file();
$file->get_by_hash($hash);
if (explode("/", $file->filetype)[0] === "image") {
  $image = new image();
  $image->get_by_hash($hash);
  #echo $file->FileHash;
  #$type = $image->filetype;
  #$FileHash = $image->FileHash;
  $render->render_template('image-details', array(
    'image' => $image
  ));
} else {
  echo "we're dealing with a file";
}
