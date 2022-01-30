<?php
    
    $sql = "SELECT wind_speed, gust_speed, wind_direction, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $wind_speed_week = "";
    $gust_speed_week = "";
    $wind_direction_week = array("N" => 0,"NNE" => 0,"NE" => 0,"ENE" => 0,"E" => 0,"ESE" => 0,"SE" => 0,"SSE" => 0,"S" => 0,"SSW" => 0,"SW" => 0,"WSW" => 0,"W" => 0,"WNW" => 0,"NW" => 0,"NNW" => 0, "N/A" => 0, "-" => 0, "-1" => 0);

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            
            $wind_direction_week[$row['wind_direction']]++;
            $wind_speed_week .= $row['wind_speed'] . ",";
            $gust_speed_week .= $row['gust_speed'] . ",";
        }
    } else {
        $error .= "No weather data found in database; ";
    }

    $wind_speed_week = substr($wind_speed_week, 0, -1); //remove trailing comma
    $gust_speed_week = substr($gust_speed_week, 0, -1); //remove trailing comma

?>
