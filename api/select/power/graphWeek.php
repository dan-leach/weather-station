<?php
    
    $sql = "SELECT power, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $power_week = "";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {            
            $power_week .= $row['power'] . ",";
        }
    } else {
        $error .= "No power data found in database";
    }

    $power_week = substr($power_week, 0, -1); //remove trailing comma

?>
