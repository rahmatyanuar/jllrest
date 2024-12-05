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
	
	//getting the name from request 
	//$name = $_POST['name']; 

	//creating a statement to insert to database 
	//$stmt = $conn->prepare("INSERT INTO names (name) VALUES (?)");
	$getQuery = "SELECT customerNumber,customerName FROM `customers`";
	
	//binding the parameter to statement 
	//$stmt->bind_param("s", $name);
	
	//if data inserts successfully
	if($result = $conn->query($getQuery)){
		//making success response 
		$response['error'] = false; 
		$response['message'] = 'Nama Customer Load successfully'; 
		//$response['result']= mysqli_fetch_row($stmt);
		$i = 0;
		while ($row = $result->fetch_row()) {
			$data[$i]['id'] = $row[0];
			$data[$i]['nama_customer'] = $row[1];
			$i++;
		}
		$response['data'] = $data;
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