<?php

class ControllerHome extends Controller
{
    public function Index()
    {
        #Registry::get('render')->render_template('Core/header');
        Registry::get('render')->render_template('Core/navbar');
        #Registry::get('render')->render_template('Core/footer');
    }

    public function Gallery()
    {
        // Check if the user is logged in; if not, redirect to the login page.
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /User/login');
            die();  // Stop further execution after redirect.
        }


        Registry::get('render')->render_template('Core/navbar');

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
        $FileArray = $adminMenu ? $files->Find(null, array("image"), "`id` DESC", $limit) : $files->Find($GLOBALS['User']->id, array("image"), "`id` DESC", $limit);

        // Calculate the total number of pages for pagination.
        $totalItems = $files->Count($GLOBALS['User']->id);
        $totalPages = ceil($totalItems / $items_per_page);


        Registry::get('render')->render_template('Site/Util/paginate', [
            'page' => $page,
            'pages' => $totalPages,
            'url' => "/Home/Gallery?page=",
        ]);

        // Render the image gallery.
        Registry::get('render')->render_template('Site/Image/gallery', [
            'FileArray' => $FileArray,
            'adminMenu' => $adminMenu,
        ]);

        // Render the pagination controls.
        Registry::get('render')->render_template('Site/Util/paginate', [
            'page' => $page,
            'pages' => $totalPages,
            'url' => "/Home/Gallery?page=",
        ]);


    }
}
