<?php
function CheckAuth($conn){
	//HTTP Auth
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		exit;
	} else {
		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		$getQueryCustomer = 'CALL kwh.getAuth()';
		if($result = $conn->query($getQueryCustomer)){
		    while ($row = $result->fetch_row()) {
		        if(password_verify($_SERVER['PHP_AUTH_PW'], $row[1])==false){
		            header("HTTP/1.1 401 Unauthorized");
		            CloseCon($conn);
		            exit;
		        }
		    }
		}
	}
}