<?php

    $sql = "SELECT datetime FROM tbl_weather_hourly ORDER BY datetime ASC";
        $result = $link->query($sql);

        while($row = $result->fetch_assoc()) {

            $start = new DateTime($row['datetime']);
    
        }

        $backfillStart = $start;
        $startCheck = $start->format('Y-m-d H');
        $backfillStart->modify('+1 hour');
        
        $end = new DateTime();
        $end->modify('-1 hour');
        $endString = $end->format('Y-m-d H:59:59');
  
        //check that an hourly backfill is required
        $endCheck = $end->format('Y-m-d H');
        if ($startCheck == $endCheck){
            $backfillIsNeeded = false;
        } else {
            $backfillIsNeeded = true;
        }

        $endString = $end->format('Y-m-d H:59:59');
        $backfillEnd = new DateTime($endString);
?>