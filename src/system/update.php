<?php
include 'bootstrap.php';
global $system;
if(false == $system->beAdmin()){
    echo "Not authorized";
    exit;
}else{
    $Updates = glob(ROOT . '/system/updates/*.php');
    # Sort $Updates by name - Preserving natural numbers
    natsort($Updates);

    foreach ($Updates as $update) {
        require_once $update;
    }
}
