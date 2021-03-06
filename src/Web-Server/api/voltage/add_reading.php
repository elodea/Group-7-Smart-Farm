<?php 

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";
include_once "../../objects/voltage.php";

include '../../lib/phpMQTTpub.php';

$database = new Database();
$conn = $database->getConnection();

$voltage = new Voltage($conn);

$data = json_decode(file_get_contents('php://input'));

if (
    //Make sure we have values!
    !empty($data->id) &&
    (!empty($data->reading) || $data->reading == "0")
) {
    //add the reading
    $voltage->id = $data->id;
    $voltage->reading = $data->reading;
    date_default_timezone_set('Australia/Melbourne');
    $voltage->datetime = date("Y-m-d H:i:s");

    if($voltage->addReading()) {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "voltage reading was entered"));

    } else {    // if unable to enter the reading, tell the user
               
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to enter voltage reading"));
    }    
} else {    // tell the user data is incomplete
    
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to enter voltage reading. Data is incomplete."));
}

$conn->close();

?>