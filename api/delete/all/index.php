<?php
    //first check key is correct
    require '../../key.php';
    $check = false;
    if (isset($_GET["key"])) {
        $k = $_GET["key"];
        $check = checkKey($k);
    }
    if (!$check) die('{"error":"Incorrect key"}');

    //clear SQL
    require '../link.php';
    if($link === false){
        die('{"error":"Weather data could not be cleared from databse. The server returned the following error message: ' . mysqli_connect_error() . '"}');
    }
    $sql = "DELETE FROM tbl_weather";
    if ($link->query($sql) === FALSE) {
        die('{"error":"Weather data could not be cleared from databse. The server returned the following error message: ' . $link->error . '"}');
    }
    $link->close();

    echo('{"msg":"Weather database cleared"}');
?>