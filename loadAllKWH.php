<?php
include 'dbConn.php';
include 'checkAuth.php';

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET');


//Connecting to the database
$conn = OpenCon();

//HTTP Auth
CheckAuth($conn);

//making an array to store the response
$response = array(); 

//if there is a post request move ahead 
if($_SERVER['REQUEST_METHOD']=='GET'){
	error_reporting(E_ERROR | E_PARSE);
	//creating a statement to insert to database 
	$getQueryKwh = "SELECT a.bill_id, b.bgr_id ,a.report_date,max(a.kwh) FROM `billing_tbl` as a left join bergainser_tbl as b on a.bgr_id = b.id group by a.bgr_id";
	//if data inserts successfully
	$idMeteran = null;
	if($result = $conn->query($getQueryKwh)){
		//making success response 
		$response['error'] = false; 
		$response['message'] = 'Last KWH Counter Loaded'; 
		$i = 0;
		while ($row = $result->fetch_row()) {
			$data[$i]['bill_id'] = $row[0];
			$data[$i]['bgr_id'] = $row[1];
			$data[$i]['report_date'] = $row[2];
			$data[$i]['kwh'] = $row[3];
			$data[$i]['status'] = $row[4];
			$i++;
		}
		$response['data_all_kwh'] = $data;
		
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