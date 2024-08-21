<?php

/**
 * Application Settings Configuration
 *
 * This file contains configuration settings for the application, 
 * including site URL, allowed file formats, database connections, and more.
 */

// Site URL
$settings['site_url'] = $_ENV['SITE_URL'];

// Allowed File Formats
$settings['allowed_video_formats'] = array("webm", "mp4", "mp3", "flv", "aac", "ogg", "oga");
$settings['allowed_image_formats'] = array("jpg", "jpeg", "gif", "png", "bmp");

// Banned File Formats
$settings['banned_formats'] = array(
    "php", "html", "htm", "ade", "adp", "bat", "chm", "cmd", "com", "cpl", 
    "exe", "hta", "ins", "isp", "jar", "jse", "lib", "lnk", "mde", "msc", 
    "msp", "mst", "pif", "scr", "sct", "shb", "sys", "vb", "vbe", "vbs", 
    "vxd", "wsc", "wsf", "wsh"
);

// Cache Settings
$settings['cache'] = false;

// File Storage Driver
// Options: "mysql" or "local"
$settings['fileDriver'] = "local";

// Database Connection Settings
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
