<?php
require_once '../system/bootstrap.php';
$render->render_template('navbar');
$system->beAuthenticated();
$render->render_template('User/edit');
