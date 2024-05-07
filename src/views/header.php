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
  <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">

  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- Bootstrap core CSS -->
  <link href="/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <!-- Custom styles for this template -->
  <link href="//vjs.zencdn.net/7.10.2/video-js.min.css" rel="stylesheet">
  <script src="//vjs.zencdn.net/7.10.2/video.min.js"></script>
  <style type="text/css">
    table {
      border: 2px #2b2b2b solid;
      width: 1280px;
      height: 10%;
      max-height: 640px;
      min-height: 640px;
      background-color: #fbfbfb;
    }

    td,
    th {
      border: 2px #2b2b2b solid;
      text-align: center;
    }
  </style>
  <title>MunBoard</title>
</head>
<body>

<?php
$template=ob_get_contents();
ob_end_clean();
