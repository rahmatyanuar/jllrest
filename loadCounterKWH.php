<?php
include 'dbConn.php';
include 'checkAuth.php';

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: POST');


//Connecting to the database
$conn = OpenCon();

//HTTP Auth
CheckAuth($conn);

//making an array to store the response
$response = array();

//if there is a post request move ahead
if($_SERVER['REQUEST_METHOD']=='POST'){
    error_reporting(E_ERROR | E_PARSE);
    $dataJSON = json_decode(file_get_contents('php://input'), true);
    $meteran = $dataJSON['meteran'];
    if($meteran == null || $meteran == "null"){
        $getQueryKwh = "CALL kwh.getCounterToday()";
    }else{
        $getQueryKwh = "CALL kwh.getCounterById(".$meteran.")";
    }
    $conn = OpenCon();
    if($result = $conn->query($getQueryKwh)){
        //making success response
        $response['error'] = false;
        $response['message'] = 'Last KWH Counter Loaded';
        $i = 0;
        while ($row = $result->fetch_row()) {
            $data[$i]['id'] = $row[0];
            $data[$i]['bgr_id'] = $row[1];
            $data[$i]['report_date'] = $row[2];
            $data[$i]['kwh'] = $row[3];
            $i++;
        }
        $response['data_last_kwh'] = $data;
        CloseCon($conn);
    }else{
        //if not making failure response
        $response['error'] = true;
        $response['message'] = 'Please try later';
    }
}else{
    $response['error'] = true;
    $response['message'] = "Invalid request";
}

//displaying the data in json format
echo json_encode($response);
CloseCon($conn);