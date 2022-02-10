<?php
    
    $sql = "SELECT humidity, datetime FROM tbl_weather_hourly WHERE datetime >= '$lastWeek'";

    $humidity_week = "";
    $labels = "";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {            
            $humidity_week .= $row['humidity'] . ",";

            //also generate the datetime labels in this file
            $dt = new DateTime($row['datetime']);
            $labels .= $dt->format('l H:00') . ",";
        }
    } else {
        $error .= "No humidity data found in database";
    }

    $humidity_week = substr($humidity_week, 0, -1); //remove trailing comma
    $labels = substr($labels, 0, -1); //remove trailing comma

?>
