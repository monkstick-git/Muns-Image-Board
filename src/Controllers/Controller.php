<?php

/*
 * This is the base controller class. All other "real" controllers should extend this class.
 * This class provides the render object, the system object, the requestType property, and the ArgumentList property.
 * The render object is used to render templates. The system object is used to access the system object.
 * The requestType property is used to store the request type (GET, POST, etc.).
 * The ArgumentList property is used to store all the $_POST and $_GET variables.
*/
class Controller
{
    public $requestType;
    public $ArgumentList;
    public $PreviousPage;

    public function __construct($DisableRender = false)
    {
        include_once(ROOT . '/Controllers/Helpers/helper.php');

        // If the DisableRender flag is set to false, create a new render object. 
        // It should be set to false when the controller is being used as an API controller.
        if($DisableRender == false){
            Registry::set('render', new Render());
        }else{
            logger("🔴🔴🔴 RENDERER DISABLED 🔴🔴🔴");
        }
        
        $this->requestType = $_SERVER['REQUEST_METHOD'];
        $this->populateArgumentList();

        $this->PreviousPage = $_SERVER['HTTP_REFERER'] ?? '/';     

    }

    // This function gets all the $_POST and $_GET variables and stores them in the ArgumentList property.
    public function populateArgumentList()
    {
        $this->ArgumentList = array_merge($_POST, $_GET);
    }
}