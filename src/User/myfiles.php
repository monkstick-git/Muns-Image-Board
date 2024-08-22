<?php
require_once '../system/bootstrap.php';

if (!isset($_SESSION['logged_in'])) {
  header('Location: /User/login');
  $render->render_template('login');
  die();
} else {

}

$render->render_template('navbar');
$system->beAuthenticated();

if($_SESSION['User']->has_permission("FILE.READ_OWN")){
    mlog("User has permission to read own files");
}else{
    mlog("User does not have permission to read own files");
    $render->render_template('error', array('error' => 'You do not have permission to view your files.'));
    die();
}

# Get a list of all the users files
$UsersFiles = new file();
# Check if there is a sort order set
if(isset($_GET['sortType']) && isset($_GET['sortDir'])){
    $sortType = $_GET['sortType'];
    $sortDir = $_GET['sortDir'];

    # Allowed sort types:
    $allowedSortTypes = array("name", "size", "created", "modified");

    # Allowed sort directions:
    $allowedSortDirs = array("ASC", "DESC");

    if(in_array($sortType, $allowedSortTypes) && in_array($sortDir, $allowedSortDirs)){
        mlog("✅ Sort order is set to: $sortType $sortDir");
        $files = $UsersFiles->Find($_SESSION['user_id'], null, "`$sortType` $sortDir", 1000);
    }else{
        mlog("❌ Sort order is set to: $sortType $sortDir");
        $files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);
    }

}else{
    $files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);
}

$arguments = array(
    'FileArray' => $files
);

$render->render_template('file-browser', $arguments);