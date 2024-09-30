<?php

/**
 * Logs a message to the PHP error log with a custom prefix.
 *
 * @param string $str The message to log.
 */
function logger($str)
{
    error_log("MunBoard Log: \n" . $str . "\n");
}

/**
 * Reorganizes the $_FILES array structure to be more manageable.
 *
 * This function takes the array structure created by PHP for file uploads
 * (when multiple files are uploaded) and reorganizes it into a more manageable format.
 *
 * @param array $file_post The original $_FILES array.
 * @return array The reorganized array of files.
 */
function reArrayFiles(&$file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

/**
 * Decompresses and decodes a base64-encoded and compressed string.
 *
 * @param string $data The base64-encoded and compressed data.
 * @return string The decompressed data.
 */
function Expand($data)
{
    $data = base64_decode($data);
    $data = gzuncompress($data);
    return $data;
}

/**
 * Compresses and encodes a string using gzcompress and base64 encoding.
 *
 * @param string $data The data to compress and encode.
 * @return string The compressed and base64-encoded string.
 */
function Compress($data, $level = 1, $encoding = "base64")
{
    $data = gzcompress($data, 1);
    $data = base64_encode($data);
    return $data;
}