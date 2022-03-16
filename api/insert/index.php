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
                return htmlspecialchars($_GET["datetime"]);
            } else {
                return -1;
            }
        }
        function datetime(){
            if (isset($_GET["datetime"])) {
                return date("Y-m-d H:i:s", htmlspecialchars($_GET["datetime"]));
            } else {
                return -1;
            }
        }
        function version(){
            if (isset($_GET["version"])) {
                return htmlspecialchars($_GET["version"]);
            } else {
                return -1;
            }
        }
        function comment(){
            if (isset($_GET["comment"])) {
                return htmlspecialchars($_GET["comment"]);
            } else {
                return "";
            }
        }
        function wind_speed(){
            if (isset($_GET["wind_speed"])) {
                return htmlspecialchars($_GET["wind_speed"]);
            } else {
                return -1;
            }
        }
        function gust_speed(){
            if (isset($_GET["gust_speed"])) {
                return htmlspecialchars($_GET["gust_speed"]);
            } else {
                return -1;
            }
        }
        function wind_direction(){
            if (isset($_GET["wind_direction"])) {
                return htmlspecialchars($_GET["wind_direction"]);
            } else {
                return -1;
            }
        }
        function rainfall(){
            if (isset($_GET["rainfall"])) {
                return htmlspecialchars($_GET["rainfall"]);
            } else {
                return -1;
            }
        }
        function ambient_temp(){
            if (isset($_GET["ambient_temp"])) {
                return htmlspecialchars($_GET["ambient_temp"]);
            } else {
                return -1;
            }
        }
        function internal_temp(){
            if (isset($_GET["internal_temp"])) {
                return htmlspecialchars($_GET["internal_temp"]);
            } else {
                return -1;
            }
        }
        function ground_temp(){
            if (isset($_GET["ground_temp"])) {
                return htmlspecialchars($_GET["ground_temp"]);
            } else {
                return -1;
            }
        }
        function humidity(){
            if (isset($_GET["humidity"])) {
                return htmlspecialchars($_GET["humidity"]);
            } else {
                return -1;
            }
        }
        function pressure(){
            if (isset($_GET["pressure"])) {
                return htmlspecialchars($_GET["pressure"]);
            } else {
                return -1;
            }
        }
        function power(){
            if (isset($_GET["power"])) {
                return htmlspecialchars($_GET["power"]);
            } else {
                return -1;
            }
        }
        function energy(){
            if (isset($_GET["energy"])) {
                return htmlspecialchars($_GET["energy"]);
            } else {
                return -1;
            }
        }
        function allParams(){
            $str = "submissionID: " . $this->submissionID() . ", ";
            $str .= "datetime: " . $this->datetime() . ", ";
            $str .= "version: " . $this->version() . ", ";
            $str .= "comment: " . $this->comment() . ", ";
            $str .= "wind_speed: " . $this->wind_speed() . ", ";
            $str .= "gust_speed: " . $this->gust_speed() . ", ";
            $str .= "wind_direction: " . $this->wind_direction() . ", ";
            $str .= "rainfall: " . $this->rainfall() . ", ";
            $str .= "ambient_temp: " . $this->ambient_temp() . ", ";
            $str .= "internal_temp: " . $this->internal_temp() . ", ";
            $str .= "ground_temp: " . $this->ground_temp() . ", ";
            $str .= "humidity: " . $this->humidity() . ", ";
            $str .= "pressure: " . $this->pressure()  . ", ";
            $str .= "power: " . $this->power() . ", "; //instantaneous power (kW)
            $str .= "energy: " . $this->energy(); //cumulative energy generated since midnight (kWh)
            return $str;
        }
    }

    //generate log entries and parameters for SQL insert
    $update = new Weather();
    $submissionID = $update->submissionID();
    $datetime = $update->datetime();
    $version = $update->version();
    $comment = $update->comment();
    $wind_speed = $update->wind_speed();
    $gust_speed = $update->gust_speed();
    $wind_direction = $update->wind_direction();
    $rainfall = $update->rainfall();
    $ambient_temp = $update->ambient_temp();
    $internal_temp = $update->internal_temp();
    $ground_temp = $update->ground_temp();
    $humidity = $update->humidity();
    $pressure = $update->pressure();
    $power = $update->power();
    $energy = $update->energy();

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

    $stmt = $link->prepare("INSERT INTO tbl_weather (datetime, version, comment, wind_speed, gust_speed, wind_direction, rainfall, ambient_temp, internal_temp, ground_temp, humidity, pressure, power, energy, submissionID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) {
        die(
            '{
                "status":' . http_response_code(500) . ',
                "submissionID":"' . $submissionID . ',
                "msg": "Weather data could not be logged. The server returned the following error message: prepare() failed: "' . mysqli_error($link) .'
            }'
        );
    }
    $rc = $stmt->bind_param("sssssssssssssss", $datetime, $version, $comment, $wind_speed, $gust_speed, $wind_direction, $rainfall, $ambient_temp, $internal_temp, $ground_temp, $humidity, $pressure, $power, $energy, $submissionID);
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
            "submissionID":"' . $submissionID . '",
            "datetime":"' . $datetime . '"
        }
    ';
    echo $output;

?>