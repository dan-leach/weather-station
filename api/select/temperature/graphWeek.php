<?php

    $sql = "SELECT ambient_temp, ground_temp, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $ambient_temp_week = "";
    $ground_temp_week = "";

    $prevDecimalHours = 1; //set to one so that first decimal hours will always be less than this

    $result = $link->query($sql);
    
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $datetime = new DateTime($row['datetime']); //creates datetime object with value of the datetime of current row's weather data
            $interval = $datetime->diff($midnight);
            $decimalHours = $interval->h + (($interval->i) / 60);

            while ($decimalHours > ($prevDecimalHours + (1/59.9))) { //59.9 rather than 60 to allow for decimal hour imprecision due to finite significant digits
                $ambient_temp_week .= "NaN" . ",";
                $ground_temp_week .= "NaN" . ",";
                $prevDecimalHours += (1/60);
            }
            
            $ambient_temp_week .= $row['ambient_temp'] . ",";
            $ground_temp_week .= $row['ground_temp'] . ",";

            $prevDecimalHours = $decimalHours;
        }
    } else {
        $error .= "No temperature data found in database";
    }

    $ambient_temp_week = substr($ambient_temp_week, 0, -1); //remove trailing comma
    $ground_temp_week = substr($ground_temp_week, 0, -1); //remove trailing comma

?>
