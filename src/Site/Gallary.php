<?php
include '../system/bootstrap.php';
$render->render_template('navbar');

$adminMenu = false;
if (isset(($_REQUEST['admin']))) {
  if ($_REQUEST['admin'] == 'true') {
    if ($GLOBALS['User'] && $GLOBALS['User']->is_admin()) {
      $adminMenu = true;
    }
  }
}


#$page = 1;
$page = abs(filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));
if ($page <= 0) {
  $page = 1;
}

$items_per_page = 9;
$offset = ($page - 1) * $items_per_page;
$limit = $offset . "," . $items_per_page;

$files = new image();
# Array of file objects
if (false == $adminMenu) {
  $FileArray = $files->get_files_by_owner($GLOBALS['User']->id, 'image', "`id` DESC", $limit);
} else {
  $FileArray = $files->get_all_owners_files('image', "`id` DESC", $limit);
}

$totalItems = $files->count_total_items($GLOBALS['User']->id);

$totalPages = ceil($totalItems / $items_per_page);
#echo($totalItems);
$render->render_template('image-gallary', array(
  'FileArray' => $FileArray,
  'adminMenu' => $adminMenu
));

$render->render_template('paginate', array(
  'page' => $page,
  'pages' => $totalPages,
  'url' => "/Site/Gallary?page="
));
