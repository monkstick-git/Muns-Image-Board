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
        
        return $this->save(url: $LongURL, short: $ShortURL, userID: $userID);
        
    }

    private function save($url, $short, $userID) {
        $url = Registry::get('SqlSlaves')->safe($url);
        $short = Registry::get('SqlSlaves')->safe($short);
        $userID = Registry::get('SqlSlaves')->safe($userID);

        $sql = "INSERT INTO urls (long_url, short_url, user_id) VALUES ('$url', '$short', '$userID')";
        return Registry::get('Sql')->insert($sql);
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

    public function getOwner($LinkID){
        $LinkID = $LinkID ?? false;
        if(!$LinkID){
            return false;
        }
        $LinkID = Registry::get('SqlSlaves')->safe($LinkID);
        $sql = "SELECT user_id FROM urls WHERE id = '$LinkID'";
        $result = Registry::get('SqlSlaves')->query($sql);
        if($result){
            return $result[0]['user_id'];
        }else{
            return false;
        }
    }

    public function delete($uid, $LinkID){
        $uid = $uid ?? false;
        $LinkID = $LinkID ?? false;
        if(!$uid || !$LinkID){
            return false;
        }
        $uid = Registry::get('SqlSlaves')->safe($uid);
        $LinkID = Registry::get('SqlSlaves')->safe($LinkID);
        
        # Delete the URL
        $sql = "DELETE FROM urls WHERE id = '$LinkID' AND user_id = '$uid'";
        if(Registry::get('Sql')->insert($sql)){
            return true;
        }else{
            return false;
        }
    }

}