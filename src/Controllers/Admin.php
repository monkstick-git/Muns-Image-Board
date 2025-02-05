<?php

class ControllerAdmin extends Controller
{

    public function __construct($DisableRender = false)
    {
        parent::__construct($DisableRender);
    }

    public function Index()
    {
        if (!Registry::get('system')->beAuthenticated()) {
            Registry::get('system')->redirect(location: '/User/login');
            return;
        }

        // TODO: Check if the user has the admin privileges instead of just checking if the user is an 'admin'
        if (!Registry::get('system')->beAdmin()) {
            Registry::get('system')->redirect(location: '/');
            return;
        }


        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Site/Admin/index');
    }

    public function Users()
    {
        if (!Registry::get('system')->beAuthenticated()) {
            Registry::get('system')->redirect(location: '/User/login');
            return;
        }

        if (!Registry::get('system')->beAdmin()) {
            Registry::get('system')->redirect(location: '/');
            return;
        }

        $Users = new user();
        $Users = $Users->getAllUsers();

        $Arguments = [
            'Users' => $Users
        ];

        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Site/Admin/users', $Arguments);
    }

    public function Files()
    {
        if (!Registry::get('system')->beAuthenticated()) {
            Registry::get('system')->redirect(location: '/User/login');
            return;
        }

        if (!Registry::get('system')->beAdmin()) {
            Registry::get('system')->redirect(location: '/');
            return;
        }

        $files = new file();
        $FileArray = $files->Find(null, null, "`id` DESC", 1000);

        Registry::get('render')->render_template(
            'Site/Files/browse',
            array(
                'FileArray' => $FileArray
            )
        );
    }

    public function Permissions()
    {
        if (!Registry::get('system')->beAuthenticated()) {
            Registry::get('system')->redirect(location: '/User/login');
            return;
        }

        if (!Registry::get('system')->beAdmin()) {
            Registry::get('system')->redirect(location: '/');
            return;
        }
        Registry::get('render')->render_template('Core/navbar');

        $Permissions = Registry::get('User')->getAllPermissions();
        # Load the .json file from system/permissions.json
        $PermissionMatrix = json_decode(file_get_contents('system/permissions.json'), true);
        $Arguments = [
            'Permissions' => $Permissions,
            'PermissionMatrix' => $PermissionMatrix
        ];
        Registry::get('render')->render_template('Site/Admin/permissions', $Arguments);
    }
}