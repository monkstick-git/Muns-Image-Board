<?php

function logger($str)
{
    error_log("MunBoard Log: \n" . $str . "\n");
}

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
 * @param $data
 * @return binary
 */
function Expand($data)
{
    $data = base64_decode($data);
    $data = gzuncompress($data);
    return $data;
}

/**
 * @param $data
 * @return string
 */
function Compress($data)
{
    $data = gzcompress($data, 1);
    $data = base64_encode($data);
    return $data;
}