<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header('Location: /');
}

if (isset($_REQUEST['username'])) {
    $Username = $_REQUEST['username'] ? $_REQUEST['username'] : false;
} else {
    $Username = false;
}

if (isset($_REQUEST['password'])) {
    $Password = $_REQUEST['password'] ? $_REQUEST['password'] : false;
} else {
    $Password = false;
}

if ($Username && $Password) {
    $User = new user();
    $User->get_user_by_username($Username);

    if (password_verify($Password, $User->password)) {
        $User->login();
        $_SESSION['user'] = $User;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $User->id;
        header('Location: /');
    } else {
        $render->render_template('login', array('errors' => 'Invalid username or password.'));
    }
} else {
    $render->render_template('login');
}
