<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
$system->beAuthenticated();

if($_SESSION['User']->has_permission("FILE.READ_OWN")){
    logger("User has permission to read own files");
}else{
    logger("User does not have permission to read own files");
    $render->render_template('error', array('error' => 'You do not have permission to view your files.'));
    die();
}

# Get a list of all the users files
$UsersFiles = new file();
$files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);

$arguments = array(
    'FileArray' => $files
);

$render->render_template('file-browser', $arguments);