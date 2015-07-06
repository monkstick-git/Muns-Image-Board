<?php 
	$uploadedFile = $_FILES["data"];
		$tmpname = "tmp/" . str_replace(".","",microtime(true)) . "." . pathinfo($_FILES["data"]["name"])['extension'];
		
	if(in_array(pathinfo(strtolower($tmpname))['extension'],array("php","html","htm","ade", "adp", "bat", "chm", "cmd", "com", "cpl", "exe", "hta", "ins", "isp", "jar", "jse", "lib", "lnk", "mde", "msc", "msp", "mst", "pif", "scr", "sct"," shb", "sys", "vb", "vbe", "vbs", "vxd", "wsc", "wsf", "wsh")) )
	{
		echo "Illegal File type,  Sorry!";
	}
	else
	{
		move_uploaded_file($_FILES["data"]["tmp_name"],$tmpname);
		file_put_contents("images/" . str_replace(".","",microtime(true)) . "." . pathinfo($tmpname)['extension'], file_get_contents($tmpname));
		
		if(in_array(pathinfo(strtolower($tmpname))['extension'],array("webm","mp4","mp3","flv","aac","ogg","oga")))
		{
			echo "http://saltyservers.co.uk/munage-board/video.php?vid=" . $tmpname;
		}else{
			echo "http://saltyservers.co.uk/munage-board/" . $tmpname;	
		}

	}
	
	

	
	
?>



