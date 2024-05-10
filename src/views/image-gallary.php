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
        $fileID = $fileValue['hash'];
        if ($adminMenu):
          $fileOwner = $fileValue['owner'];
          $tmpUser = new user();
          $tmpUser->get_user_by_id($fileOwner);
          $ownerName = $tmpUser->username;
        endif;
        $fileType = explode("/", ($fileValue['filetype']))[1];
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
        <div class='col-md-4'>
          <div class='card mb-4 box-shadow'>
            <a href='<?php echo $fileURL; ?>'><img class='card-img-top lazyload '
                data-src='<?php echo $thumbnailfileURL; ?>' alt='Thumbnail' style='height: 100%; '
                src='https://cdn.dribbble.com/users/3251/screenshots/470914/aah.gif' data-holder-rendered='true'
                lazyload='on'></a>
            <div class='card-body'>
              <p class='card-text'><?php echo $fileName; ?></p>
              <div class='d-flex justify-content-between align-items-center'>
                <div class='btn-group'>
                  <a type='button' class='btn btn-sm btn-outline-secondary'
                    href='/Site/files/details?id=<?php echo $fileID; ?>'>Details</a>
                  <button type='button' class='btn btn-sm btn-outline-secondary'>Delete</button>
                </div>
                <small class='text-muted'><?php echo $modified; ?></small>
                <?php echo $adminString; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<script src='https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js'></script>
<script>
  $('.lazyload').lazyload();
</script>

<?php
$template = ob_get_contents();
ob_end_clean();
?>