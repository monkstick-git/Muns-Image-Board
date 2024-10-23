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

        $this->shortHelper->store(LongURL: $LongURL, ShortURL: $ShortURL);
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

}