<?php
include '../system/bootstrap.php';
$render->render_template('navbar');

# Echo Image and cache it for 24 hours
$files = new file();
# Array of file objects
$FileArray = $files->get_files_ids_by_owner($GLOBALS['User']->id, 'image', "`size` ASC");


$render->render_template('image-gallary', array(
  'FileArray' => $FileArray
));
?>

