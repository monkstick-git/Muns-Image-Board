<?php

class ControllerUser extends Controller
{

    private $userHelper;

    public function __construct()
    {
        parent::__construct();
        include_once(ROOT . '/Controllers/Helpers/userHelper.php');
        $this->userHelper = new userHelper();
    }

    /**
     * Display the login form and handle the login process
     * @return void
     */
    public function Login()
    {
        $this->render->render_template('Core/navbar');

        # Redirect if already logged in
        $this->userHelper->redirect_if_logged_in();
        $this->userHelper->getLoginInput(); // Get the input from the user, username, password, and CSRF token and store it in the userHelper object.

        # Display the login form if the request method is GET
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->render->render_template('Site/Auth/login');
        }

        if ($this->userHelper->username && $this->userHelper->password) {
            if (!$this->userHelper->validateCsrf()) {
                $this->render->render_template('Site/Auth/login', ['errors' => CSRF_ERROR]);
                return;
            }

            // Attempt to authenticate the user. If successful, $authenticatedUser will be a User object. Otherwise, it will be false.
            $authenticatedUser = $this->userHelper->authenticate();

            if ($authenticatedUser) {
                $this->userHelper->login(user: $authenticatedUser);
                $this->system->redirect('/');
                return;
            } else {
                $this->render->render_template('Site/Auth/login', ['errors' => USERNAME_PASSWORD_ERROR]);
                return;
            }
        }

    }

    /**
     * Log the user out
     * @return void
     */
    public function Logout()
    {
        session_start();
        # clear the $_SESSION
        session_unset();
        # destroy the session
        unset($_SESSION);
        session_destroy();

        $this->system->redirect('/User/LoggedOut');
    }

    /**
     * Display the logged out page
     * @return void
     */
    public function LoggedOut()
    {
        $this->render->render_template('Core/navbar');
        $arguments = array(
            'message' => LOGGED_OUT_MESSAGE,
            'message_header' => LOGGED_OUT_MESSAGE_HEADER
        );
        $this->render->render_template('Site/Auth/loggedout', $arguments);
        return;
    }

    /**
     * Display the registration form and handle the registration process
     * @return void
     */
    public function Register()
    {
        $this->userHelper->redirect_if_logged_in();
        $this->render->render_template('Core/navbar');

        # Check if GET request
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->render->render_template('Site/Auth/register');
            return;
        }

        # Get the input from the user, username, password, password_confirm, and CSRF token and store it in the userHelper object.
        $this->userHelper->getRegisterInput();

        # Check CSRF token second, after we have the input.
        if (!$this->userHelper->validateCsrf()) {
            $arguments = array(
                'errors' => CSRF_ERROR
            );
            $this->render->render_template('Site/Auth/register', $arguments);
            return;
        }

        $Error = $this->userHelper->checkRegisterInput(); // Check the input for errors.

        if ($Error) {
            $arguments = array(
                'errors' => $Error
            );
            $this->render->render_template('Site/Auth/register', $arguments);
            return;
        }

        # If no simple errors exist, we can attempt to create the user.
        $RegisterSuccess = $this->userHelper->RegisterUser();
        if ($RegisterSuccess === true) {
            $this->render->render_template('Site/Auth/login', ['success' => REGISTER_SUCCESS_MESSAGE]);
        } else {
            $this->render->render_template('Site/Auth/register', ['errors' => $RegisterSuccess]);
        }
    }

    /**
     * View the profile of the currently logged in user
     * @return void
     */
    public function Profile()
    {
        $this->system->beAuthenticated(); // Redirect if not logged in

        $this->render->render_template('Core/navbar');
        $this->render->render_template('Site/Auth/profile');
    }

    /**
     * Edit the currently logged in user's profile
     * @return void
     */
    public function Edit()
    {
        $this->system->beAuthenticated(); // Redirect if not logged in

        $this->render->render_template('Core/navbar');
        $arguments = array();

        if ($this->requestType == 'GET') {
            $this->render->render_template('Site/Auth/edit', $arguments);
            return;
        }

        // Check if the form has been submitted
        if ($this->requestType == 'POST') {
            $User = new user();
            $User->get_user_by_id($_SESSION['user_id']);

            $profileUpdated = false;

            // Update password if provided
            if (!empty($this->ArgumentList['password'])) {
                // Bit hacky but this attempts to change the password and returns an array of success and error messages
                $arguments = $this->userHelper->changePassword(ArgumentList: $this->ArgumentList, User: $User);
                $this->render->render_template('Site/Auth/edit', $arguments);
                return;
            }

            // Update first name, last name, and bio
            $updates = [];
            if (!empty($this->ArgumentList['firstName'])) {
                $User->name = $this->ArgumentList['firstName'];
                $updates[] = 'First Name';
            }

            if (!empty($this->ArgumentList['lastName'])) {
                $User->surname = $this->ArgumentList['lastName'];
                $updates[] = 'Last Name';
            }

            if (!empty($this->ArgumentList['bio'])) {
                $User->bio = $this->ArgumentList['bio'];
                $updates[] = 'Bio';
            }

            if (!empty($updates)) {
                if ($User->update()) {
                    $arguments['success']['profile'] = "Profile updated: " . implode(", ", $updates);
                    $this->render->render_template('Site/Auth/edit', $arguments);
                    return;
                } else {
                    $arguments['errors']['profile'] = "Failed to update profile.";
                    $this->render->render_template('Site/Auth/edit', $arguments);
                    return;
                }
            }
        }
    }

    /**
     * View the files of the currently logged in user
     */
    public function Files()
    {
        $this->system->beAuthenticated(); // Redirect if not logged in
        
        $this->render->render_template('Core/navbar');
        if (!$this->userHelper->hasPermission("FILE.READ_OWN")) {
            $this->render->render_template('Core/error', ['errors' => ERROR_PERMISSION_DENIED]);
            return;
        }

        // If we are here, the user has permission to view their files.
        $UsersFiles = $this->userHelper->getUsersFiles($this->ArgumentList);
        $arguments = array(
            'FileArray' => $UsersFiles
        );

        $this->render->render_template('Site/Files/browse', $arguments);
    }
}