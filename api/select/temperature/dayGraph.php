<?php
    $error = "";

    require '../link.php';
    if($link === false){
        $error += "Graph data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error();
    }

    $today = date('Y-m-d');
    $sql = "SELECT ambient_temp, ground_temp, datetime FROM tbl_weather WHERE datetime >= '$today'";

    $ambient_temp = "";
    $ground_temp = "";
    $midnight = new DateTime('midnight');

    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $datetime = new DateTime($row['datetime']);
            $interval = $datetime->diff($midnight);
            $decimalHours = $interval->h + (($interval->i) / 60);
            
            $ambient_temp .= $decimalHours . "," . $row['ambient_temp'] . ";";
            $ground_temp .= $decimalHours . "," . $row['ground_temp'] . ";";
        }
    } else {
        $error += "No weather data found in database";
    }
    $link->close();

    $ambient_temp = substr($ambient_temp, 0, -1); //remove trailing comma
    $ground_temp = substr($ground_temp, 0, -1); //remove trailing comma

    $output = '
        {
            "data": {
                "ambient_temp":"' . $ambient_temp . '",
                "ground_temp":"' . $ground_temp . '"
            },
            "error":"' . $error . '"
        }
    ';
    echo $output;

?>
