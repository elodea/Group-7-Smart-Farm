<?php
    include 'lib/charting.php';
    include 'lib/utility.php';

    $params = array();
    $range = "";
    $voltageError = "false";

    // get post form parameters
    if(isset($_POST['search'])) {
        $type = $_POST['type'];
        $id = $_POST['id'];
        $id = str_replace("%", " ", $id);
        $params += array("id" => $id);

        if(!empty($_POST['startDatetime'])) {
            $start = new DateTime($_POST['startDatetime']);
            $params += array("start" => $start->format('Y-m-d H:i:s'));
        }

        if(!empty($_POST['endDatetime'])) {
            $end = new DateTime($_POST['endDatetime']);
            $params += array("end" => $end->format('Y-m-d H:i:s'));
            if(isset($params['start'])) {
                $range .= $params['start'] . "  to  " . $params['end'];
            } else {
                $range .= "Everything until " . $params['end']; 
            }
            } else {
                if(isset($params['start'])) $range = "Everything after " . $params['start'];
            }     
    
        if(empty($_POST['startDatetime']) && empty($_POST['endDatetime'])) {
            $range = "All Records";
        }

        $display = "block";
    } else {
        $display = "none";
    }

    $params = http_build_query($params);
    $chartData;
    $error = "false";

    if ($type && $id) {
        switch($type) {
            case "Temperature":
                $chartData = getData("api/temperature/get_readings.php?".$params, "temperature");
                break;
            case "Moisture":
                $chartData = getData("api/soil/get_readings.php?".$params, "moisture_level");
                break;
            case "WaterLevel":
                $chartData = getData("api/water/get_readings.php?".$params, "water_level");
                break;
        }

        $voltageChartData = getData("api/voltage/get_readings.php?".$params, "voltage");
    } else {
        $error = "true";
        // set response code - 400 bad request
        http_response_code(400);
  
        // tell the user
        echo json_encode(array("message" => "Unable to enter soil moisture reading. Data is incomplete."));
    }
    
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
        if (<?php echo $error; ?> == "true")
            return;

        var chartData = createChart("ChartContainer", "", <?php echo json_encode($chartData, JSON_NUMERIC_CHECK); ?>, "line");
        chartData.render();

        var voltageData = createChart("VoltageChartContainer", "<?php echo $type; ?>", <?php echo json_encode($voltageChartData, JSON_NUMERIC_CHECK); ?>, "line");
        voltageData.render();
    }
</script>
   
</head>
<body style="display=<?php $display ?>">

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
            <?=buildSensorTypeCard("", $chartData, false, false)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildStatCard($chartData)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildBatteryStatusCard($voltageChartData, $id)?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-light footer">
        <span>Adam Knox | Jeremy Vun | Henil Patel</span>
        <span>2020</span>
    </div>
</body>
</html>