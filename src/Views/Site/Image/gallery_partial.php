<?php
$FileArray = $arguments['FileArray'];
$adminMenu = $arguments['adminMenu'];
ob_start();
?>


<?php foreach ($FileArray as $key => $value): ?>
    <?php
    $fileValue = $value;
    $fileID = $fileValue['hash'] . "_" . $fileValue['id'];
    if ($adminMenu):
        $fileOwner = $fileValue['owner'];
        $tmpUser = new user(); # TODO: Should be passed to the view
        $tmpUser->get_user_by_id($fileOwner);
        $ownerName = $tmpUser->username;
    endif;
    $fileType = explode("/", ($fileValue['filetype']))[1];
    $fileContentType = explode("/", ($fileValue['filetype']))[0]; # Image, Video etc

    $thumbnailfileURL = "/image/thumbnail?id=$fileID.$fileType";
    $fileURL = "/image/raw?id=$fileID.$fileType";
    $file = new image(); # TODO: Should be passed to the view
    $file->getFileMetadata($fileID);
    $fileName = $file->FileName;
    $modified = $file->Modified;
    if ($adminMenu):
        $adminString = "<div>$ownerName</div>";
    else:
        $adminString = "";
    endif;
    ?>

    <div class="col-md-4 d-flex align-items-stretch">
        <div class="card mb-4 shadow-sm" style="width: 100%;">
            <a href="<?= $fileURL ?>">
                <img class="card-img-top lazyload" src="<?= $thumbnailfileURL ?>" data-src="<?= $thumbnailfileURL ?>" alt="Thumbnail" loading="lazy">
            </a>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($fileName) ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?= $modified ?></h6>
                <p class="card-text">Click the buttons below to view details or delete this file.</p>
                <div class="d-flex justify-content-between">
                    <a href="/File/Details?id=<?= $fileID ?>" class="btn btn-primary">Details</a>
                    <a href="/File/Delete?id=<?= $fileID ?>" class="btn btn-danger">❌ Delete ❌</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
