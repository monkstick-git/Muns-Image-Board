<?php

class fileHelper extends helper
{

    public function uploadFile($ArgumentList, $FileList)
    {

        $Privacy = $ArgumentList['public'] ?? false; // Default to false - which is private
        filter_var($Privacy, FILTER_VALIDATE_BOOLEAN);
        $Privacy = $Privacy ? 1 : 0; // Convert to int
        $Privacy = filter_var($Privacy, FILTER_VALIDATE_BOOLEAN);
        $Success = false;

        $Files = reArrayFiles(file_post: $FileList); # Change the array so each file is ordered
        foreach ($Files as $FileChunk) {
            $file = new file();
            $file->PublicFile = $Privacy;
            $file->setFromUpload($FileChunk);
            $fileType = explode("/", $file->FileType)[0];

            if ($fileType == "image") {
                unset($file);
                $image = new image();
                $image->PublicFile = $Privacy;
                $image->setFromUpload($FileChunk);
                $image->thumbnail = $image->CreateImageThumbNail();
                $image->set();
            } else {
                $file->set();
            }
        }

        return true;
    }

    public function downloadFile($File)
    {
        // All permissions and file validation should be done before calling this function
        $content = $File->Content;
        header("Content-Type: $File->FileType");
        header("Content-Disposition: attachment; filename=\"$File->FileName\"");
        ob_clean();
        echo $content;
        ob_flush();
        return;

    }

    public function loadFile($ArgumentList)
    {

        // Example: https://img.foo.wales/File/Download?id=85fddbf87add077ea541281b4cd4c207_485.jpg
        // id = 85fddbf87add077ea541281b4cd4c207_485.jpg

        $id = $ArgumentList['id'];
        $id = explode(".", $id)[0]; // Get the left hand side of the filename.type (e.g: 10.jpg -> 10) as the ID
        $file = new file();
        $file->get($id);
        # Check if there is a fileID set
        if ($file->FileID == null) {
            return false;
        } else {
            return $file ?? false;
        }
    }

    public function checkPermissions($File)
    {
        if ($File->PublicFile == 0) { // If the file is private (1 = public)
            if (Registry::get('User')->id != $File->Owner) { // As of now, only the owner can download the file if it is private
                mlog("🔴 User is not the owner of the file");
                return false;
            } else {
                mlog("✅ User is the owner of the file - attempting to download");
                return true;
            }
        } else {
            mlog("✅ File is public - attempting to download");
            return true;
        }
    }

    public function displayFile($File){
        header("Content-Type: $File->FileType");
        # set header to real filename. Use "attachment" to force download instead of view
        header("Content-Disposition: inline; filename=\"$File->FileName\"");

        echo $File->Content;
    }

}