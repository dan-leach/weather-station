<?php
    $num = 1; //default to return single row
    if (isset($_GET["num"])) $num = filter_var($_GET["num"], FILTER_SANITIZE_NUMBER_INT); //unless number of rows specified in GET paramater: num

    require '../link.php';
    if($link === false){
        die("Weather data could not be selected from databse. The server returned the following error message: " . mysqli_connect_error());
    }
    if ($num < 1) die();
    $sql = "SELECT *from tbl_weather ORDER BY eventID DESC LIMIT " . $num;
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo '{"datetime":"' . $row['datetime']. '",';
            echo '"version":"' . $row['version']. '",';
            echo '"comment":"' . $row['comment']. '",';
            echo '"wind_speed":' . $row['wind_speed']. ',';
            echo '"gust_speed":' . $row['gust_speed']. ',';
            echo '"wind_direction":"' . $row['wind_direction']. '",';
            echo '"rainfall":' . $row['rainfall']. ',';
            echo '"ambient_temp":' . $row['ambient_temp']. ',';
            echo '"ground_temp":' . $row['ground_temp']. ',';
            echo '"humidity":' . $row['humidity']. ',';
            echo '"pressure":' . $row['pressure']. '}';
        }
    } else {
        echo '{"error": "No weather data found in database"}';
    }
    $link->close();
?>