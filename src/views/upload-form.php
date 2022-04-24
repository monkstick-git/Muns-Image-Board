<?php

$template = '
<div class="container">
<div class="row justify-content-start">
<div class="col-12">


<form action="/Site/Upload" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>

</div>
</div>
</div>
';
