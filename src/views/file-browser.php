<?php
$FileArray = $arguments['FileArray'];

ob_start();
?>

<div class="container py-5">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <?php foreach ($FileArray as $key => $value):
            $fileValue = $value;
            $fileHash = $fileValue['hash'];
            $fileID = $fileValue['id'];
            $uniqueID = $fileHash . "_" . $fileID;
            $fileOwner = $fileValue['owner'];
            $tmpUser = new user();
            $tmpUser->get_user_by_id($fileOwner);
            $ownerName = $tmpUser->username;
            $fileType = explode("/", ($fileValue['filetype']))[1];
            $fileURL = "/Site/download/$uniqueID.$fileType?r=0";
            $file = new image();
            $file->getFileMetadata($uniqueID);
            $fileName = $file->FileName;
            $modified = $file->Modified;
            $Size = $file->FileSize;
            # Convert Size to human readable
            $Size = $Size / 1024; # KB
            # Round to 2 decimal places
            $Size = round($Size, 2);
            # Add KB to the end
            $Size = $Size . " KB";
            $type = $file->FileType;
            $deleteURL = "/Site/files/delete?id=$uniqueID";
            ?>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $fileName ?></h5>
                        <p class="card-text">
                            <strong>MD5:</strong> <?= $fileHash ?><br>
                            <strong>Uploaded:</strong> <?= $modified ?><br>
                            <strong>Size:</strong> <?= $Size ?><br>
                            <strong>Owner:</strong> <?= $ownerName ?><br>
                            <strong>Type:</strong> <?= $type ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= $fileURL ?>" class="btn btn-primary btn-sm" target="_blank">Download</a>
                        <a href="<?= $deleteURL ?>" class="btn btn-danger btn-sm">❌ Delete ❌</a>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
