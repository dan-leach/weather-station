<?php
    $client_errors = "false";
    if(file_exists("../client_errors")) $client_errors = true;

    $station_errors = "false";
    if(file_exists("../station_errors")) $station_errors = true;

    $server_errors = "false";
    if(file_exists("../../../error_log")) $server_errors = "true";
    if(file_exists("../../error_log")) $server_errors = "true";
    if(file_exists("../../delete/error_log")) $server_errors = "true";
    if(file_exists("../../delete/all/error_log")) $server_errors = "true";
    if(file_exists("../error_log")) $server_errors = "true";
    if(file_exists("../check/error_log")) $server_errors = "true";
    if(file_exists("../new/error_log")) $server_errors = "true";
    if(file_exists("../../insert/error_log")) $server_errors = "true";
    if(file_exists("../../select/error_log")) $server_errors = "true";
    if(file_exists("../../select/all/error_log")) $server_errors = "true";
    if(file_exists("../../select/latest/error_log")) $server_errors = "true";
    if(file_exists("../../select/rainfall/error_log")) $server_errors = "true";
    if(file_exists("../../select/temperature/error_log")) $server_errors = "true";
    if(file_exists("../../select/pressure/error_log")) $server_errors = "true";
    if(file_exists("../../select/humidity/error_log")) $server_errors = "true";
    $output = '
            {
                "client_errors":' . $client_errors . ',
                "server_errors":' . $server_errors . ',
                "station_errors":' . $station_errors . '
            }
        ';
    echo $output;
?>