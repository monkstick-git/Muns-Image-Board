<?php
$FileArray = $arguments['FileArray'];
$adminMenu = $arguments['adminMenu'];
ob_start();
?>

<div class="container-fluid album py-5 bg-light">
    <div class="row justify-content-center">
        <!-- Update row-cols-md-3 to row-cols-md-6 for 6 images per row on medium screens and larger -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-6 g-3">
            <?php foreach ($FileArray as $key => $value): ?>
                <?php
                $fileValue = $value;
                $fileID = $fileValue['hash'] . "_" . $fileValue['id'];
                if ($adminMenu):
                    $fileOwner = $fileValue['owner'];
                    $tmpUser = new user(); # TODO: This should be passed to the view, not created here
                    $tmpUser->get_user_by_id($fileOwner);
                    $ownerName = $tmpUser->username;
                endif;
                $fileType = explode("/", ($fileValue['filetype']))[1];
                $fileContentType = explode("/", ($fileValue['filetype']))[0]; # Image, Video etc

                $thumbnailfileURL = "/image/thumbnail?id=$fileID.$fileType";
                $fileURL = "/image/raw?id=$fileID.$fileType";
                $file = new image(); # TODO: This should be passed to the view, not created here
                $file->getFileMetadata($fileID);
                $fileName = $file->FileName;
                $modified = $file->Modified;
                if ($adminMenu):
                    $adminString = "<div>$ownerName</div>";
                else:
                    $adminString = "";
                endif;
                ?>

                <?php
                // If the file is an image, display it
                if ($fileContentType == "image"):
                    insertImageCard($fileURL, $thumbnailfileURL, $fileName, $fileID, $modified);
                endif;

                // If the file is a video, display a video card
                if ($fileContentType == "video"):
                    insertVideoCard($fileURL, $fileName, $fileID, $modified);
                endif;
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
