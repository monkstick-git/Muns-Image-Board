<?php

class Controller404 extends Controller
{
    public function Index()
    {
        $this->render->render_template('Core/navbar');
        $this->render->render_template('Errors/404', array('errors' => PAGE_NOT_FOUND));
    }
}
