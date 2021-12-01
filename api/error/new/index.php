<?php
    $source = 'undefined';
    $stack = 'undefined';
    if (isset($_GET["source"])) $source = filter_var($_GET["source"], FILTER_SANITIZE_STRING);
    if (isset($_GET["stack"])) $stack = filter_var($_GET["stack"], FILTER_SANITIZE_STRING);
    $now = date('Y-m-d h:m:s');
    $output = "[" . $now . "] ". $source . ": " .$stack . "\n";
    $log = fopen("../" . $source . "_errors", "a");
    fwrite($log, $output);
    fclose($log);
    echo "api/error/new: ". $source . " logged";
?>
