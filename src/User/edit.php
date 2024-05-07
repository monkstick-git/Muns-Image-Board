<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
$system->beAuthenticated();
$arguments = array();

# Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'] ?? $_POST['password'] || null;
    $passwordConfirm = $_POST['passwordConfirm'] ?? $_POST['passwordConfirm'] || null;

    if (isset($password) && $password != "") {
        if ($password == $passwordConfirm) {
            $User = new user();
            $User->get_user_by_id($_SESSION['user_id']);

            $PasswordChanged = $User->setPassword($password);

            if ($PasswordChanged == true) {
                $arguments = array(
                    'success' => array(
                        'password' => "Password Changed!"
                    )
                );
            }
        } else {
            $arguments = array(
                'errors' => array(
                    'nomatch' => "Passwords do not match!"
                )
            );
        }
    }
}


$render->render_template('User/edit', $arguments);
