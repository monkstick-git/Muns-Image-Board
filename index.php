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
			echo "<a href='http://www.saltyservers.co.uk/munage-board/" . $url . "'> http://www.saltyservers.co.uk/munage-board/" . $url . " </a> <br>";
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



<!-- <form action="index.php" method="post">
  Image/Webm Url: <input type="text" name="fname"> 
  <input type="submit" value="Submit">
</form>
<br>

<form action="index.php" method="post" enctype="multipart/form-data"> 
  Upload Own: <input type="file" name="uploadedFile" id="uploadedFile">
  <input type="submit" value="Upload File" name="submitfile">
</form> -->

</center>


<table>
<?php 
	if(isset($_POST["submitfile"])){
		$uploadedFile = $_POST["submitfile"];
	}

		if($BugReport)
		{
			echo "File: " . $uploadedFile . "<br>";
		}
	$uploaded = 0;
	if(isset($_POST["submitfile"]))
	{

		$check = getimagesize($_FILES["uploadedFile"]["tmp_name"]);
		$tmpname = "tmp/" . str_replace(".","",microtime(true)) . "." . pathinfo($_FILES["uploadedFile"]["name"])['extension'];
		move_uploaded_file($_FILES["uploadedFile"]["tmp_name"],$tmpname);
		$uploaded = 1;
	}
	
	$filetypes = array("jpg","jpeg","gif","png","bmp");
	
	if($uploaded)
	{
			$url = $tmpname;
	}else{

			$url = $_POST["fname"];
			$urlissite = isset($url);
	}
	$players = 0;
	$files = glob('images/*.{*}', GLOB_BRACE);

	$page = $_GET["page"];
	if(!isset($page)){$page=1;} 
	$imgOld = "tmp/" . str_replace(".","",microtime(true)) . "." . pathinfo($url)['extension'];
	
	if(in_array(pathinfo(strtolower($url))['extension'],$filetypes) ){
		file_put_contents($imgOld, file_get_contents($url));
	}
	$Accepted = false;
	if(in_array(pathinfo(strtolower($url))['extension'],array("webm","mp4","mp3","flv","aac","ogg","oga")) ){
		$Accepted = true;
		file_put_contents("images/" . str_replace(".","",microtime(true)) . "." . pathinfo($url)['extension'], file_get_contents($url));
	}	
	
	if(in_array(pathinfo(strtolower($url))['extension'],array("jpg","png","bmp")) ){
		$Accepted = true;
        $image = new Imagick($imgOld);
		$image->setImageFormat( "jpg" );
		$image->setImageCompressionQuality(85); 
		$image->stripImage();
		$img = "images/" . str_replace(".","",microtime(true)) . ".jpg";

		$d = $image->getImageGeometry();
		if($d['width'] > 1920 or $d['height'] > 1080) 
		{
			$image->scaleImage(1920, 1080, true); // => 666x500
		}	
		
		$image->writeImage( $img );	
		unlink($imgOld);
	}
	
	if(in_array(pathinfo(strtolower($url))['extension'],array("gif")) ){
		$Accepted = true;
		file_put_contents("images/" . str_replace(".","",microtime(true)) . ".gif", file_get_contents($url));		
	}	
	
	if($Accepted == false and $urlissite == true)
	{
		file_put_contents("images/" . str_replace(".","",microtime(true)) . ".websiteurl", $url);
	#	$Site = '<iframe width="1280" height="480" src="' . $url . '" frameborder="0" allowfullscreen></iframe>';
	#	echo $Site;
	}
	
	$files = glob('images/*.{*}', GLOB_BRACE);
	$max = 12;
	$filecount = 1;
	
	function pages($filenum,$page1,$max1)
	{
		
		global $filecount;
		$filecount = $filecount + 1;
		#echo (1 + ($max1 * ($page1 - 1)))  . "  :  " . $filecount   .  "<br>";
		if( $filenum >= (1 + ($max1 * ($page1 - 1))) and $filenum <= ($max1 * $page1) )
		{
		#	echo "<br>";
			return true;
		}else
		{
			return false;
		}
	}
	
	foreach(array_reverse($files) as $file) {

		if(in_array(pathinfo(strtolower($file))['extension'],array("jpg","jpeg","gif","png")) and pages($filecount,$page,$max ) == true ){
			echo '<tr>';
			echo '<td>';
			echo "<a href='http://www.saltyservers.co.uk/munage-board/" . $file . "'<br>";
			echo "<img src='" . $file . "' alt='null' style='max-height:480px; max-width:1280'> <br>";
			echo '</td>';
			echo '</tr>';
		}elseif(in_array(pathinfo($file)['extension'],array("webm","mp4","mp3","flv","aac","ogg","oga")) and pages($filecount,$page,$max ) == true ){
			echo '<tr>';
			echo '<td>';
			$players = $players + 1;
			Video($file,$players);
			echo '</td>';
			echo '</tr>';
		}else{
			if(pathinfo($file)['extension'] == "websiteurl" and pages($filecount,$page,$max ) == true)
			{
			echo '<tr>';
			echo '<td>';
			$Site = '<iframe width="1280" height="480" src="' . file_get_contents($file) . '" ></iframe>';
			echo '<a href="' . file_get_contents($file) . '"> ' . file_get_contents($file) . ' </a>';
			echo $Site;
			echo '</td>';
			echo '</tr>';
			}
		}
		

	}
	echo "</table>";
	$PageNum = 0;
	$PageLine = 1;
	$tmpnum = 1;
	for($I=1;$I<= $filecount;$I++)
	{
		$tmpnum = $tmpnum + 1;
		if($tmpnum >= $max){
			$PageNum ++;
			$PageLine++;
			echo '<font size="6"> <a href="http://saltyservers.co.uk/munage-board/?page=' . $PageNum . '">  ' . $PageNum . ' </a></font>';
			if($PageLine > 5) {
				echo "<br>";
				$PageLine = 1;
			}
			
			#echo " - " . $PageNum;
			$tmpnum = 1;
			}
		
	}
?>
</center>