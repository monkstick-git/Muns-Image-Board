<?php

class Controller404 extends Controller
{
    public function Index()
    {
        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Errors/404', array('errors' => PAGE_NOT_FOUND));
    }
}
