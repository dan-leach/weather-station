<?php
    
    $sql = "SELECT rainfall, datetime FROM tbl_weather WHERE datetime >= '$lastWeek'";

    $rainfall_week = "";
    $cumRainWeek = 0;

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            // don't need to manage missing values as cumulative

            $cumRainWeek += floatval($row['rainfall']);
            $rainfall_week .= strval($cumRainWeek) . ",";
        }
    } else {
        $error .= "No rainfall data found in database";
    }
    $rainfall_week = substr($rainfall_week, 0, -1); //remove trailing comma

?>
