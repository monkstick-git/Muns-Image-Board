<?php

class ControllerApiv1 extends Controller
{

    public $Response = [];
    private $apiv1Helper;
    public function __construct()
    {
        parent::__construct(DisableRender: true); // Disable the render object for API controllers (true)
        include_once(ROOT . '/Controllers/Helpers/apiv1Helper.php');
        $this->apiv1Helper = new apiv1Helper();
        $this->apiv1Helper->checkApiToken($this->ArgumentList); // Check if the user is authenticated early
    }

    /**
     * Index
     * 
     * This function is the default function for the API controller.
     * It should tell the user that the API is working and what endpoints are available.
     */
    public function Index()
    {
        // Public API, No authentication required       
        $this->Response['Endpoints'] = [
            'GET /Files' => 'Get all files for the authenticated user',
            'GET /files?id={id}' => 'Get a file by ID for the authenticated user', # Todo
            'POST /Upload' => 'Upload a file',
            'DELETE /files?id={id}' => 'Delete a file by ID' # TODO
        ];
    }

    public function Files()
    {
        // Private API, authentication required
        if ($this->apiv1Helper->User) {
            $Limit = (int) $this->ArgumentList['limit'] ?? 100;

            $file = new file();
            $files = $file->Find(userID: $this->apiv1Helper->User->id, limit: $Limit);
            $this->Response['Files'] = $files;
        }
    }


    public function Upload()
    {
        // Private API, authentication required
        if ($this->apiv1Helper->User) {
            $GLOBALS['User'] = $this->apiv1Helper->User; // Set the global user object to the authenticated user - This is used in the file class to set the userID
            global $settings; // Get the settings array from the global scope 
            $Data = $_FILES['data'] ?? null;

            $quotaCheck = $this->apiv1Helper->checkRemainingQuota($Data); // Check if the user has enough quota to upload the file

            if ($quotaCheck == false) {
                $this->Response['status'] = 507;
                $this->Response['message'] = QUOTA_ERROR;
                return;
            }

            $Upload = $this->apiv1Helper->UploadFile($Data);
            if ($Upload == false) {
                $this->Response['status'] = 500;
                $this->Response['message'] = ERROR_GENERIC;
            } else {
                // If the file was uploaded successfully, return a 200 status code
                $this->Response['status'] = 200;
                $this->Response['message'] = SUCCESS;
                $this->Response['url'] = $Upload;
            }
        } else {
            $this->Response['status'] = 401;
            $this->Response['message'] = ERROR_UNAUTHORIZED;
        }
    }

    public function __destruct()
    {

        # set the response header to application/json
        header('Content-Type: application/json');
        # Set the response code to $this->Response['status']
        http_response_code($this->Response['status']);
        # Output the response as JSON
        echo json_encode($this->Response);

        return;
    }

}