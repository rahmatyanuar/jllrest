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
	//creating a statement to insert to database 
	//$stmt = $conn->prepare("INSERT INTO names (name) VALUES (?)");
	$getQuery = "SELECT a.id,a.bgr_id,a.tnt_loc_name, billing.kwh as kwh FROM `bergainser_tbl` as a left join (SELECT s1.*
		FROM billing_tbl as s1
		LEFT JOIN billing_tbl AS s2
		     ON s1.bill_id = s2.bill_id ORDER BY s1.kwh DESC LIMIT 1) as billing on a.id = billing.bgr_id";
	$getQueryCustomer = "SELECT customerNumber,customerName FROM `customers`";
	$getQueryKaryawan = "SELECT employeeNumber, CONCAT(firstName,' ',lastName) AS name FROM `employees`";
	$getQueryTenant = "SELECT tnt_name FROM `tenant_tbl`";
	$getQueryKwh = "SELECT bill_id,bgr_id,bill_date,kwh,tnt_id,customerNumber,employeeNumber,total,signature_data FROM billing_tbl";
		
	//if data inserts successfully
	$tenantID = null;
	if($result = $conn->query($getQuery)){
		//making success response 
		$response['error'] = false; 
		$response['message'] = 'Nama Meteran Load successfully'; 
		//$response['result']= mysqli_fetch_row($stmt);
		$i = 0;
		while ($row = $result->fetch_row()) {
			$data[$i]['id'] = $row[0];
			$data[$i]['nama_meteran'] = $row[1];
			$data[$i]['tnt_loc_name'] = $row[2];
			$data[$i]['kwh'] = $row[3];
			$i++;
		}
		$response['data'] = $data;
		
		$i = 0;
		$data = null;
		if($result = $conn->query($getQueryCustomer)){
			while ($row = $result->fetch_row()) {
				$data[$i]['id'] = $row[0];
				$data[$i]['nama_customer'] = $row[1];
				$i++;
			}
		}
		$response['data_customer'] = $data;
		
		$i = 0;
		$data = null;
		if($result = $conn->query($getQueryKaryawan)){
			while ($row = $result->fetch_row()) {
				$data[$i]['id'] = $row[0];
				$data[$i]['nama_karyawan'] = $row[1];
				$i++;
			}
		}
		$response['data_karyawan'] = $data;
		
		$i = 0;
		$data = null;
		if($result = $conn->query($getQueryTenant)){
			while ($row = $result->fetch_row()) {
				$data[$i]['tnt_name'] = $row[0];
				$i++;
			}
		}
		$response['data_tenant'] = $data;
		
		$i = 0;
		$data = null;
		if($result = $conn->query($getQueryKwh)){
			while ($row = $result->fetch_row()) {
				$data[$i]['kwh_id'] = $row[0];
				$data[$i]['meteran_id'] = $row[1];
				$data[$i]['bill_date'] = $row[2];
				$data[$i]['kwh'] = $row[3];
				$data[$i]['tenant_id'] = $row[4];
				$data[$i]['customer_no'] = $row[5];
				$data[$i]['employee_no'] = $row[6];
				$data[$i]['kwh_total'] = $row[7];
				$data[$i]['signature'] = $row[8];
				$i++;
			}
		}
		
		if($result->num_rows<1){
			$response['kwh_total']=array();
		} else {
			$response['kwh_total'] = $data;
		}
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