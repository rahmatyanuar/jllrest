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
	if($meteran != null){
		$getQueryKwh = "SELECT * FROM (SELECT a.id,a.bgr_id,a.tnt_loc_name, MAX(b.kwh) as kwh, MAX(b.bill_date) as report_date FROM `bergainser_tbl` as a left join billing_tbl as b ON a.id=b.bgr_id GROUP BY a.id,a.bgr_id,a.tnt_loc_name) as c WHERE CAST(c.report_date AS DATE) = CURRENT_DATE";
	}else{
	//creating a statement to insert to database 
		$getQueryKwh = "SELECT a.id,a.bgr_id,a.tnt_loc_name, billing.kwh as kwh FROM `bergainser_tbl` as a left join (SELECT s1.*
			FROM billing_tbl as s1
			LEFT JOIN billing_tbl AS s2
				 ON s1.bill_id = s2.bill_id ORDER BY s1.kwh DESC LIMIT 1) as billing on a.id = billing.bgr_id where a.id = ?";
	}
	//if data inserts successfully
	if($result = $conn->execute_query($getQueryKwh,[$meteran])){
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