<head>
<script type="text/javascript" src="/jwplayer/jwplayer.js"></script>
<meta charset=utf-8 />
<!--
Created using JS Bin
http://jsbin.com

Copyright (c) 2015 by mmcc (http://jsbin.com/cayake/2/edit)

Released under the MIT license: http://jsbin.mit-license.org
-->
<meta name="robots" content="noindex">
<title>Video.js RTMP Example Embed</title>
  
  <link href="http://vjs.zencdn.net/4.11/video-js.css" rel="stylesheet">
  <script src="http://vjs.zencdn.net/4.11/video.js"></script>
  
  
 <style type="text/css">
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

</style>
</head>

<center>
<?php
$BugReport = false;
if($BugReport == true)
{
	error_reporting(-1);
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
}

function Video($url,$players)
{
			#echo "D http://www.saltyservers.co.uk/munage-board/images/" . $url . "<br>";
			echo "<a href='http://www.saltyservers.co.uk/munage-board/images/" . $url . "'> http://www.saltyservers.co.uk/munage-board/images/" . $url . " </a> <br>";
			$started = microtime(true);
			$stream = $url;
			if(!isset($width)){$width= 720 ;};


			echo '<p align="center" >';
			echo '<div id="Player-' . $players . '" >; Loading the player...</div>';	
			echo '<script type="text/javascript">';
			echo 'jwplayer("Player-' . $players . '").setup({';
			echo 'height: 480,';
			echo 'width: 1280,';
			#echo 'width: '.$width * 1.77 . ',';
			#echo 'image: "/1/blueorificelogo.jpg",';
			echo 'file: "' . $stream . '",';
			echo '});';
			echo '</script>';	
}

?>
<body background="bg/paper.jpg">
<center>

<?php

$vid = $_GET["vid"];
echo $vid;
Video($vid,1);
?>