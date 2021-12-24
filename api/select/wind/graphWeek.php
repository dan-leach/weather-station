<?php
    
    $sql = "SELECT wind_speed, gust_speed, wind_direction, datetime FROM tbl_weather WHERE datetime >= '$lastWeek'";

    $wind_speed_week = "";
    $gust_speed_week = "";
    $wind_direction_week = array("N" => 0,"NNE" => 0,"NE" => 0,"ENE" => 0,"E" => 0,"ESE" => 0,"SE" => 0,"SSE" => 0,"S" => 0,"SSW" => 0,"SW" => 0,"WSW" => 0,"W" => 0,"WNW" => 0,"NW" => 0,"NNW" => 0, "N/A" => 0, "-" => 0);
    $prevDecimalHours = 1; //set to one so that first decimal hours will always be less than this

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $datetime = new DateTime($row['datetime']); //creates datetime object with value of the datetime of current row's weather data
            $interval = $datetime->diff($midnight);
            $decimalHours = $interval->h + (($interval->i) / 60);

            while ($decimalHours > ($prevDecimalHours + (1/59.9))) { //59.9 rather than 60 to allow for decimal hour imprecision due to finite significant digits
                $wind_speed_week .= "NaN" . ",";
                $gust_speed_week .= "NaN" . ",";
                $prevDecimalHours += (1/60);
            }
            
            $wind_direction_week[$row['wind_direction']]++;
            $wind_speed_week .= $row['wind_speed'] . ",";
            $gust_speed_week .= $row['gust_speed'] . ",";

            $prevDecimalHours = $decimalHours;
        }
    } else {
        $error .= "No weather data found in database; ";
    }

    $wind_speed_week = substr($wind_speed_week, 0, -1); //remove trailing comma
    $gust_speed_week = substr($gust_speed_week, 0, -1); //remove trailing comma

?>
