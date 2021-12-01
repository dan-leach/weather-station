<?php
    //should be called by script which has already opened link to database
    $sql = "SELECT min(pressure), max(pressure), datetime FROM tbl_weather WHERE datetime >= '$today'";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $pressure_min = $row['min(pressure)'];
            $pressure_max = $row['max(pressure)'];
        } 
    } else {
        $error += "No temperature data found in database";
    }
?>