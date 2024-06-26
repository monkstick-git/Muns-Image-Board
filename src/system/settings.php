<?php

$settings['site_url'] = $_ENV['SITE_URL']; #"http://localhost:8080/";
$settings['allowed_video_formats'] = array("webm", "mp4", "mp3", "flv", "aac", "ogg", "oga");
$settings['allowed_image_formats'] = array("jpg", "jpeg", "gif", "png", "bmp");
$settings['banned_formats'] = array("php", "html", "htm", "ade", "adp", "bat", "chm", "cmd", "com", "cpl", "exe", "hta", "ins", "isp", "jar", "jse", "lib", "lnk", "mde", "msc", "msp", "mst", "pif", "scr", "sct", " shb", "sys", "vb", "vbe", "vbs", "vxd", "wsc", "wsf", "wsh");
$settings['cache'] = false;
$settings['fileDriver'] = "local"; # Options are currently "mysql" or "local"

$settings['databases'] = array(
  'writer' => array(
    'host' => 'mysql',
    'user' => 'munboard',
    'pass' => 'munboard',
    'name' => 'munboard',
    'prefix' => '',
    'charset' => 'utf8',
    'engine' => 'InnoDB'
  ),
  'slaves' => array(
    array(
      'host' => 'mysql',
      'user' => 'munboard',
      'pass' => 'munboard',
      'name' => 'munboard',
      'prefix' => '',
      'charset' => 'utf8',
      'engine' => 'InnoDB'
    )
  )
);
