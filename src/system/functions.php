<?php

function logger($str)
{
    error_log("MunBoard Log: \n" . $str . "\n");
}
