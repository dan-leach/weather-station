<?php
    //should be called by script which has already opened link to database
    $sql = "SELECT max(gust_speed), datetime FROM tbl_weather WHERE datetime >= '$today'";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $gust_speed_max = $row['max(gust_speed)'];
        } 
    } else {
        $error += "No gust speed data found in database";
    }
?>