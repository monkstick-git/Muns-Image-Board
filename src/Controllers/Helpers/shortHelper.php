<?php

class shortHelper extends helper
{

    function sanitizeURL($URL)
    {
        $URL = filter_var($URL, FILTER_SANITIZE_URL);
        return $URL;
    }

    function generateShortURL($URL)
    {
        # Generate a unique short URL

        $ShortURL = substr(md5($URL), 0, 6);
        return $ShortURL;
    }

    function store($LongURL, $ShortURL)
    {
        $short = new short();
        $ID = Registry::get('User')->id;
        return $short->store(LongURL: $LongURL, ShortURL: $ShortURL, userID: $ID);
    }

    function get($ShortURL)
    {
        $short = new short();
        return $short->get(ShortURL: $ShortURL);
    }

    function list($uid){
        $short = new short();
        return $short->list(uid: $uid);
    }

}
