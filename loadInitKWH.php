<?php
include 'dbConn.php';
include 'checkAuth.php';

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET');


//Connecting to the database
$conn = OpenCon();

//HTTP Auth
CheckAuth($conn);

//checking the successful connection
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='GET'){
	$getQuery = 'SELECT bgr_id, kwh, bill_date, tnt_id, customerNumber,employeeNumber FROM billing_tbl GROUP by bgr_id
					ORDER BY `billing_tbl`.`bgr_id` ASC ';
	if($result = $conn->query($getQuery)){
		$response['error'] = false; 
		$response['message'] = 'KWH Load successfully'; 
		$i = 0;
		while ($row = $result->fetch_row()) {
			$data[$i]['bgr_id'] = $row[0];
			$data[$i]['kwh'] = $row[1];
			$data[$i]['bill_date'] = $row[2];
			$data[$i]['tnt_id'] = $row[3];
			$data[$i]['customer_id'] = $row[4];
			$data[$i]['employee_no'] = $row[5];
			$i++;
		}
		$response['data'] = $data;
	}
}else{
	$response['error'] = true; 
	$response['message'] = "Invalid request"; 
}
echo json_encode($response);
CloseCon($conn);