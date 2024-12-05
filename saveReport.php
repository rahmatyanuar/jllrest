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
    $reportDate = $data['report_date'];
    $tenant = $data['tenant_id'];
    $kwh = $data['total_kwh'];
    $meteran = $data['meteran_id'];
    $signatureCust = $data['customer_signature'];
    $signatureEmp = $data['employee_signature'];
    
    $pqry = "CALL kwh.insertReportProc('".$reportDate."', '".$tenant."', '".$meteran."', '".$kwh."', '".$signatureCust."', '".$signatureEmp."')";
    $conn = OpenCon();
    $qry = mysqli_query($conn,$pqry);
    
    if($qry){
        //making success response
        $response['error'] = false;
        $response['message'] = 'Report saved successfully';
    }else{
        //if not making failure response
        $response['error'] = true;
        $response['message'] = 'Report Failed To Save on Server';
        $response['details'] = $pqry;
    }
    
}else{
    $response['error'] = true;
    $response['message'] = "Invalid request";
}

//displaying the data in json format
echo json_encode($response);