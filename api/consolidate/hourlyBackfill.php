<?php
    require 'link.php';

    if (!isset($backfillIsNeeded)) die("Launch via graphs/index.php");
    if (!$backfillIsNeeded) die("Backfill not required");

    //get required backfill from getTimes.php
    $dt = $backfillStart;
    $end = $backfillEnd;

    //or set custom start and end
    //$dt = new Datetime('2022-01-23 16:00:00');
    //$end = new Datetime('2022-01-30 14:00:00');

    //echo "backfill period: " . $dt->format('Y-m-d H:i:s') . " to " . $end->format('Y-m-d H:i:s') . "<br>";

    while ($dt < $end){
        $lower = $dt->format('Y-m-d H:i:s');
        $dt->modify('+1 hour');
        $upper = $dt->format('Y-m-d H:i:s');
        averageHour($lower, $upper, $link);
    }

    function averageHour($lower, $upper, $link){
        $wind_speeds = [];
        $max_gust_speed = 0;
        $wind_directions = [];
        $rainfall = 0;
        $ambient_temps = [];
        $ground_temps = [];
        $humiditys = [];
        $pressures = [];
        $powers = [];

        $sql = "SELECT *, datetime FROM tbl_weather WHERE datetime BETWEEN '$lower' AND '$upper'";
        $result = $link->query($sql);
        // output data of each row
        while($row = $result->fetch_assoc()) {

            $wind_speeds[] = $row['wind_speed'];
            $gust_speed = $row['gust_speed'];
            if ($gust_speed > $max_gust_speed) $max_gust_speed = $gust_speed;
            switch ($row['wind_direction']) { //creates an array of wind directions in degrees, which can then be averaged for the hour
                case 'N':
                    $wind_directions[] = 0;
                    break;
                case 'NNE':
                    $wind_directions[] = 22.5;
                    break;
                case 'NE':
                    $wind_directions[] = 45;
                    break;
                case 'ENE':
                    $wind_directions[] = 67.5;
                    break;
                case 'E':
                    $wind_directions[] = 90;
                    break;
                case 'ESE':
                    $wind_directions[] = 112.5;
                    break;
                case 'SE':
                    $wind_directions[] = 135;
                    break;
                case 'SSE':
                    $wind_directions[] = 157.5;
                    break;
                case 'S':
                    $wind_directions[] = 180;
                    break;
                case 'SSW':
                    $wind_directions[] = 202.5;
                    break;
                case 'SW':
                    $wind_directions[] = 225;
                    break;
                case 'WSW':
                    $wind_directions[] = 247.5;
                    break;
                case 'W':
                    $wind_directions[] = 270;
                    break;
                case 'WNW':
                    $wind_directions[] = 292.5;
                    break;
                case 'NW':
                    $wind_directions[] = 315;
                    break;
                case 'NNW':
                    $wind_directions[] = 337.5;
                    break;
            };
            $rainfall += $row['rainfall'];
            $ambient_temps[] = $row['ambient_temp'];
            $ground_temps[] = $row['ground_temp'];
            $humiditys[] = $row['humidity'];
            $pressures[] = $row['pressure'];
            $powers[] = $row['power'];

        }

        $av_wind_speed = (count($wind_speeds)) ? array_sum($wind_speeds)/count($wind_speeds) : -1;
        $av_wind_direction = (count($wind_directions)) ? get_average_wind_direction($wind_directions) : -1;
        $av_ambient_temp = (count($ambient_temps)) ? array_sum($ambient_temps)/count($ambient_temps) : -1;
        $av_ground_temp = (count($ground_temps)) ? array_sum($ground_temps)/count($ground_temps) : -1;
        $av_humidity = (count($humiditys)) ? array_sum($humiditys)/count($humiditys) : -1;
        $av_pressure = (count($pressures)) ? array_sum($pressures)/count($pressures) : -1;
        $av_power = (count($powers)) ? array_sum($powers)/count($powers) : -1;

        /*
        echo "<hr><strong>Averaging data for time period from " . $lower . " to " . $upper . ".</strong><br>";
        echo "av_wind_speed: " . $av_wind_speed . "; ";
        echo "max_gust_speed: " . $max_gust_speed . "; ";
        echo "av_wind_direction: " . $av_wind_direction . "; ";
        echo "rainfall: " . $rainfall . "; ";
        echo "av_ambient_temp: " . $av_ambient_temp . "; ";
        echo "av_ground_temp: " . $av_ground_temp . "; ";
        echo "av_humidity: " . $av_humidity . "; ";
        echo "av_pressure: " . $av_pressure . "; ";
        echo "av_power: " . $av_power . "; ";
        echo "datetime: " . $lower . "; ";
        */
        

        $stmt = $link->prepare("INSERT INTO tbl_weather_hourly (wind_speed, gust_speed, wind_direction, rainfall, ambient_temp, ground_temp, humidity, pressure, power, datetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ( false===$stmt ) {
            die(
                '{
                    "status":' . http_response_code(500) . ',
                    "msg": "Weather hourly data could not be logged. The server returned the following error message: prepare() failed: "' . mysqli_error($link) .'
                }'
            );
        }
        $rc = $stmt->bind_param("ssssssssss", $av_wind_speed, $max_gust_speed, $av_wind_direction, $rainfall, $av_ambient_temp, $av_ground_temp, $av_humidity, $av_pressure, $av_power, $lower);
        if ( false===$rc ) {
            die(
                '{
                    "status":' . http_response_code(500) . ',
                    "msg": "Weather data could not be logged. The server returned the following error message: bind_param() failed: "' . mysqli_error($link) . '
                }'
            );
        }
        $stmt->execute();

        $stmt->close();
    }

    function get_average_wind_direction($angles){
        // Calculate the average value of a list of angles. Return 0 if empty list
        // Return the wind direction in 22.5 degree intervals
        $av_wind_direction = 0.0;

        $sin_sum = 0.0;
        $cos_sum = 0.0;

        foreach ($angles as $angle){
            $radians = deg2rad($angle);
            $sin_sum += sin($radians);
            $cos_sum += cos($radians);
        }

        $sin = $sin_sum / count($angles);
        $cos = $cos_sum / count($angles);
        if ($cos == 0.0){ // prevent division by zero in atan
            if ($sin > 0.0){
                $av_wind_direction = 90.0;
            } else {
                $av_wind_direction = 270.0;
            }
        } else {
            $arc = rad2deg(atan($sin / $cos));
            $av_wind_direction = 0.0;

            if ($sin >= 0.0 && $cos >= 0.0){ // 0 - 90
                $av_wind_direction = $arc;
            } elseif ($cos < 0.0){    // 90 - 270
                $av_wind_direction = 180.0 + $arc;
            } else { // s <= 0 and c >0  //270 - 360
                $av_wind_direction = 360.0 + $arc;
            }
                
            $av_wind_direction = round($av_wind_direction / 22.5) * 22.5;
            if ($av_wind_direction == 360.0){
                $av_wind_direction = 0.0;
            }
        }

        switch ($av_wind_direction){
            case 0.0:
                $av_wind_direction = "N";
                break;
            case 22.5:
                $av_wind_direction = "NNE";
                break;
            case 45.0:
                $av_wind_direction = "NE";
                break;
            case 67.5:
                $av_wind_direction = "ENE";
                break;
            case 90.0:
                $av_wind_direction = "E";
                break;
            case 112.5:
                $av_wind_direction = "ESE";
                break;
            case 135.0:
                $av_wind_direction = "SE";
                break;
            case 157.5:
                $av_wind_direction = "SSE";
                break;
            case 180.0:
                $av_wind_direction = "S";
                break;
            case 202.5:
                $av_wind_direction = "SSW";
                break;
            case 225.0:
                $av_wind_direction = "SW";
                break;
            case 247.5:
                $av_wind_direction = "WSW";
                break;
            case 270.0:
                $av_wind_direction = "W";
                break;
            case 292.5:
                $av_wind_direction = "WNW";
                break;
            case 315.0:
                $av_wind_direction = "NW";
                break;
            case 337.5:
                $av_wind_direction = "NNW";
                break;
            default:
                error_log("Wind direction error: " . $av_wind_direction);
                $av_wind_direction = -1;
        }
        
        return $av_wind_direction;
    }

    $link->close();
?>