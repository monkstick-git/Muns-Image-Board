<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');

$CSRF = $_SESSION['User']->CSRF->Token;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header('Location: /');
}

# Check if the permission is set
# Commenting out as all users should be able to login
// if($_SESSION['User']->has_permission(SYSTEM_CAN_LOGIN)){
//     logger("User has permission to login");
// }else{
//     logger("User does not have permission to login");
//     $render->render_template('login', array('errors' => 'You do not have permission to login.'));
//     die();
// }

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
    $User = $_SESSION['User'];
    $User->get_user_by_username($Username);

    # Check the CSRF 
    if($CSRF==$_REQUEST['csrf']){
        # CSRF is valid
        logger("CSRF is valid");
    }else{
        logger("CSRF is NOT valid");
        logger("CSRF of User : " . $CSRF);
        logger("CSRF Recieved: " . $_REQUEST['csrf']);
        $render->render_template('login', array('errors' => 'Invalid CSRF token, Please refresh the page and try again.'));
        die();
    }

    if ((password_verify($Password, $User->password))) {
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
