<?php
    //should be called by script which has already opened link to database
    $sql = "SELECT max(power), datetime FROM tbl_weather WHERE datetime >= '$today'";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $power_max = $row['max(power)'];
        } 
    } else {
        $error += "No power data found in database";
    }
?>