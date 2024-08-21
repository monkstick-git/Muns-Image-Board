<?php
/**
 * This script handles the display of the image gallery, including pagination and admin options.
 * It checks if the user is logged in, determines if the admin menu should be shown, and renders the gallery and pagination.
 */

include '../system/bootstrap.php';

// Check if the user is logged in; if not, redirect to the login page.
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /User/login');
    die();  // Stop further execution after redirect.
}

// Render the navbar.
$render->render_template('navbar');

// Determine if the admin menu should be shown.
$adminMenu = false;
if (isset($_REQUEST['admin']) && $_REQUEST['admin'] === 'true' && $GLOBALS['User'] && $GLOBALS['User']->is_admin()) {
    $adminMenu = true;
}

// Get the current page number from the query parameters, default to 1 if not set or invalid.
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

// Set items per page and calculate the offset for the database query.
$items_per_page = 9;
$offset = ($page - 1) * $items_per_page;
$limit = "$offset, $items_per_page";

// Initialize the file object to retrieve files.
$files = new file();

// Fetch the files to display based on user role (admin or regular user).
$FileArray = $adminMenu ? $files->Find(null, 'image', "`id` DESC", $limit) : $files->Find($GLOBALS['User']->id, 'image', "`id` DESC", $limit);

// Calculate the total number of pages for pagination.
$totalItems = $files->Count($GLOBALS['User']->id);
$totalPages = ceil($totalItems / $items_per_page);

// Render the image gallery.
$render->render_template('image-gallary', [
    'FileArray' => $FileArray,
    'adminMenu' => $adminMenu,
]);

// Render the pagination controls.
$render->render_template('paginate', [
    'page' => $page,
    'pages' => $totalPages,
    'url' => "/Site/Gallary?page=",
]);

