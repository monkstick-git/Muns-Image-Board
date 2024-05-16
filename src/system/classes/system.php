<?php

class system
{

    public $cache = array("null" => "null");

    public function __construct()
    {
    }

    public function redirect($location)
    {
        header("Location: $location");
    }

    public function beAuthenticated()
    {
        if (!isset($_SESSION['User'])) {
            $this->redirect('/User/login');
        }
    }

    public function beAdmin()
    {
        if (!isset($_SESSION['User'])) {
            $this->redirect('/User/login');
        }
        if($GLOBALS['User']->is_admin() == false){
            # return not authorized header
            header('HTTP/1.0 403 Forbidden');
            return false;
        }else{
            return true;
        }
    }

    public function cache($input, $data, $ttl = 60)
    {
        global $redis;
        $id = base64_encode($input);
        if (null !== ($redis->get($id))) {
        } else {
            mlog("Adding to cache: $input");
            $redis->set($id, $data, 'EX', $ttl);
        }
    }

    public function cache_get($query)
    {
        global $redis;
        $id = base64_encode($query);
        if (null !== ($redis->get($id))) {
            $cached_Obj = $redis->get($id);
            mlog("Returning from cache: $id");
            #mlog(print_r($cached_Obj, true));
            return $cached_Obj;
        } else {
        }
    }

    public function checkCache($input)
    {
        if (isset($this->cache[base64_encode($input)])) {
            #print_r($this->cache[$input]);
            return $this->cache[base64_encode($input)];
        } else {
            return false;
        }
    }
}
