<?php

class csrf
{

    public $Token;

    public function __construct()
    {
        $this->csrf_token();
    }

    private function csrf_token()
    {
        # Check if a token is already set and if not set a new one
        if (!isset($this->Token)) {
            $this->Token = $this->generate();
        }else{
            $this->Token = $_SESSION['csrf_token'];
        }
    }

    public function generate()
    {
        $Token = bin2hex(random_bytes(32));
        return $Token;
    }

    public function validate($Token)
    {
        if ($Token == $this->Token) {
            return true;
        } else {
            return false;
        }
    }

}

