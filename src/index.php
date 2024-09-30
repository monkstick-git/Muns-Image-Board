<?php 
// Include the bootstrap file for necessary initializations
require_once './system/bootstrap.php';

// Function to redirect users
function redirect($url): never {
    header("Location: {$url}");
    exit;
}

// Check user login status and redirect accordingly
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    redirect(url: '/User/login');
} else {
    redirect(url: '/Site/Gallary');
}