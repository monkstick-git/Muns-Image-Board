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
  
  <!-- Use only one Bootstrap CSS file -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
  <link href="//vjs.zencdn.net/7.10.2/video-js.min.css" rel="stylesheet">
  
  <style>
    /* Consolidated CSS */
    table {
      border: 2px #2b2b2b solid;
      width: 1280px;
      height: 10%;
      max-height: 640px;
      min-height: 640px;
      background-color: #fbfbfb;
    }

    td, th {
      border: 2px #2b2b2b solid;
      text-align: center;
    }

    .card {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
    }

    .card-img-top {
      width: 100%;
      height: 200px;
      object-fit: cover;
      object-position: center;
    }

    .col-md-4 {
      margin-bottom: 20px;
    }

    .btn-group {
      width: 100%;
      justify-content: center;
    }

    @media (min-width: 576px) {
      .btn-group {
        justify-content: flex-start;
      }
    }

    .text-muted {
      width: 100%;
      text-align: center;
    }

    @media (min-width: 576px) {
      .text-muted {
        text-align: left;
      }
    }
  </style>

  <title>MunBoard</title>
</head>

<body>

  <!-- Bootstrap and other scripts loaded deferred -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/@popperjs/core@2" defer></script>
  <script src="/vendor/twbs/bootstrap/dist/js/bootstrap.min.js" defer></script>
  <script src="//vjs.zencdn.net/7.10.2/video.min.js" defer></script>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
