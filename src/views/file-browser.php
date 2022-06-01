<?php
$FileArray = $arguments['FileArray'];

$template = '
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
';

foreach ($FileArray as $key => $value) {
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
    $file->loadMinimal($fileID);
    $fileName = $file->FileName;
    $modified = $file->modified;
    $Size = $file->FileSize;
    $type = $file->fileType;
    #$Owner = $file->Owner;
    $template .= "<tr>
      <th scope=\"row\">$fileID</th>
      <td>$fileName</td>
      <td>$modified</td>
      <td>$Size</td>
      <td>$ownerName</td>
      <td>$type</td>
      <td><a href=\"$fileURL\" target=\"_blank\">Download</a></td>";
}

#<tr>
#<th scope="row">1</th>
#<td>Mark</td>
#<td>Otto</td>
#<td>@mdo</td>
#</tr>
#<tr>
#<th scope="row">2</th>
#<td>Jacob</td>
#<td>Thornton</td>
#<td>@fat</td>
#</tr>
#<tr>
#<th scope="row">3</th>
#<td>Larry</td>
#<td>the Bird</td>
#<td>@twitter</td>
#</tr>

$template .= '
</tbody>
</table>
';
