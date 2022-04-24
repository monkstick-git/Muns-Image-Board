<?php
include '../system/bootstrap.php';
$render->render_template('navbar');

if($_SESSION['user']->loggedIn){
  if(isset($_FILES['fileToUpload'])){
    $file = new file();
    $file->loadObjectFromUpload($_FILES['fileToUpload']);
    #$file->encoded  = ($file->blob( file_get_contents($_FILES['fileToUpload']['tmp_name'] )));
    #$file->Owner    = $GLOBALS['User']->id;
    #$file->FileType = $_FILES['fileToUpload']['type'];
    $file->save();
  }else{

  }
  $render->render_template('upload-form');
}else{
  $render->render_template('login');
}
