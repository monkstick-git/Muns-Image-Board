<?php
$FileArray = $arguments['FileArray'];
$adminMenu = $arguments['adminMenu'];
ob_start();
?>

<div class="container album py-5 bg-light">
  <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
      <?php foreach ($FileArray as $key => $value): ?>
        <?php
        $fileValue = $value;
        $fileID = $fileValue['hash'] . "_" . $fileValue['id'];
        if ($adminMenu):
          $fileOwner = $fileValue['owner'];
          $tmpUser = new user();
          $tmpUser->get_user_by_id($fileOwner);
          $ownerName = $tmpUser->username;
        endif;
        $fileType = explode("/", ($fileValue['filetype']))[1];
        $fileContentType = explode("/", ($fileValue['filetype']))[0]; # Image, Video etc

        $thumbnailfileURL = "/images/thumbnail/$fileID.$fileType";
        $fileURL = "/images/raw/$fileID.$fileType";
        $file = new image();
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
<script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script>
<script>
  $('.lazyload').lazyload();
</script>

<?php
$template = ob_get_contents();
ob_end_clean();
?>