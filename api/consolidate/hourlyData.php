<?php

    $wind_speeds = [];
    $max_gust_speed = 0;
    $wind_directions = [];
    $rainfall = 0;
    $ambient_temps = [];
    $ground_temps = [];
    $humiditys = [];
    $pressures = [];
    $powers = [];

    require 'link.php';

    $oneHour = date("Y-m-d H:i:s", strtotime("-1 hour"));

    $sql = "SELECT *, datetime FROM tbl_weather WHERE datetime >= '$oneHour'";
    $result = $link->query($sql);
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $wind_speeds[] = $row['wind_speed'];
        $gust_speed = $row['gust_speed'];
        if ($gust_speed > $max_gust_speed) $max_gust_speed = $gust_speed;
        switch ($row['wind_direction']) { //creates an array of wind directions in degrees, which can then be averaged for the hour
            case 'N':
                $wind_directions[] = 0;
                break;
            case 'NNE':
                $wind_directions[] = 22.5;
                break;
            case 'NE':
                $wind_directions[] = 45;
                break;
            case 'ENE':
                $wind_directions[] = 67.5;
                break;
            case 'E':
                $wind_directions[] = 90;
                break;
            case 'ESE':
                $wind_directions[] = 112.5;
                break;
            case 'SE':
                $wind_directions[] = 135;
                break;
            case 'SSE':
                $wind_directions[] = 157.5;
                break;
            case 'S':
                $wind_directions[] = 180;
                break;
            case 'SSW':
                $wind_directions[] = 202.5;
                break;
            case 'SW':
                $wind_directions[] = 225;
                break;
            case 'WSW':
                $wind_directions[] = 247.5;
                break;
            case 'W':
                $wind_directions[] = 270;
                break;
            case 'WNW':
                $wind_directions[] = 292.5;
                break;
            case 'NW':
                $wind_directions[] = 315;
                break;
            case 'NNW':
                $wind_directions[] = 337.5;
                break;
        };
        $rainfall += $row['rainfall'];
        $ambient_temps[] = $row['ambient_temp'];
        $ground_temps[] = $row['ground_temp'];
        $humiditys[] = $row['humidity'];
        $pressures[] = $row['pressure'];
        $powers[] = $row['power'];

    }

    $av_wind_speed = array_sum($wind_speeds)/count($wind_speeds);
    $av_wind_direction = array_sum($wind_directions)/count($wind_directions);
    $av_ambient_temp = array_sum($ambient_temps)/count($ambient_temps);
    $av_ground_temp = array_sum($ground_temps)/count($ground_temps);
    $av_humidity = array_sum($humiditys)/count($humiditys);
    $av_pressure = array_sum($pressures)/count($pressures);
    $av_power = array_sum($powers)/count($powers);

    echo "<strong>Averaging data for time period from " . $oneHour . " to now.</strong><br>";
    echo "av_wind_speed: " . $av_wind_speed . "<br>";
    echo "max_gust_speed: " . $max_gust_speed . "<br>";
    echo "av_wind_direction: " . $av_wind_direction . "<br>";
    echo "rainfall: " . $rainfall . "<br>";
    echo "av_ambient_temp: " . $av_ambient_temp . "<br>";
    echo "av_ground_temp: " . $av_ground_temp . "<br>";
    echo "av_humidity: " . $av_humidity . "<br>";
    echo "av_pressure: " . $av_pressure . "<br>";
    echo "av_power: " . $av_power . "<br>";

    $stmt = $link->prepare("INSERT INTO tbl_weather_hourly (wind_speed, gust_speed, wind_direction, rainfall, ambient_temp, ground_temp, humidity, pressure, power) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $rc = $stmt->bind_param("sssssssss", $av_wind_speed, $max_gust_speed, $av_wind_direction, $rainfall, $av_ambient_temp, $av_ground_temp, $av_humidity, $av_pressure, $av_power);
    $rc = $stmt->execute();
    $stmt->close();
    $link->close();
?>