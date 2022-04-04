<?php
    
    $sql = "SELECT rainfall, datetime FROM tbl_weather WHERE datetime >= '$today' ORDER BY datetime ASC";

    $rainfall = "";
    $cumRainToday = 0;
    $prevDecimalHours = 1; //set to one so that first decimal hours will always be less than this

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $datetime = new DateTime($row['datetime']); //creates datetime object with value of the datetime of current row's weather data
            $interval = $datetime->diff($midnight);
            $decimalHours = $interval->h + (($interval->i) / 60);

            while ($decimalHours > ($prevDecimalHours + (1/59.9))) { //59.9 rather than 60 to allow for decimal hour imprecision due to finite significant digits
                $rainfall .= "NaN" . ",";
                $prevDecimalHours += (1/60);
            }

            $cumRainToday += floatval($row['rainfall']);
            $rainfall .= strval($cumRainToday) . ",";

            $prevDecimalHours = $decimalHours;
        }
    } else {
        $error .= "No rainfall data found in database";
    }
    $cumRainToday = 20;
    $rainfall = substr($rainfall, 0, -1); //remove trailing comma

?>
