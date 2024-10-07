<?php

class ControllerAdmin extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function Index()
    {
        if (!$this->system->beAuthenticated()) {
            $this->system->redirect(location: '/User/login');
            return;
        }

        if (!$this->system->beAdmin()) {
            $this->system->redirect(location: '/');
            return;
        }


        $this->render->render_template('Core/navbar');
        $this->render->render_template('Site/Admin/index');
    }

    public function Users()
    {
        if (!$this->system->beAuthenticated()) {
            $this->system->redirect(location: '/User/login');
            return;
        }

        if (!$this->system->beAdmin()) {
            $this->system->redirect(location: '/');
            return;
        }

        $Users = new user();
        $Users = $Users->getAllUsers();

        $Arguments = [
            'Users' => $Users
        ];

        $this->render->render_template('Core/navbar');
        $this->render->render_template('Site/Admin/users', $Arguments);
    }

    public function Files()
    {
        if (!$this->system->beAuthenticated()) {
            $this->system->redirect(location: '/User/login');
            return;
        }

        if (!$this->system->beAdmin()) {
            $this->system->redirect(location: '/');
            return;
        }

        $files = new file();
        $FileArray = $files->Find(null, null, "`id` DESC", 1000);

        $this->render->render_template(
            'Site/Files/browse',
            array(
                'FileArray' => $FileArray
            )
        );
    }
}