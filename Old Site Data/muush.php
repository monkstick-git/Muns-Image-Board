<?php
require 'bootstrap.php';
$uploadedFile = $_FILES["data"];
# Sets to ./tmp/random.filetype
$tmpname = ROOT . '/tmp/' . str_replace(".", "", microtime(true)) . "." . pathinfo($_FILES["data"]["name"])['extension'];
#$tmpname = ROOT . '/tmp/' . "file.png";

# write to error log
error_log("Uploaded file: " . $uploadedFile['name'] . " to " . $tmpname);

if (in_array(pathinfo(strtolower($tmpname))['extension'], $settings['banned_formats'])) {
    echo "Illegal File type,  Sorry!";
    http_response_code(403);
    exit;
}

#print_r($_FILES["data"]);

#move_uploaded_file($_FILES["data"]["tmp_name"], $tmpname);
if ( empty($_FILES["data"]["tmp_name"]) ){
    echo "Failed to copy file";
    http_response_code(500);
    exit;
}
copy($_FILES["data"]["tmp_name"], $tmpname);
$ImgName = "uploads/" . str_replace(".", "", microtime(true)) . "." . pathinfo($tmpname)['extension'];
file_put_contents($ImgName, file_get_contents($tmpname));

if (in_array(pathinfo(strtolower($tmpname))['extension'], $settings['allowed_video_formats'])) {
    echo $settings['site_url'] . "video.php?vid=" . $ImgName;
} else {
    echo $settings['site_url'] . $ImgName;
}

# Delete file
unlink($tmpname);

#<b>Warning</b>:  file_get_contents(./tmp/16492740933866.png): Failed to open stream: No such file or directory in <b>/var/www/default/htdocs/muush.php</b> on line <b>15</b><br />
#http://localhost:8080/./tmp/16492740933866.png
