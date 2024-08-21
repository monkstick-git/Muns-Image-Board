<?php
include '../../system/bootstrap.php';
$render->render_template('navbar');
$PreviousPage = $_SERVER['HTTP_REFERER'];

# Displays details of a file
$hash = filter_input(INPUT_GET, 'id');
$file = new file();
$file->get($hash);
if($_SESSION['user']->id != $file->Owner){
    mlog("🔴 User is not the owner of the file");
    $render->render_template('error', ['error' => 'You are not the owner of this file']);
    exit();
}else{
    mlog("✅ User is the owner of the file - attempting to delete");
    if($file->delete()){
        mlog("✅ File deleted");
    }else{
        mlog("🔴 File not deleted");
    }
    
    # Redirect back to where the user came from
    header("Location: $PreviousPage");
}

