<?php
    
    $sql = "SELECT rainfall, datetime FROM tbl_weather WHERE datetime >= '$today'";

    $rainfall = "";
    $cumRainToday = 0;

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            // don't need to manage missing values as cumulative

            $cumRainToday += floatval($row['rainfall']);
            $rainfall .= strval($cumRainToday) . ",";
        }
    } else {
        $error .= "No rainfall data found in database";
    }
    $cumRainToday = 20;
    $rainfall = substr($rainfall, 0, -1); //remove trailing comma

?>
