<?php
    
    $sql = "SELECT humidity, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $humidity_week = "";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {            
            $humidity_week .= $row['humidity'] . ",";
        }
    } else {
        $error .= "No humidity data found in database";
    }

    $humidity_week = substr($humidity_week, 0, -1); //remove trailing comma

?>
