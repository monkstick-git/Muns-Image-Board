<?php

class system {
    public function __construct(){

    }

    public function redirect($location){
        header("Location: $location");
    }

    public function beAuthenticated(){
        if(!isset($_SESSION['User'])){
            $this->redirect('/User/login');
        }
    }

}
