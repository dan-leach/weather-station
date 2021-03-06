<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panborough Weather Station</title>
    <!--favicons-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <!--bootstrap5-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>

    <!--moment js-->
    <script src="dependencies/moment.js"></script>

    <!--sweetalert2-->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!--chart.js-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"></script>
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="centered">
            <h1 class="title display-4">Panborough Weather Station</h1>
        </div>
        <div class="status centered">
            <p id="status" class="no-wrap"></p>
            <button type="button" id="toggleAutoReload" class="btn btn-sm btn-success" onClick="ui.reload.toggle()">Auto-reload: ON</button>
        </div>
        <p class="errors centered" id="error"></p>
        <hr>
        <?php include 'html/monitor.html'; ?>
        <hr>
        <?php include 'html/graphs.html'; ?>
        <hr>
    </div>
    <?php include 'html/log.html'; ?>
    
    <script src="js/main.js"></script>
    <script src="js/graphs.js"></script>
</body>
</html>