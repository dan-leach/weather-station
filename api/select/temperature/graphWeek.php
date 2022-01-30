<?php

    $sql = "SELECT ambient_temp, ground_temp, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $ambient_temp_week = "";
    $ground_temp_week = "";

    $result = $link->query($sql);
    
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $ambient_temp_week .= $row['ambient_temp'] . ",";
            $ground_temp_week .= $row['ground_temp'] . ",";
        }
    } else {
        $error .= "No temperature data found in database";
    }

    $ambient_temp_week = substr($ambient_temp_week, 0, -1); //remove trailing comma
    $ground_temp_week = substr($ground_temp_week, 0, -1); //remove trailing comma
    
?>
