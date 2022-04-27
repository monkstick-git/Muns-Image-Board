<?php include './system/bootstrap-light.php';

if (isset($_FILES['data'])) {
  if (isset($_REQUEST['api'])) {
    $GLOBALS['User']->get_user_by_api($_REQUEST['api']);
    if ($GLOBALS['User']->id) {
      $file = new file();
      $file->loadObjectFromUpload($_FILES['data']);
      $file->save();
      $type = explode("/",$file->FileType)[1];
      echo "https://📷.foo.wales/images/raw/" . $file->FileID . "." . $type;
    }
  }
} else {
}
