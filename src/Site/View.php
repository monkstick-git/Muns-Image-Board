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

if ( (isset($_GET['type']))  ) {
  $type = $_GET['type'];
} else {
  if((isset($fileType))){
    $type = $fileType;
  }else{
    $type = false;
  }
  
}

if(isset($fileID)){
  $id = $fileID;
  #$id = 'a754dde58a9d283901db01a4a75b5a0a';
}
$file = new image();
$file->get_by_hash($id);

if ($type == 'thumbnail') {
  $content = $file->thumbnail;
} else {
  $content = $file->content;
}
#$content = $file->thumbnail;
header("Content-Type: $file->filetype");

echo $content;
