<?php
    $error = "";

    require '../link.php';
    if($link === false){
        $error += "Weather data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error();
    }

    //get rainfall for last hour
    require '../rainfall/cumRainHour.php';

    //get minimums and maximums for today
    $today = date('Y-m-d');
    require '../temperature/minMax.php';
    require '../pressure/minMax.php';
    require '../humidity/minMax.php';
    require '../wind/max.php';
    require '../power/max.php';

    //get latest cumulative for today to update graph
    require '../rainfall/graphToday.php';

    $sql = "SELECT *from tbl_weather ORDER BY eventID DESC LIMIT 1";
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $datetime = $row['datetime'];
            $wind_speed = $row['wind_speed'];
            $gust_speed = $row['gust_speed'];
            $wind_direction = $row['wind_direction'];
            $ambient_temp = $row['ambient_temp'];
            $ground_temp = $row['ground_temp'];
            $humidity = $row['humidity'];
            $pressure = $row['pressure'];
            $power = $row['power'];
            $energy = $row['energy'];
        }
    } else {
        $error += "No weather data found in database";
    }
    $link->close();
    
    $output = '
        {
            "latest": {
                "datetime":"' . $datetime . '",
                "wind_speed":' . $wind_speed . ',
                "gust_speed":' . $gust_speed . ',
                "wind_direction":"' . $wind_direction . '",
                "ambient_temp":' . $ambient_temp . ',
                "ground_temp":' . $ground_temp . ',
                "humidity":' . $humidity . ',
                "pressure":' . $pressure . ',
                "rainfall":' . $cumRainToday . ',
                "power":' . $power . ',
                "energy":' . $energy . '
            },
            "cum": {
                "rainfall":' . $cumRainHour . '
            },
            "minMax": {
                "ambient_temp": {
                    "min":' . $ambient_temp_min . ',
                    "max":' . $ambient_temp_max . '
                },
                "ground_temp": {
                    "min":' . $ground_temp_min . ',
                    "max":' . $ground_temp_max . '
                },
                "pressure": {
                    "min":' . $pressure_min . ',
                    "max":' . $pressure_max . '
                },
                "humidity": {
                    "min":' . $humidity_min . ',
                    "max":' . $humidity_max . '
                },
                "gust_speed": {
                    "max":' . $gust_speed_max . '
                },
                "power": {
                    "max":' . $power_max . '
                }
            },
            "error":"' . $error . '"
        }
    ';
    echo $output;
?>