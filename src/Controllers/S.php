<?php

# This is the controller for the shortening service. It handles the creation of new short URLs and the redirection of existing ones.
class ControllerS extends Controller
{

    private $shortHelper;

    public function __construct($DisableRender = false)
    {
        parent::__construct($DisableRender);
        include_once(ROOT . '/Controllers/Helpers/shortHelper.php');
        $this->shortHelper = new shortHelper();
    }

    function Index()
    {
        $ShortURL = $this->ArgumentList['s'];
        $LongURL = $this->shortHelper->get(ShortURL: $ShortURL);

        if ($LongURL) {
            $URL = $LongURL['long_url'];
            $ShortURL = $LongURL['short_url'];
            $URLOwner = $LongURL['user_id'];
            $DateCreated = $LongURL['created'];
            Registry::get('system')->redirect($URL);
        } else {
            $Arguments = array('errors' => 'Short URL not found');
            Registry::get('render')->render_template('Errors/404', $Arguments);
        }
    }

    function New()
    {
        Registry::get('system')->beAuthenticated();
        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Site/Short/index');
    }

    function Create()
    {
        # Check if the user is logged in
        Registry::get('system')->beAuthenticated();

        $LongURL = $this->shortHelper->sanitizeURL(URL: $this->ArgumentList['longUrl']);
        $ShortURL = $this->shortHelper->generateShortURL(URL: $this->ArgumentList['longUrl']);

        $Success = $this->shortHelper->store(LongURL: $LongURL, ShortURL: $ShortURL);
        if($Success){
            $Arguments = array('shortURL' => $ShortURL);
            Registry::get('render')->render_template('Site/Short/index', $Arguments);
        }else{
            $Arguments = array('errors' => 'Failed to create short URL');
            Registry::get('render')->render_template('Errors/404', $Arguments);
        }
    }

    function Get()
    {
        $ShortURL = $this->ArgumentList['shortURL'];
        $LongURL = $this->shortHelper->get(ShortURL: $ShortURL);

        if ($LongURL) {
            $URL = $LongURL['long_url'];
            $ShortURL = $LongURL['short_url'];
            $URLOwner = $LongURL['user_id'];
            $DateCreated = $LongURL['created'];
            Registry::get('system')->redirect($URL);
        } else {
            $Arguments = array('errors' => 'Short URL not found');
            Registry::get('render')->render_template('Errors/404', $Arguments);
        }
    }

    function List(){
        Registry::get('system')->beAuthenticated();
        Registry::get('render')->render_template('Core/navbar');

        $UserID = Registry::get('User')->id;
        $ShortURLs = $this->shortHelper->list(uid: $UserID);
        $Arguments = array('shortURLs' => $ShortURLs);
        Registry::get('render')->render_template('Site/Short/list', $Arguments);
    }

    function Delete(){
        Registry::get('system')->beAuthenticated();
        $LinkID = $this->ArgumentList['id'];
        $UserID = Registry::get('User')->id;
        # Check if the owner of the short URL is the same as the logged-in user
        $Owner = $this->shortHelper->getOwner(LinkID: $LinkID);
        if($Owner != $UserID){
            $Arguments = array('errors' => 'You do not have permission to delete this short URL');
            Registry::get('render')->render_template('Errors/404', $Arguments);
        }else{
            return $this->shortHelper->delete(uid: $UserID, LinkID: $LinkID);
        }
        #$this->shortHelper->delete(uid: $UserID, LinkID: $LinkID);
    }

}