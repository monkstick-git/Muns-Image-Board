<?php include 'bootstrap.php'; ?>

<centre>
    <?php
    $BugReport = false;
    if ($BugReport == true) {
        error_reporting(-1);
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);
    }

    function Video($url, $players)
    {
        #echo "D http://www.saltyservers.co.uk/munage-board/images/" . $url . "<br>";
        echo "<a href='$url'> " . $url . " </a> <br>";
        $started = microtime(true);
        $stream = $url;
        if (!isset($width)) {
            $width = 720;
        };
        echo '<p align="center" >';
        echo "
				<video
				id='my-player'
				class='video-js'
				controls
				preload='auto'
				data-setup='{}'>
			<source src='$stream' type='video/mp4'></source>
		</video>
				";
    }

    ?>

    <body background="bg/paper.jpg">
        <centre>


            <!-- <form action="index.php" method="post">
            Image/Webm Url: <input type="text" name="fname"> 
            <input type="submit" value="Submit">
          </form>
          <br>
          
          <form action="index.php" method="post" enctype="multipart/form-data"> 
            Upload Own: <input type="file" name="uploadedFile" id="uploadedFile">
            <input type="submit" value="Upload File" name="submitfile">
          </form> -->

        </centre>


        <table>
            <?php
            $players = 0;
            $uploaded = 0;
            $filetypes = $settings['allowed_image_formats'];
            $files = glob('uploads/*.{*}', GLOB_BRACE);

            $page = !empty($_GET["page"]) ? $_GET["page"] : $page = 1;
            $files = glob('uploads/*.{*}', GLOB_BRACE);
            $max = 12;
            $filecount = 1;

            function pages($filenum, $page1, $max1)
            {
                global $filecount;
                $filecount = $filecount + 1;
                #echo (1 + ($max1 * ($page1 - 1)))  . "  :  " . $filecount   .  "<br>";
                if ($filenum >= (1 + ($max1 * ($page1 - 1))) and $filenum <= ($max1 * $page1)) {
                    #	echo "<br>";
                    return true;
                } else {
                    return false;
                }
            }

            foreach (array_reverse($files) as $file) {
                if (in_array(pathinfo(strtolower($file))['extension'], array("jpg", "jpeg", "gif", "png")) and pages($filecount, $page, $max) == true) {
                    echo '<tr>';
                    echo '<td>';
                    echo "<a href='" . $settings['site_url'] . $file . "'<br>";
                    echo "<img src='" . $file . "' alt='null' style='max-height:480px; max-width:1280'> <br>";
                    echo '</td>';
                    echo '</tr>';
                } elseif (in_array(pathinfo($file)['extension'], $settings['allowed_video_formats']) and pages($filecount, $page, $max) == true) {
                    echo '<tr>';
                    echo '<td>';
                    $players = $players + 1;
                    Video($file, $players);
                    echo '</td>';
                    echo '</tr>';
                }
            }
            echo "</table>";
            $PageNum = 0;
            $PageLine = 1;
            $tmpnum = 1;
            for ($I = 1; $I <= $filecount; $I++) {
                $tmpnum = $tmpnum + 1;
                if ($tmpnum >= $max) {
                    $PageNum++;
                    $PageLine++;
                    echo '<font size="6"> <a href="' . $settings['site_url'] . '?page=' . $PageNum . '">  ' . $PageNum . ' </a></font>';
                    if ($PageLine > 5) {
                        echo "<br>";
                        $PageLine = 1;
                    }

                    #echo " - " . $PageNum;
                    $tmpnum = 1;
                }
            }
            ?>
</centre>
</body>

<?php $render->render_flush(); ?>
