<?php

class userHelper extends helper
{

    public $username;
    public $password;
    public $password_confirm;
    public $csrf;
    public $submittedCSRF;

    public function authenticate()
    {
        $user = new User();
        $user->get_user_by_username($this->username);

        if ($user && password_verify($this->password, $user->password)) {        
            return $user; // Return user object if authentication succeeds
        }

        return false; // Return false if authentication fails
    }

    public function validateCsrf()
    {
        logger($this->csrf);
        logger($this->submittedCSRF);
        # Check if either csrf or submittedCSRF is empty
        if (!$this->csrf || !$this->submittedCSRF) {
            return false;
        }else{
            return $this->csrf === $this->submittedCSRF;
        }
        
    }

    public function login($user)
    {
        Registry::get('User')->login();        
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
    }

    public function redirect_if_logged_in()
    {
        // Redirect if already logged in
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: /');
            exit();
        }

    }

    public function getLoginInput()
    {
        $this->username = $_REQUEST['username'] ?? false;
        $this->password = $_REQUEST['password'] ?? false;
        $this->csrf = $_REQUEST['csrf'] ?? '';
        $this->submittedCSRF = $_SESSION['User']->CSRF->Token;
    }

    public function getRegisterInput()
    {
        $this->username = $_REQUEST['username'] ?? false;
        $this->password = $_REQUEST['password'] ?? false;
        $this->password_confirm = $_REQUEST['password_confirm'] ?? false;
        $this->csrf = $_REQUEST['csrf'] ?? '';
        $this->submittedCSRF = $_SESSION['User']->CSRF->Token;
    }

    public function checkRegisterInput()
    {
        $Error = array();
        if (!$this->username) {
            $Error['username'] = REGISTER_USERNAME_REQUIRED;
        }
        if (!$this->password) {
            $Error['password'] = REGISTER_PASSWORD_REQUIRED;
        }
        if (!$this->password_confirm) {
            $Error['password_confirm'] = REGISTER_PASSWORD_CONFIRM_REQUIRED;
        }
        if ($this->password != $this->password_confirm) {
            $Error['password_confirm'] = REGISTER_PASSWORD_MISMATCH;
        }

        return $Error;
    }

    public function registerUser()
    {
        $User = new user();
        $Error = array();
        if ($User->check_if_username_exists($this->username)) {
            $Error['username'] = REGISTER_USER_ALREADY_EXISTS;
        }

        if (!$User->password_complexity_check($this->password)) {
            $Error['password'] = REGISTER_PASSWORD_COMPLEXITY_ERROR;
        }

        # Final Error Check
        if ($Error) {
            $arguments = array(
                'errors' => $Error
            );
            return $arguments;
        }

        # If no errors, create the new user
        $User->username = $this->username;
        $User->password = $this->password;
        if ($User->create_user()) {
            return true; // Return true if user creation succeeds
        } else {
            $arguments = array(
                'errors' => array(
                    'general' => REGISTER_ERROR_GENERIC
                )
            );
            return $arguments;
        }
    }

    public function changePassword($ArgumentList, $User){
        if ($ArgumentList['password'] == $ArgumentList['passwordConfirm']) {
            $PasswordChanged = $User->setPassword($ArgumentList['password']);

            if ($PasswordChanged) {
                $arguments['success']['password'] = "Password Changed!";
                $profileUpdated = true;
            } else {
                $arguments['errors']['password'] = "Failed to change the password.";
            }
        } else {
            $arguments['errors']['nomatch'] = "Passwords do not match!";
        }

        return $arguments;
    }

    public function hasPermission($Permission){
        return $_SESSION['User']->has_permission($Permission);
    }

    public function getUsersFiles($ArgumentList){
        $UsersFiles = new file();

        if(isset($ArgumentList['sortType']) && isset($ArgumentList['sortDir'])){
            $sortType = $ArgumentList['sortType'];
            $sortDir = $ArgumentList['sortDir'];
        
            # Allowed sort types:
            $allowedSortTypes = array("name", "size", "created", "modified");
        
            # Allowed sort directions:
            $allowedSortDirs = array("ASC", "DESC");
        
            if(in_array($sortType, $allowedSortTypes) && in_array($sortDir, $allowedSortDirs)){
                mlog("✅ Sort order is set to: $sortType $sortDir");
                $files = $UsersFiles->Find($_SESSION['user_id'], null, "`$sortType` $sortDir", 1000);
            }else{
                mlog("❌ Sort order is set to: $sortType $sortDir");
                $files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);
            }
        }else{
            $files = $UsersFiles->Find($_SESSION['user_id'], null, "`id` DESC", 1000);
        }

        return $files;

    }

}