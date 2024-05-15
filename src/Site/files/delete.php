<?php
include '../../system/bootstrap.php';
$render->render_template('navbar');

# Displays details of a file
$hash = filter_input(INPUT_GET, 'id');
$file = new file();
$file->get($hash);
if($_SESSION['user']->id != $file->Owner){
    logger("🔴 User is not the owner of the file");
    $render->render_template('error', ['error' => 'You are not the owner of this file']);
    exit();
}else{
    logger("✅ User is the owner of the file - attempting to delete");
    $file->delete();
}

