<?php
$FileArray = $arguments['FileArray'];

ob_start();
?>

<table class="table">
  <thead>
    <tr>
      <th scope="col">md5</th>
      <th scope="col">FileName</th>
      <th scope="col">Uploaded</th>
      <th scope="col">Size</th>
      <th scope="col">Owner</th>
      <th scope="col">Type</th>
      <th scope="col">Link</th>
    </tr>
  </thead>
  <tbody>

<?php foreach ($FileArray as $key => $value): 

  $fileValue = $value;
  $fileID = $fileValue['hash'];
  $fileOwner = $fileValue['owner'];
  $tmpUser = new user();
  $tmpUser->get_user_by_id($fileOwner);
  $ownerName = $tmpUser->username;
  $fileType = explode("/", ($fileValue['filetype']))[1];
  $thumbnailfileURL = "/images/thumbnail/$fileID.$fileType";
  $fileURL = "/images/raw/$fileID.$fileType";
  $file = new image();
  $file->getFileMetadata($fileID);
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
  #$Owner = $file->Owner;
  ?>

      <tr>
      <th scope="row"><?=$fileID?></th>
      <td><?=$fileName?></td>
      <td><?=$modified?></td>
      <td><?=$Size?></td>
      <td><?=$ownerName?></td>
      <td><?=$type?></td>
      <td><a href="<?=$fileURL?>" target="_blank">Download</a></td>
<?php endforeach; ?>


</tbody>
</table>

<?php
$template = ob_get_contents();
ob_end_clean();
?>