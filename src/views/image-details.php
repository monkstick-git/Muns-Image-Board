<?php
$image = ($arguments['image']);
#print_r($image);
#print_r($arguments['FileHash']);

$type = $image->FileType;
$shortType = explode("/", $type)[1];
$FileHash = $image->FileHash;
$fileName = $image->FileName;
$modified = $image->Modified;

# Display User Profile
ob_start();
?>
<div class="container-fluid ">
  <row>
    <div class='col-md-4'>
      <div class='card mb-4 box-shadow'>
        <a href='/images/raw/<?= $FileHash ?>.<?= $shortType ?>'><img class='card-img-top lazyload '
            data-src='/images/raw/<?= $FileHash ?>.<?= $shortType ?>' alt='Thumbnail' style='width: 100%; '
            src='https://cdn.dribbble.com/users/3251/screenshots/470914/aah.gif' data-holder-rendered='true'
            lazyload='on'></a>
        <div class='card-body'>
          <p class='card-text'><?= $fileName ?></p>
          <div class='d-flex justify-content-between align-items-center'>
            <div class='btn-group'>
              <a type='button' class='btn btn-sm btn-outline-secondary'
                href='/images/raw/<?= $FileHash ?>.<?= $shortType ?>'>Details</a>
              <button type='button' class='btn btn-sm btn-outline-secondary'>Delete</button>
            </div>
            <small class='text-muted'><?= $modified ?></small>
          </div>
        </div>
      </div>
    </div>
  </row>
</div>
<script src='https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js'></script>
<script>
  $('.lazyload').lazyload();
</script>
<?php
$template = ob_get_contents();
ob_end_clean();
