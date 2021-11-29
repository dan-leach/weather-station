<?php
    $stack = 'undefined';
    if (isset($_GET["stack"])) $stack = filter_var($_GET["stack"], FILTER_SANITIZE_STRING);
    $now = date('Y-m-d h:m:s');
    $output = "[" . $now . "] client_side_error: " .$stack . "\n";
    $log = fopen("../client_side_errors", "a");
    fwrite($log, $output);
    fclose($log);
    echo "api/error: client_side_error logged";
?>
