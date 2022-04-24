<?php

class system {
    public function __construct(){

    }

    public function redirect($location){
        header("Location: $location");
    }

}
