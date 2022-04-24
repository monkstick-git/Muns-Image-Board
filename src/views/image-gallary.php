<?php
$FileArray = $arguments['FileArray'];
$template = '
<div class="album py-5 bg-light">
  <div class="container">
    <div class="row">
';

foreach ($FileArray as $fileKey => $fileValue) {
  $fileID = $fileValue['id'];
  $fileURL = "/Site/View?id=$fileID";
  $file = new file();
  $file->get($fileID);
  $fileName = $file->FileName;
  $modified = $file->modified;
  $template .= "
<div class='col-md-4'>
<div class='card mb-4 box-shadow'>
  <a href='$fileURL'><img class='card-img-top lazyload' data-src='$fileURL' alt='Thumbnail' style='width: 100%; display: block;' src='https://cdn.dribbble.com/users/3251/screenshots/470914/aah.gif' data-holder-rendered='true' lazyload='on'></a>
  <div class='card-body'>
    <p class='card-text'>$fileName</p>
    <div class='d-flex justify-content-between align-items-center'>
      <div class='btn-group'>
        <button type='button' class='btn btn-sm btn-outline-secondary'>View</button>
        <button type='button' class='btn btn-sm btn-outline-secondary'>Delete</button>
      </div>
      <small class='text-muted'>$modified</small>
    </div>
  </div>
</div>
</div>
";
}


$template .= "
</div>
</div>
</div>
</div>
</div>
</div>
<script src='https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js'></script>
<script>
$('.lazyload').lazyload();
</script>
";
