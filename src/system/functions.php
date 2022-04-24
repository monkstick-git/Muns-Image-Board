<?php

function logger($str)
{
    error_log("MunBoard Error: " . $str . "\n");
}
