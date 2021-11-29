<?php
    $client_side_errors = "false";
    if(file_exists("../client_side_errors")) $client_side_errors = true;

    $server_side_errors = "false";
    if(file_exists("/weather/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/delete/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/delete/all/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/error/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/error/check/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/error/new/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/insert/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/select/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/select/all/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/select/latest/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/select/rainfall/error_log")) $server_side_errors = "true";
    if(file_exists("/weather/api/select/temperature/error_log")) $server_side_errors = "true";
    $output = '
            {
                "client_side_errors":' . $client_side_errors . ',
                "server_side_errors":' . $server_side_errors . '
            }
        ';
    echo $output;
?>