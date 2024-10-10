<?php
ob_start();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Upload and share Images">
  <meta name="author" content="Kieron">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/Images/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/Images/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/Images/favicons/favicon-16x16.png">
  <link rel="manifest" href="/assets/Images/favicons/site.webmanifest">

  <?php
    # Loop through $cssIncludes and include each CSS file.
    foreach (Registry::get('cssIncludes') as $css) {
        echo "<link href=\"$css\" rel=\"stylesheet\">\n";
    }
  ?>



  <title>MunBoard</title>
</head>

<body>


<?php
$template = ob_get_contents();
ob_end_clean();
?>
