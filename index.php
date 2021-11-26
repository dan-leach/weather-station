<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mike's Weather Station</title>
    <!--favicons-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <!--bootstrap5-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="centered">Welcome to Mike's Weather Station</h1>
        <div class="centered">
            <button type="button" id="toggleAutoReload" class="btn btn-success" onClick="ui.reload.toggle()">Auto-reload: ON</button>
            <button type="button" id="clearLog" class="btn btn-danger" onClick="api.delete.all()">Clear log</button>
        </div>
        <hr>
        <div class="centered">
            <div class="d-flex flex-row justify-content-center">
                <div class="d-flex flex-column p-2 mx-2">
                    <h4>Temperature (&#176;C)</h4>
                    <p>Air <span id="ambient_temp"></span> | Gnd <span id="ground_temp"></span></p>
                </div>
                <div class="d-flex flex-column p-2 mx-22">
                    <h4>Pressure (mBar)</h4>
                    <p><span id="pressure"></span></p>
                </div>
                <div class="d-flex flex-column p-2 mx-2">
                    <h4>Humidity (%)</h4>
                    <p><span id="humidity"></span></p>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-center">
                <div class="d-flex flex-column p-2 mx-2">
                    <h4>Wind Speed (km/h)</h4>
                    <p><span id="wind_speed"></span> (Gusting <span id="gust_speed"></span>)</p>
                </div>
                <div class="d-flex flex-column p-2 mx-22">
                    <h4>Wind Direction</h4>
                    <p><span id="wind_direction"></span></p>
                </div>
                <div class="d-flex flex-column p-2 mx-2">
                    <h4>Rainfall (mm)</h4>
                    <p><span id="rainfall"></span></p>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-center">
                <div class="d-flex flex-column p-2 mx-2">
                    <p>Updated: <span id="datetime"></span></p>
                </div>
                <div class="d-flex flex-column p-2 mx-22">
                    <p>Version: <span id="version"></span></p>
                </div>
                <div class="d-flex flex-column p-2 mx-2">
                    <p>Comment: <span id="comment"></span></p>
                </div>
            </div>
        </div>
        <hr>
        <h2>Log:</h2>
        <div id="log" class="log"></div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>