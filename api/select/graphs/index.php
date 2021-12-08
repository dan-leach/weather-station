<?php
    $error = "";

    require '../link.php';
    if($link === false){
        $error += "Graph data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error();
    }

    $today = date('Y-m-d');
    $midnight = new DateTime('midnight'); //creates datetime object with value of 00:00:00 today

    require '../temperature/graph.php';

    $output = '
        {
            "data": {
                "ambient_temp":"' . $ambient_temp . '",
                "ground_temp":"' . $ground_temp . '"
            },
            "error":"' . $error . '"
        }
    ';
    echo $output;
?>