<?php
    //first check key is correct
    require '../key.php';
    $check = false;
    if (isset($_GET["key"])) {
        $k = $_GET["key"];
        $check = checkKey($k);
    }
    if (!$check) die(
        '{
            "status":' . http_response_code(401) . ',
            "submissionID":"' . $submissionID . ',
            "msg":"Incorrect key"
        }'
    );

    class Weather { //Class of functions each returning values of each expected parameter following sanitisation. Returns -1 for any undefined expected parameters.
        function submissionID(){
            if (isset($_GET["datetime"])) {
                return filter_var($_GET["datetime"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
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
        function power(){
            if (isset($_GET["power"])) {
                return filter_var($_GET["power"], FILTER_SANITIZE_STRING);
            } else {
                return -1;
            }
        }
        function allParams(){
            $str = "submissionID: " . $this->submissionID() . ", ";
            $str .= "version: " . $this->version() . ", ";
            $str .= "comment: " . $this->comment() . ", ";
            $str .= "wind_speed: " . $this->wind_speed() . ", ";
            $str .= "gust_speed: " . $this->gust_speed() . ", ";
            $str .= "wind_direction: " . $this->wind_direction() . ", ";
            $str .= "rainfall: " . $this->rainfall() . ", ";
            $str .= "ambient_temp: " . $this->ambient_temp() . ", ";
            $str .= "ground_temp: " . $this->ground_temp() . ", ";
            $str .= "humidity: " . $this->humidity() . ", ";
            $str .= "pressure: " . $this->pressure();
            $str .= "power: " . $this->power();
            return $str;
        }
    }

    //generate log entries and parameters for SQL insert
    $update = new Weather();
    $submissionID = $update->submissionID();
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
    $power = $update->power();

    //insert into database
    require 'link.php';
    if($link === false){
        die(
            '{
                "status":' . http_response_code(500) . ',
                "submissionID":"' . $submissionID . ',
                "msg": "Weather data could not be logged. The server returned the following error message: "' . mysqli_connect_error() . '
            }'
        );
    }
    
    if ($result = $link->query("SELECT submissionID FROM tbl_weather WHERE submissionID = '$submissionID'")) { //ensures that each submission is unique
            die(
                '{
                    "status":' . http_response_code(409) . ',
                    "submissionID":"' . $submissionID . ',
                    "msg": "This submissionID already exists in tbl_weather"
                }'
            );
    }

    $stmt = $link->prepare("INSERT INTO tbl_weather (version, comment, wind_speed, gust_speed, wind_direction, rainfall, ambient_temp, ground_temp, humidity, pressure, power, submissionID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) {
        die(
            '{
                "status":' . http_response_code(500) . ',
                "submissionID":"' . $submissionID . ',
                "msg": "Weather data could not be logged. The server returned the following error message: prepare() failed: "' . mysqli_error($link) .'
            }'
        );
    }
    $rc = $stmt->bind_param("ssssssssssss", $version, $comment, $wind_speed, $gust_speed, $wind_direction, $rainfall, $ambient_temp, $ground_temp, $humidity, $pressure, $power, $submissionID);
    if ( false===$rc ) {
        die(
            '{
                "status":' . http_response_code(500) . ',
                "submissionID":"' . $submissionID . ',
                "msg": "Weather data could not be logged. The server returned the following error message: bind_param() failed: "' . mysqli_error($link) . '
            }'
        );
    }
    $rc = $stmt->execute();
    if ( false===$rc ) {
        die(
            '{
                "status":' . http_response_code(500) . ',
                "submissionID":"' . $submissionID . ',
                "msg": "Weather data could not be logged. The server returned the following error message: execute() failed: "' . mysqli_error($link) . '
            }'
        );
    }
    $stmt->close();
    mysqli_close($link);

    //output if all successful
    $output = '
        {
            "status":' . http_response_code(200) . ',
            "submissionID":"' . $submissionID . '
        }
    ';
    echo $output;
?>