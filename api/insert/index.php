<?php
    //first check key is correct
    require '../key.php';
    $check = false;
    if (isset($_GET["key"])) {
        $k = $_GET["key"];
        $check = checkKey($k);
    }
    if (!$check) die("Incorrect key");

    class Weather { //Class of functions each returning values of each expected parameter following sanitisation. Returns -1 for any undefined expected parameters.
        function version(){
            if (isset($_GET["version"])) {
                return filter_var($_GET["version"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function comment(){
            if (isset($_GET["comment"])) {
                return filter_var($_GET["comment"], FILTER_SANITIZE_STRING);
            } else {
                return "";
            }
        }
        function wind_speed(){
            if (isset($_GET["wind_speed"])) {
                return filter_var($_GET["wind_speed"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function gust_speed(){
            if (isset($_GET["gust_speed"])) {
                return filter_var($_GET["gust_speed"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function wind_direction(){
            if (isset($_GET["wind_direction"])) {
                return filter_var($_GET["wind_direction"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function rainfall(){
            if (isset($_GET["rainfall"])) {
                return filter_var($_GET["rainfall"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function ambient_temp(){
            if (isset($_GET["ambient_temp"])) {
                return filter_var($_GET["ambient_temp"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function ground_temp(){
            if (isset($_GET["ground_temp"])) {
                return filter_var($_GET["ground_temp"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function humidity(){
            if (isset($_GET["humidity"])) {
                return filter_var($_GET["humidity"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function pressure(){
            if (isset($_GET["pressure"])) {
                return filter_var($_GET["pressure"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function allParams(){
            $str = "version: " . $this->version() . ", ";
            $str .= "comment: " . $this->comment() . ", ";
            $str .= "wind_speed: " . $this->wind_speed() . ", ";
            $str .= "gust_speed: " . $this->gust_speed() . ", ";
            $str .= "wind_direction: " . $this->wind_direction() . ", ";
            $str .= "rainfall: " . $this->rainfall() . ", ";
            $str .= "ambient_temp: " . $this->ambient_temp() . ", ";
            $str .= "ground_temp: " . $this->ground_temp() . ", ";
            $str .= "humidity: " . $this->humidity() . ", ";
            $str .= "pressure: " . $this->pressure();
            return $str;
        }
    }

    //generate log entries and parameters for SQL insert
    $update = new Weather();
    $version = $update->version();
    $comment = $update->comment();
    $wind_speed = $update->wind_speed();
    $gust_speed = $update->gust_speed();
    $wind_direction = $update->wind_direction();
    $rainfall = $update->rainfall();
    $ambient_temp = $update->ambient_temp();
    $ground_temp = $update->ground_temp();
    $humidity = $update->humidity();
    $pressure = $update->pressure();

    //insert into database
    require 'link.php';
    if($link === false){
        die("Weather data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("INSERT INTO tbl_weather (version, comment, wind_speed, gust_speed, wind_direction, rainfall, ambient_temp, ground_temp, humidity, pressure) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) {
        die("Weather data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    }
    $rc = $stmt->bind_param("ssssssssss", $version, $comment, $wind_speed, $gust_speed, $wind_direction, $rainfall, $ambient_temp, $ground_temp, $humidity, $pressure);
    if ( false===$rc ) {
        die("Weather data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    }
    $rc = $stmt->execute();
    if ( false===$rc ) {
        die("Weather data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));
    }
    $stmt->close();
    mysqli_close($link);

    //output if all successful
    echo "Weather station data submission: [" . date("Y-m-d H:i:s") . "] " . $update->allParams();;
?>