<?php
    
    $sql = "SELECT rainfall, datetime FROM tbl_weather WHERE datetime >= '$today'";

    $rainfall = "";
    $cumRain = 0;

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            // don't need to manage missing values as cumulative

            $cumRain += floatval($row['rainfall']);
            $rainfall .= strval($cumRain) . ",";
        }
    } else {
        $error .= "No rainfall data found in database";
    }

    $rainfall = substr($rainfall, 0, -1); //remove trailing comma

?>
