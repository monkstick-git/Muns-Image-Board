<?php 
include './system/bootstrap.php'; 

// Redirect users based on their login status before rendering any output
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: /User/login');
    exit; // Ensure no further code is executed after the redirect
} else {
    header('Location: /Site/Gallary');
    exit; // Ensure no further code is executed after the redirect
}

// Render the navbar template only if no redirect occurs (though in this case, it won't be reached)
$render->render_template('navbar');
?>
