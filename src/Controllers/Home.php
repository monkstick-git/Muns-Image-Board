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
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: /User/login');
            die();
        }
    
        $adminMenu = false;
        if (isset($_REQUEST['admin']) && $_REQUEST['admin'] === 'true' && $GLOBALS['User'] && $GLOBALS['User']->is_admin()) {
            $adminMenu = true;
        }
    

    
        // Detect AJAX request (if the request was made via JavaScript)
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
            $items_per_page = 5;
            $offset = ($page - 1) * $items_per_page;
            $limit = "$offset, $items_per_page";
        
            $files = new file();
            $FileArray = $adminMenu ? $files->Find(null, ["image"], "`id` DESC", $limit) : $files->Find($GLOBALS['User']->id, ["image"], "`id` DESC", $limit);
            
            $totalItems = $files->Count($GLOBALS['User']->id);
            $totalPages = ceil($totalItems / $items_per_page);            

            // Only return the new gallery items
            Registry::get('render')->render_template('Site/Image/gallery_partial', [
                'FileArray' => $FileArray,
                'adminMenu' => $adminMenu
            ]);
            return;
        }else{
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
            $items_per_page = 8;
            $offset = ($page - 1) * $items_per_page;
            $limit = "$offset, $items_per_page";
        
            $files = new file();
            $FileArray = $adminMenu ? $files->Find(null, ["image"], "`id` DESC", $limit) : $files->Find($GLOBALS['User']->id, ["image"], "`id` DESC", $limit);
            
            $totalItems = $files->Count($GLOBALS['User']->id);
            $totalPages = ceil($totalItems / $items_per_page);            
        }
    
        // Full page rendering
        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Site/Image/gallery', [
            'FileArray' => $FileArray,
            'adminMenu' => $adminMenu,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    
}
