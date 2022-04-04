<?php
    $error = "";

    require '../../consolidate/getTimes.php';
    if ($backfillIsNeeded) {
        require '../../consolidate/hourlyBackfill.php';
    }

    require '../link.php';
    if($link === false){
        $error += "Graph data could not be selected from database. The server returned the following error message: " . mysqli_connect_error();
    }

    $midnight = new DateTime('midnight', new DateTimeZone('Europe/London')); //creates datetime object with value of 00:00:00 today
    $midnight->setTimezone(new DateTimeZone('UTC')); //account for difference between UTC and UK time during BST
    $today = $midnight->format('Y-m-d H:i:s');
    require '../temperature/graphToday.php';
    require '../wind/graphToday.php';
    require '../pressure/graphToday.php';
    require '../humidity/graphToday.php';
    require '../rainfall/graphToday.php';
    require '../power/graphToday.php';

    $lastWeek = date("Y-m-d", strtotime("-1 week"));
    require '../temperature/graphWeek.php';
    require '../wind/graphWeek.php';
    require '../pressure/graphWeek.php';
    require '../humidity/graphWeek.php';
    require '../rainfall/graphWeek.php';
    require '../power/graphWeek.php';

    $link->close();

    $output = '
        {
            "data": {
                "today": {
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
                    "rainfall":"' . $rainfall . '",
                    "power":"' . $power . '"
                },
                "week": {
                    "ambient_temp":"' . $ambient_temp_week . '",
                    "ground_temp":"' . $ground_temp_week . '",
                    "wind_speed":"' . $wind_speed_week . '",
                    "gust_speed":"' . $gust_speed_week . '",
                    "wind_direction": {
                        "North":' . $wind_direction_week["N"] . ',
                        "North-northeast":' . $wind_direction_week["NNE"] . ',
                        "Northeast":' . $wind_direction_week["NE"] . ',
                        "East-northeast":' . $wind_direction_week["ENE"] . ',
                        "East":' . $wind_direction_week["E"] . ',
                        "East-southeast":' . $wind_direction_week["ESE"] . ',
                        "Southeast":' . $wind_direction_week["SE"] . ',
                        "South-southeast":' . $wind_direction_week["SSE"] . ',
                        "South":' . $wind_direction_week["S"] . ',
                        "South-southwest":' . $wind_direction_week["SSW"] . ',
                        "Southwest":' . $wind_direction_week["SW"] . ',
                        "West-southwest":' . $wind_direction_week["WSW"] . ',
                        "West":' . $wind_direction_week["W"] . ',
                        "West-northwest":' . $wind_direction_week["WNW"] . ',
                        "Northwest":' . $wind_direction_week["NW"] . ',
                        "North-northwest":' . $wind_direction_week["NNW"] . '
                    },
                    "pressure":"' . $pressure_week . '",
                    "humidity":"' . $humidity_week . '",
                    "rainfall":"' . $rainfall_week . '",
                    "power":"' . $power_week . '",
                    "labels":"' . $labels . '"
                }
            },
            "backfillIsNeeded":"' . $backfillIsNeeded . '",
            "error":"' . $error . '"
        }
    ';
    echo $output;
?>