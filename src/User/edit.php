<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
$system->beAuthenticated();
$arguments = array();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'] ?? null;
    $passwordConfirm = $_POST['passwordConfirm'] ?? null;
    $firstName = $_POST['firstName'] ?? null;
    $lastName = $_POST['lastName'] ?? null;
    $bio = $_POST['bio'] ?? null;

    $User = new user();
    $User->get_user_by_id($_SESSION['user_id']);

    $profileUpdated = false;

    // Update password if provided
    if (!empty($password)) {
        if ($password == $passwordConfirm) {
            $PasswordChanged = $User->setPassword($password);

            if ($PasswordChanged) {
                $arguments['success']['password'] = "Password Changed!";
                $profileUpdated = true;
            } else {
                $arguments['errors']['password'] = "Failed to change the password.";
            }
        } else {
            $arguments['errors']['nomatch'] = "Passwords do not match!";
        }
    }

    // Update first name, last name, and bio
    $updates = [];
    if (!empty($firstName)) {
        $User->name = $firstName;
        $updates[] = 'First Name';
    }

    if (!empty($lastName)) {
        $User->surname = $lastName;
        $updates[] = 'Last Name';
    }

    if (!empty($bio)) {
        $User->bio = $bio;
        $updates[] = 'Bio';
    }

    if (!empty($updates)) {
        if ($User->update()) {
            $arguments['success']['profile'] = "Profile updated: " . implode(", ", $updates);
            $profileUpdated = true;
        } else {
            $arguments['errors']['profile'] = "Failed to update profile.";
        }
    }

    // Redirect to profile page if the profile was successfully updated
    if ($profileUpdated) {
        header("Location: /User/profile");
        exit();
    }
}

$render->render_template('User/edit', $arguments);
