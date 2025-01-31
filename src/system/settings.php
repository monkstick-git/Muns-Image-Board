<?php

/**
 * Application Settings Configuration
 *
 * This file contains configuration settings for the application, 
 * including site URL, allowed file formats, database connections, and more.
 */

// Site URL
$settings['site_url'] = $_ENV['SITE_URL'];
$settings['SiteName'] = "Muns Image Board";

// Cache Settings
$settings['cache'] = true; // Only set to false for debugging purposes. Huge improvement in performance if set to true.

// File Storage Driver
// Options: "mysql" or "local"
$settings['fileDriver'] = "mysql";

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
