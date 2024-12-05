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
    $data = json_decode(file_get_contents('php://input'), true);
    error_reporting(E_ERROR | E_PARSE);
    $tanggal = $data['tanggal'];
    date_default_timezone_set("Asia/Jakarta");
    $seconds = $tanggal / 1000;
    $formatted_date = date("Y-m-d H:i:s", $seconds);
    $tenant = $data['tenantID'];
    $customer = $data['customerID'];
    $kwh = $data['kwh'];
    $meteran = $data['meteranID'];
    $emp = $data['employeeID'];
    $signature = $data['signatureBitmap'];
    $total = $data['total'];
    
    $cst = intval($customer);
    $empl = intval($emp);
    $bgr = intval($meteran);
    $pqry = "CALL kwh.insertBillProc('".$formatted_date."', '".$tanggal."', '".$tenant."', '".$cst."', '".$kwh."', '".$bgr."', '".$empl."', '".$signature."', '".$total."')";
    $conn = OpenCon();
    $qry = mysqli_query($conn,$pqry);
    if($qry){
        //making success response
        $response['error'] = false;
        $response['message'] = 'KWH saved successfully';
    }else{
        //if not making failure response
        $response['error'] = true;
        $response['message'] = 'KWH Failed To Save on Server';
    }
    
}else{
    $response['error'] = true;
    $response['message'] = "Invalid request";
}

//displaying the data in json format
echo json_encode($response);
CloseCon($conn);