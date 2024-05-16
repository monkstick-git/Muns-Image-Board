<?php
header('Cache-Control: max-age=86400');
session_cache_limiter('none');
include '../system/bootstrap.php';
$id = 0;
$splitURL = (explode("/", $_GET['id']));
# thumbnail or original
$fileType = $splitURL[2];
$fileID = $splitURL[3];
# Get the left hand side of the filename.type (e.g: 10.jpg -> 10)
$fileID = explode(".", $fileID)[0];

if ((isset($_GET['type']))) {
  $type = $_GET['type'];
} else {
  if ((isset($fileType))) {
    $type = $fileType;
  } else {
    $type = false;
  }

}

if (isset($fileID)) {
  $id = $fileID;
}
$file = new image();
if($file->get($id) == false){
  mlog("Failed to get image:" . $id);
  echo "File not Found or you do not have permission to view this file";
  die();
}

if ($type == 'thumbnail') {
  if (isset($file->Thumbnail) && $file->Thumbnail != "") {
    $content = $file->Thumbnail;
  } else {
    $file->ThumbNail = $file->CreateImageThumbNail();
    $file->update();
    $content = $file->Thumbnail;
  }
} else {
  $content = $file->Content;
}

header("Content-Type: $file->FileType");
# set header to real filename. Use "attachment" to force download instead of view
header("Content-Disposition: inline; filename=\"$file->FileName\"");

echo $content;
