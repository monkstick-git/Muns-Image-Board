<?php

# The URL shortener class is used to create and manage short URLs and store them in the database
class short {

    public function store($LongURL, $ShortURL, $userID) {
        # Check if URL or short URL is empty
        if (empty($LongURL) || empty($ShortURL) || empty($userID)) {
            logger("URL, short URL, or user ID is empty");
            logger("URL: $LongURL");
            logger("Short URL: $ShortURL");
            logger("User ID: $userID");

            return false;
        }

        $ShortURL = Registry::get('SqlSlaves')->safe($ShortURL);
        $LongURL = Registry::get('SqlSlaves')->safe($LongURL);
        $userID = Registry::get('SqlSlaves')->safe($userID);
        $this->save(url: $LongURL, short: $ShortURL, userID: $userID);
        # return logic here
    }

    private function save($url, $short, $userID) {
        $sql = "INSERT INTO urls (long_url, short_url, user_id) VALUES ('$url', '$short', '$userID')";
        if(Registry::get('Sql')->insert($sql)){
            echo "URL saved successfully<br>";
            echo "URL: $url<br>";
            echo "Short URL: $short<br>";
        } else {
            #$Error = Registry::get('Sql')->error;
            echo "Error saving URL<br>";
            #echo "Error: $Error<br>";
        }
    }

    public function get($ShortURL) {
        $ShortURL = Registry::get('SqlSlaves')->safe($ShortURL);
        $sql = "SELECT * FROM urls WHERE short_url = '$ShortURL'";
        $result = Registry::get('SqlSlaves')->query($sql);
        if($result){
            return $result[0];
        }else{
            return false;
        }
        
    }

    public function list($uid){
        $uid = $uid ?? false;
        if(!$uid){
            return false;
        }
        $uid = Registry::get('SqlSlaves')->safe($uid);
        $sql = "SELECT * FROM urls WHERE user_id = '$uid'";
        $result = Registry::get('SqlSlaves')->query($sql);
        if($result){
            return $result;
        }else{
            return false;
        }
    }

}