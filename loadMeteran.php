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
    $getQuery = "CALL kwh.getMeteranProc()";
    $getQueryCustomer = "CALL kwh.getCustomerProc()";
    $getQueryKaryawan = "CALL kwh.getEmployeeProc()";
    $getQueryTenant = "CALL kwh.getTenantProc()";
    $getQueryKwh = "CALL kwh.getbillProc()";
    $getQueryLastKWH = "CALL kwh.getCounterToday()";
    
    CloseCon($conn);
    $conn = OpenCon();
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
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid request Get Meteran";
    }
        
    CloseCon($conn);
    $conn = OpenCon();
        
    if($result = $conn->query($getQueryLastKWH)){
        $response['error'] = false;
        $response['message'] = 'Last KWH Load successfully';
        
        $i = 0;
        $data = null;
        while ($row = $result->fetch_row()) {
            $data[$i]['id'] = $row[0];
            $data[$i]['nama_meteran'] = $row[1];
            $data[$i]['tnt_loc_name'] = $row[2];
            $data[$i]['kwh'] = $row[3];
            $data[$i]['report_date'] = $row[4];
            $i++;
        }
        $response['data_last_kwh'] = $data;
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid Last KWH request";
    }
        
    CloseCon($conn);
    $conn = OpenCon();
        
    if($result = $conn->query($getQueryCustomer)){
        $response['error'] = false;
        $response['message'] = 'Get Customer Load successfully';
        
        $i = 0;
        $data = null;
        while ($row = $result->fetch_row()) {
            $data[$i]['id'] = $row[0];
            $data[$i]['nama_customer'] = $row[1];
            $i++;
        }
        $response['data_customer'] = $data;
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid Customer request ";
    }
        
    CloseCon($conn);
    $conn = OpenCon();
        
    if($result = $conn->query($getQueryKaryawan)){
        $response['error'] = false;
        $response['message'] = 'Get Karyawan Load successfully';
        
        $i = 0;
        $data = null;
        while ($row = $result->fetch_row()) {
            $data[$i]['id'] = $row[0];
            $data[$i]['nama_karyawan'] = $row[1];
            $i++;
        }
        $response['data_karyawan'] = $data;
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid Karyawan request";
    }

    CloseCon($conn);
    $conn = OpenCon();
    
    if($result = $conn->query($getQueryTenant)){
        $response['error'] = false;
        $response['message'] = 'Get Tenant Load successfully';
        
        $i = 0;
        $data = null;
        while ($row = $result->fetch_row()) {
            $data[$i]['tnt_name'] = $row[0];
            $i++;
        }
        $response['data_tenant'] = $data;
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid Tenant request";
    }

    CloseCon($conn);
    $conn = OpenCon();
        
    if($result = $conn->query($getQueryKwh)){
        $response['error'] = false;
        $response['message'] = 'Get KWH Load successfully';
        
        $i = 0;
        $data = null;
        
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
        if($result->num_rows<1){
            $response['kwh_total']=array();
        } else {
            $response['kwh_total'] = $data;
        }
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid KWH request";
    }
}else{
    //if not making failure response
    $response['error'] = true;
    $response['message'] = 'Invalid Request Method';
}


//displaying the data in json format
echo json_encode($response);
CloseCon($conn);