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
  
</head>

<center>
<?php
function Video($url,$players)
{
			echo "Direct Link: http://www.saltyservers.co.uk/munage-board/" . $url . "<br>";
			$started = microtime(true);
			$stream = $url;
			if(!isset($width)){$width= 720 ;};


			echo '<p align="center">';
			echo '<div id="Player-' . $players . '" >; Loading the player...</div>';	
			echo '<script type="text/javascript">';
			echo 'jwplayer("Player-' . $players . '").setup({';
			echo 'height: 480,';
			echo 'width: '.$width * 1.77 . ',';
			#echo 'image: "/1/blueorificelogo.jpg",';
			echo 'file: "' . $stream . '",';
			echo '});';
			echo '</script>';	
}

?>

<form action="get.php" method="post">
  Input: <input type="text" name="fname"><br>
  <input type="submit" value="Submit">
</form>

<?php 
	$filetypes = array("jpg","gif","png","bmp","webm","mp4");
	$players = 0;
	$files = glob('*.{*}', GLOB_BRACE);
	$url = $_POST["fname"];
	$img = str_replace(".","",microtime(true)) . "." . pathinfo($url)['extension'];
	
	if(in_array(pathinfo(strtolower($url))['extension'],$filetypes) ){
		file_put_contents($img, file_get_contents($url));
	}
	
	$files = glob('*.{*}', GLOB_BRACE);
	$max = 3;
	$filecount = 0;
	$page = 3;
	
	foreach(array_reverse($files) as $file) {
		$filecount = $filecount + 1;
		if($filecount > ($max * $page) and $filecount < ($max + ($max * $page)) )
		{
			continue;
		}else{break;}
		echo "<br>";
		if(in_array(pathinfo($file)['extension'],array("jpg","JPG","gif","png")) ){
			echo "Direct Link: http://www.saltyservers.co.uk/munage-board/" . $file . "<br>";
			echo "<img src='" . $file . "' alt='null'> <br>";
		}elseif(in_array(pathinfo($file)['extension'],array("webm","mp4")) ){
			$players = $players + 1;
			Video($file,$players);
		}else{
		
		}
		

	}

?>
</center>