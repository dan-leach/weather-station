<?php
    require '../link.php';
    if($link === false){
        die("Weather data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error());
    }

    //get rainfall for last hour
    $rainMins = 60;
    require '../rainfall/rainMins.php';

    //get temp MinMax for today
    require '../temperature/dayMinMax.php';

    $sql = "SELECT *from tbl_weather ORDER BY eventID DESC ";
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "datetime: " . $row['datetime'];
            echo "; wind_speed: " . $row['wind_speed'];
            echo "; gust_speed: " . $row['gust_speed'];
            echo "; wind_direction: " . $row['wind_direction'];
            echo "; ambient_temp: " . $row['ambient_temp'];
            echo "; ground_temp: " . $row['ground_temp'];
            echo "; humidity: " . $row['humidity'];
            echo "; pressure: " . $row['pressure'];
            echo "<br>";
        }
    } else {
        die("Error: No weather data found in database");
    }
    $link->close();
?>