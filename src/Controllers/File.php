<?php

class ControllerFile extends Controller
{

    private $fileHelper;

    public function __construct($DisableRender = false)
    {
        parent::__construct($DisableRender);
        include_once(ROOT . '/Controllers/Helpers/fileHelper.php');
        $this->fileHelper = new fileHelper();
    }

    /**
     * Display the upload form. Upload handler is in ProcessUpload
     * @return void
     */
    public function Upload()
    {
        Registry::get('system')->beAuthenticated();
        Registry::get('render')->render_template('Core/navbar');
        Registry::get('render')->render_template('Site/Files/upload');
    }

    public function ProcessUpload()
    {
        Registry::get('system')->beAuthenticated();

        $UploadedFiles = $_FILES['filesToUpload'];
        $FileUploaded = $this->fileHelper->uploadFile(ArgumentList: $this->ArgumentList, FileList: $UploadedFiles);
    }

    public function Delete()
    {

        Registry::get('system')->beAuthenticated();

        # Displays details of a file
        $hash = filter_input(INPUT_GET, 'id');
        $file = new file();
        $file->get($hash);
        if (Registry::get('User')->id != $file->Owner) {
            mlog("🔴 User is not the owner of the file");
            Registry::get('render')->render_template(templateName: 'Core/error', arguments: ['error' => 'You are not the owner of this file']);
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
            Registry::get('render')->render_template(templateName: 'Core/error', arguments: ['errors' => FILE_ERROR_PERMISSION]);
            return;
        }

        // Check if the file exists
        if ($File == false) {
            Registry::get('render')->render_template(templateName: 'Core/error', arguments: ['errors' => FILE_ERROR_PERMISSION]);
            return;
        }

        $this->fileHelper->downloadFile(File: $File);
        return;
    }

    public function Details()
    {
        Registry::get('system')->beAuthenticated();

        # Displays details of a file
        $hash = filter_input(INPUT_GET, 'id');
        $file = new file();
        $file->get($hash);
        if (Registry::get('User')->id != $file->Owner) {
            mlog("🔴 User is not the owner of the file");
            Registry::get('render')->render_template(templateName: 'Core/error', arguments: ['error' => 'You are not the owner of this file']);
            exit();
        } else {
            // All checks passed, show image details
            Registry::get('render')->render_template(
                'Site/Image/details',
                array(
                    'file' => $file
                )
            );
        }
    }

    public function View()
    {
        // Load Image or fail
        $File = $this->fileHelper->loadFile($this->ArgumentList);
        if ($File == false) {
            Registry::get('system')->Redirect('/'); // Redirect to home if the file is not found
            return;
        }

        // Permission check (always allow for now)
        if (!$this->fileHelper->checkPermissions($File)) {
            Registry::get('system')->Redirect('/'); // Redirect to home if the file is not found
            return;
        }

        // Display the image if permission is granted and the image is loaded
        $this->fileHelper->displayFile($File);
    }    
}