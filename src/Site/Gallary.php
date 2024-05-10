<?php
include '../system/bootstrap.php';
$render->render_template('navbar');
if (!isset($_SESSION['logged_in'])) {
  header('Location: /User/login');
  $render->render_template('login');
  die();
} else {
  #$render->render_template('login');
}


$adminMenu = false;
#if (isset(($_REQUEST['admin']))) {
if (isset($_REQUEST['admin']) && $_REQUEST['admin'] == 'true') {
  if ($GLOBALS['User'] && $GLOBALS['User']->is_admin()) {
    $adminMenu = true;
  }
}
#}


#$page = 1;
$page = abs(filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));
if ($page <= 0) {
  $page = 1;
}

$items_per_page = 9;
$offset = ($page - 1) * $items_per_page;
$limit = $offset . "," . $items_per_page;

$files = new file();
# Array of file objects
if (false == $adminMenu) {
  # Find all 'Image' files owned by the current user
  $FileArray = $files->Find($GLOBALS['User']->id, 'image', "`id` DESC", $limit);
} else {
  $FileArray = $files->Find(null, 'image', "`id` DESC", $limit);
}


$totalItems = $files->Count($GLOBALS['User']->id);
$totalPages = ceil($totalItems / $items_per_page);
#echo($totalItems);
$render->render_template('image-gallary', array(
  'FileArray' => $FileArray,
  'adminMenu' => $adminMenu
)
);

$render->render_template('paginate', array(
  'page' => $page,
  'pages' => $totalPages,
  'url' => "/Site/Gallary?page="
)
);
