<?php
    
    $sql = "SELECT pressure, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $pressure_week = "";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {            
            $pressure_week .= $row['pressure'] . ",";
        }
    } else {
        $error .= "No pressure data found in database";
    }

    $pressure_week = substr($pressure_week, 0, -1); //remove trailing comma

?>
