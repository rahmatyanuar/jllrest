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
		$getQueryCustomer = "SELECT u.first_name, u.password FROM users as u where u.first_name = ? AND u.is_active = 1";
		if($result = $conn->execute_query($getQueryCustomer,[$username])){
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