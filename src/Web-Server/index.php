<?php
    include 'lib/charting.php';
    include 'lib/utility.php';

    $tempChartData = getData("api/temperature/get_readings.php?", "temperature");
    $waterChartData = getData("api/water/get_readings.php?", "water_level");
    $voltageChartData = getData("api/voltage/get_readings.php?", "voltage");
    $moistureChartData = getData("api/soil/get_readings.php?", "moisture_level");
    
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet.css">

<!-- libs -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>

<!-- css -->
<link rel="stylesheet" href="/static/styles.css" />

<script>
    var createChart = function(containerId, title, data, chartType="line") {
        var result = new CanvasJS.Chart(containerId, {
            theme: "dark2",
            animationEnabled: true,
            title: {
                text: title
            },
            data: data
        });

        return result;
    }

    window.onload = function () {
        var tempChart = createChart("TemperatureChartContainer", "Temperature", <?php echo json_encode($tempChartData, JSON_NUMERIC_CHECK); ?>, "line");
        var moistureChart = createChart("MoistureChartContainer", "Moisture", <?php echo json_encode($moistureChartData, JSON_NUMERIC_CHECK); ?>, "line");
        var waterChart = createChart("WaterLevelChartContainer", "WaterLevel", <?php echo json_encode($waterChartData, JSON_NUMERIC_CHECK); ?>, "line");
        var voltageChart = createChart("VoltageChartContainer", "Voltage", <?php echo json_encode($voltageChartData, JSON_NUMERIC_CHECK); ?>, "line");

        tempChart.render();
        moistureChart.render();
        waterChart.render();
        voltageChart.render();
    }
</script>
   
</head>
<body>

    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="/">
            <span class="navbar-brand mb-0 h1">
                <img src="./static/icon.png" />
                Smart Farm Dashboard
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="options.php">Options</a>
                </li>
            </ul>
        </div>
        <span class="navbar-brand mb-0 h1">Group 7 - SWE-30011 IoT Programming</span>
    </nav>

    <!-- CONTENT -->
    <div class="container mx-auto p-2">
        <div class="row p-0 m-0">
            <div class="col-lg">
                <?=buildSensorTypeCard("Temperature", $tempChartData)?>
            </div>
            <div class="col-lg">
                <?=buildSensorTypeCard("Moisture", $moistureChartData)?>
            </div>
        </div>

        <div class="row p-0 m-0">
            <div class="col-lg">
                <?=buildSensorTypeCard("WaterLevel", $waterChartData)?>
            </div>
            <div class="col-lg">
                <?=buildSensorTypeCard("Voltage", $voltageChartData)?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-light footer">
        <span>Adam Knox | Jeremy Vun | Henil Patel</span>
        <span>2020</span>
    </div>
</body>
</html>