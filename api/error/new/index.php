<?php
    $source = 'undefined';
    $stack = 'undefined';
    if (isset($_GET["source"])) $source = htmlspecialchars($_GET["source"]);
    if (isset($_GET["stack"])) $stack = htmlspecialchars($_GET["stack"]);
    $now = date('Y-m-d H:i:s');
    $output = "[" . $now . "] ". $source . ": " .$stack . "\n";
    $log = fopen("../" . $source . "_errors", "a");
    fwrite($log, $output);
    fclose($log);
    echo "api/error/new: ". $source . " logged";
?>
