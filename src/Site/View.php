<?php
header('Cache-Control: max-age=86400');
session_cache_limiter('none');
include '../system/bootstrap.php';
$id = $_GET['id'];

$file = new file();
$file->get($id);
$content = $file->content;
header("Content-Type: $file->filetype");
echo $content;
