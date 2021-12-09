<?php
    $error = "";

    require '../link.php';
    if($link === false){
        $error += "Graph data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error();
    }

    $today = date('Y-m-d');
    $midnight = new DateTime('midnight'); //creates datetime object with value of 00:00:00 today

    require '../temperature/graph.php';
    require '../wind/graph.php';
    require '../pressure/graph.php';
    require '../humidity/graph.php';
    require '../rainfall/graph.php';

    $link->close();

    $output = '
        {
            "data": {
                "ambient_temp":"' . $ambient_temp . '",
                "ground_temp":"' . $ground_temp . '",
                "wind_speed":"' . $wind_speed . '",
                "gust_speed":"' . $gust_speed . '",
                "wind_direction": {
                    "North":' . $wind_direction["N"] . ',
                    "North-northeast":' . $wind_direction["NNE"] . ',
                    "Northeast":' . $wind_direction["NE"] . ',
                    "East-northeast":' . $wind_direction["ENE"] . ',
                    "East":' . $wind_direction["E"] . ',
                    "East-southeast":' . $wind_direction["ESE"] . ',
                    "Southeast":' . $wind_direction["SE"] . ',
                    "South-southeast":' . $wind_direction["SSE"] . ',
                    "South":' . $wind_direction["S"] . ',
                    "South-southwest":' . $wind_direction["SSW"] . ',
                    "Southwest":' . $wind_direction["SW"] . ',
                    "West-southwest":' . $wind_direction["WSW"] . ',
                    "West":' . $wind_direction["W"] . ',
                    "West-northwest":' . $wind_direction["WNW"] . ',
                    "Northwest":' . $wind_direction["NW"] . ',
                    "North-northwest":' . $wind_direction["NNW"] . '
                },
                "pressure":"' . $pressure . '",
                "humidity":"' . $humidity . '",
                "rainfall":"' . $rainfall . '"
            },
            "error":"' . $error . '"
        }
    ';
    echo $output;
?>