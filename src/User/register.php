<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header('Location: /');
}

$Username = isset($_REQUEST['username']) ? $_REQUEST['username'] : false;
$Password = isset($_REQUEST['password']) ? $_REQUEST['password'] : false;
$Password_Confirm = isset($_REQUEST['password_confirm']) ? $_REQUEST['password_confirm'] : false;

if ($Username || $Password || $Password_Confirm) {
    $Error = array();
    if (!$Username) {
        $Error['username'] = "Username is required";
    }
    if (!$Password) {
        $Error['password'] = "Password is required";
    }
    if (!$Password_Confirm) {
        $Error['password_confirm'] = "Password Confirm is required";
    }
    if ($Password != $Password_Confirm) {
        $Error['password_confirm'] = "Password Confirm must match Password";
    }

    if ($Error) {
        $arguments = array(
            'errors' => $Error
        );
        $render->render_template('register', $arguments);
    } else {
        # If no simple errors exist, we can attempt to create the user.
        $User = new user();
        if ($User->check_if_username_exists($Username)) {
            $Error['username'] = "Username already exists";
        }

        if(!$User->password_complexity_check($Password)){
            $Error['password'] = "Password must be at least 8 characters long and contain at least one number, one uppercase and one lowercase letter";
        }

        # Final Error Check
        if ($Error) {
            $arguments = array(
                'errors' => $Error
            );
            $render->render_template('register', $arguments);
            exit();
        }

        # If no errors, create the new user
        $User->username = $Username;
        $User->password = $Password;
        if($User->create_user()){
            $arguments = array(
                'success' => "User created successfully"
            );
            $render->render_template('register', $arguments);
        } else {
            $arguments = array(
                'errors' => array(
                    'general' => "Error creating user"
                )
            );
            $render->render_template('register', $arguments);
        }
    }
} else {
    $render->render_template('register');
}
