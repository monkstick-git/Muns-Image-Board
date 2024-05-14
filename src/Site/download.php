<?php
ob_clean();
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

if (isset($fileID)) {
  $id = $fileID;
} else {
  die("No file found with matching ID");
}

$file = new file();
$file->get($id);

$content = $file->Content;

header("Content-Type: $file->FileType");
# set header to real filename. Use "attachment" to force download instead of view
header("Content-Disposition: attachment; filename=\"$file->FileName\"");
ob_clean();
echo $content;
ob_flush();
die();