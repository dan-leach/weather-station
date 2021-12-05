<?php
    //first check key is correct
    require "../../key.php";
    $check = false;
    if (isset($_GET["key"])) {
        $k = $_GET["key"];
        $check = checkKey($k);
    }
    if (!$check) die('{"failMsg":"Incorrect key"}');

    $source = "";
    if (isset($_GET["source"])) $source = $_GET["source"];
    if (!$source) die("Must select error source to clear.");

    function removeErrorFile($path){
        if (file_exists($path)) {
            unlink($path);
            return true;
        }
    }

    if ($source == "server") {
        removeErrorFile("/home/dev/public_html/weather/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/delete/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/delete/all/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/error/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/error/check/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/error/clear/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/error/new/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/insert/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/all/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/humidity/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/latest/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/pressure/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/rainfall/error_log");
        removeErrorFile("/home/dev/public_html/weather/api/select/temperature/error_log");
        die('{"passMsg":"Server error logs cleared"}');
    }
    
    $source = "/home/dev/public_html/weather/api/error/" . $source . "_errors";
    $removed = removeErrorFile($source);
    if ($removed){
        die('{"passMsg":"' . $source . ' error logs cleared"}');
    } else {
        die('{"failMsg":"Could not locate error file [' . $source . '] to remove it"}');
    }
    
?>