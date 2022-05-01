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

    // public function cache($input, $data)
    // {
    //     $id = base64_encode($input);
    //     if (isset($_SESSION['cache'][$id])) {
    //     } else {
    //         logger("Adding to cache: $input");
    //         #$this->cache[$id] = $data;
    //         $_SESSION['cache'][$id] = $data;
    //     }
    // }

    // public function cache_get($query)
    // {
    //     $id = base64_encode($query);
    //     if (isset($_SESSION['cache'][$id])) {
    //         $cached_Obj = $_SESSION['cache'][$id];
    //         logger("Returning from cache: $id");
    //         #logger(print_r($cached_Obj, true));
    //         return $cached_Obj;
    //     } else {
    //     }
    // }

    public function cache($input, $data, $ttl = 60)
    {
        global $redis;
        $id = base64_encode($input);
        if (null !== ($redis->get($id))) {
        } else {
            logger("Adding to cache: $input");
            $redis->set($id, $data, 'EX', $ttl);
        }
    }

    public function cache_get($query)
    {
        global $redis;
        $id = base64_encode($query);
        if (null !== ($redis->get($id))) {
            $cached_Obj = $redis->get($id);
            logger("Returning from cache: $id");
            #logger(print_r($cached_Obj, true));
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
