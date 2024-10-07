<?php

class ControllerFile extends Controller
{

    private $fileHelper;

    public function __construct()
    {
        parent::__construct();
        include_once(ROOT . '/Controllers/Helpers/fileHelper.php');
        $this->fileHelper = new fileHelper();
    }

    /**
     * Display the upload form. Upload handler is in ProcessUpload
     * @return void
     */
    public function Upload()
    {
        $this->system->beAuthenticated();

        $this->render->render_template('Core/navbar');
        $this->render->render_template('Site/Files/upload');
    }

    public function ProcessUpload()
    {
        $this->system->beAuthenticated();

        $UploadedFiles = $_FILES['filesToUpload'];
        $FileUploaded = $this->fileHelper->uploadFile(ArgumentList: $this->ArgumentList, FileList: $UploadedFiles);
    }

    public function Delete()
    {

        $this->system->beAuthenticated();

        # Displays details of a file
        $hash = filter_input(INPUT_GET, 'id');
        $file = new file();
        $file->get($hash);
        if ($_SESSION['user']->id != $file->Owner) {
            mlog("🔴 User is not the owner of the file");
            $this->render->render_template(templateName: 'Core/error', arguments: ['error' => 'You are not the owner of this file']);
            exit();
        } else {
            mlog("✅ User is the owner of the file - attempting to delete");
            if ($file->delete()) {
                mlog("✅ File deleted");
            } else {
                mlog("🔴 File not deleted");
            }

            # Redirect back to where the user came from
            header("Location: $this->PreviousPage");
        }

    }

    public function Download()
    {
        // Load Image or get a false back
        $File = $this->fileHelper->loadFile($this->ArgumentList);

        // Check if the user has permission to download the file
        if (!$this->fileHelper->checkPermissions($File)) {
            $this->render->render_template(templateName: 'Core/error', arguments: ['errors' => FILE_ERROR_PERMISSION]);
            return;
        }

        // Check if the file exists
        if ($File == false) {
            $this->render->render_template(templateName: 'Core/error', arguments: ['errors' => FILE_ERROR_PERMISSION]);
            return;
        }

        // Download the file
        $this->render->render_template(templateName: 'Core/error', arguments: ['errors' => 'It worked']);
        $this->fileHelper->downloadFile(File: $File);



    }

    public function Details()
    {
        $this->system->beAuthenticated();

        # Displays details of a file
        $hash = filter_input(INPUT_GET, 'id');
        $file = new file();
        $file->get($hash);
        if ($_SESSION['user']->id != $file->Owner) {
            mlog("🔴 User is not the owner of the file");
            $this->render->render_template(templateName: 'Core/error', arguments: ['error' => 'You are not the owner of this file']);
            exit();
        } else {
            // All checks passed, show image details
            $this->render->render_template('Site/Image/details', array(
                'file' => $file
              )
              );            
        }
    }
}