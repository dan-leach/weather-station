<?php
    function checkErrorFile($path) {
        if (!file_exists($path)) return; //do nothing if error file not found
        return "[" . $path . "]:<br>" . file_get_contents($path) . ";<br>";
    }
    
    $client_errors = "";
    $client_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/client_errors");

    $station_errors = "";
    $station_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/station_errors");

    $server_errors = "";
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/delete/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/delete/all/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/check/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/clear/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/error/new/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/insert/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/all/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/humidity/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/latest/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/pressure/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/rainfall/error_log");
    $server_errors .= checkErrorFile("/home/dev/public_html/weather/api/select/temperature/error_log");
    
    $output = '
            {
                "client":"' . base64_encode($client_errors) . '",
                "server":"' . base64_encode($server_errors) . '",
                "station":"' . base64_encode($station_errors) . '"
            }
        ';
    echo $output;
?>