<?php
    //should be called by script which has already opened link to database
    $sql = "SELECT rainfall from tbl_weather ORDER BY eventID DESC LIMIT 60";
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        $cumRainHour = 0;
        while($row = $result->fetch_assoc()) {
            $cumRainHour += $row['rainfall'];
        }
    } else {
        $error += "No rainfall data found in database";
    }
?>