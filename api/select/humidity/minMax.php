<?php
    //should be called by script which has already opened link to database
    $sql = "SELECT min(humidity), max(humidity), datetime FROM tbl_weather WHERE datetime >= '$today'";

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $humidity_min = $row['min(humidity)'];
            $humidity_max = $row['max(humidity)'];
        } 
    } else {
        $error += "No temperature data found in database";
    }
?>