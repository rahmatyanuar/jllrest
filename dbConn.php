<?php
function OpenCon(){
 $dbhost = "172.17.0.2";
 $dbuser = "jll";
 $dbpass = "C0b4d1b4c4";
 $db = "kwh";
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
 //checking the successful connection
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
 
 return $conn;
}
 
function CloseCon($conn){
 $conn -> close();
}
   
?>