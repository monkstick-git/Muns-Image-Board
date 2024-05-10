<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
$system->beAuthenticated();

# Get a list of all the users files
$UsersFiles = new file();
$files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);

$arguments = array(
    'FileArray' => $files
);


$render->render_template('file-browser', $arguments);


