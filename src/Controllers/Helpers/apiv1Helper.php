<?php

class apiv1Helper extends helper
{

    public $User = null;

    public function checkApiToken($ArgumentList)
    {
        $token = $ArgumentList['api'];
        $user = new user();
        $user->get_user_by_api($token);
        if ($user->id == null) {
            return false;
        } else {
            $this->User = $user; // Set the user property to the user object so it can be accessed in the controller
            return true;
        }
    }

    public function UploadFile($Data)
    {
        // Process the uploaded file
        $file = new file();
        $file->setFromUpload($Data);
        $fileType = explode("/", $file->FileType)[0];
        // Image files
        if ($fileType === "image") {
            unset($file);
            try {
                $image = new image();
                $image->setFromUpload($_FILES['data']);
                $image->thumbnail = $image->CreateImageThumbNail();
                $image->PublicFile = 1; // Assume the image should be public
                $image->set();
                $type = explode("/", $image->FileType)[1];

                return Registry::get('settings')['site_url'] . "image/raw?id=" . $image->FileHash . "_" . $image->FileID . "." . $type;
            } catch (Exception $e) {
                return false;
            }

        } else {
            try {
                $exploded = explode(".", $_FILES['data']['name']);
                $fileTypeGuess = end($exploded); // Guess the file type based on extension
                $file->PublicFile = 1; // Assume the image should be public
                $file->set();
                return Registry::get('settings')['site_url'] . "File/Download?id=" . $file->FileHash . "_" . $file->FileID . "." . $fileTypeGuess;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function checkRemainingQuota($Data)
    {
        # Check how big the file is and check against the users Quota
        $FileSize = strlen(file_get_contents($Data['tmp_name'])); // File size in bytes
        $FileSize = $FileSize / 1024 / 1024; // Convert to MB (We don't need to be too stingy with the quota - few bytes won't hurt)
        $UserQuota = $this->User->quota; // Quota is in MB
        $TotalSpaceUsed = $this->User->getSpaceUsed(); // Space used in bytes (not MB)
        $TotalSpaceUsed = $TotalSpaceUsed / 1024 / 1024; // Convert to MB

        # Calculate if there is enough space left
        $RemainingSpace = $UserQuota - $TotalSpaceUsed;
        if ($RemainingSpace < $FileSize) {
            return false;
        }

        return true;
    }

}