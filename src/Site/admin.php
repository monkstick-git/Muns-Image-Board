<?php
include '../system/bootstrap.php';
$render->render_template('navbar');
$limit = 100000;

# ToDo
global $system;
if (false == $system->beAdmin()) {
  echo "Not authorized";
  exit;
}

$files = new file();
$FileArray = $files->Find(null, null, "`id` DESC", $limit);

$render->render_template('file-browser', array(
  'FileArray' => $FileArray
)
);
