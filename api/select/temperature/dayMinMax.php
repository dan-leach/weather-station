<?php
    //should be called by script which has already opened link to database
    $today = date('Y-m-d');
    $sql = "SELECT min(ambient_temp), max(ambient_temp), min(ground_temp), max(ground_temp), datetime FROM tbl_weather WHERE datetime >= '$today'";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $ambient_temp_min = $row['min(ambient_temp)'];
            $ambient_temp_max = $row['max(ambient_temp)'];
            $ground_temp_min = $row['min(ground_temp)'];
            $ground_temp_max = $row['max(ground_temp)'];
        } 
    } else {
        $error += "No temperature data found in database";
    }
?>