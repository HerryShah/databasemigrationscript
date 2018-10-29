<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(1000);
error_reporting(E_ALL);

include_once 'db_config.php';
include_once 'general_functions.php';

$start=0;
$end=2000;
$columns_array = [];
$valuesArr = [];
$dyn_columns_array = [];
$individual_array = [];
$return_data=[];
$data=[];
$isuccess=1;

$table_name = 'ip_addresses';

$get_coupons_code_qry = "select id from ".$table_name." order by id desc limit 1";
$result_code=mysqli_query($redpa_new_conn,$get_coupons_code_qry);

if(!empty($result_code)){
	$db_coupon_code_qry=mysqli_fetch_array($result_code);
	if($db_coupon_code_qry['id'] > 0){
		$start = $db_coupon_code_qry['id'];
	}	
}else{
	echo "0 results";exit;
}

//$start = 50;
try{

	$table_columns_qry = "select column_name from information_schema.columns where table_schema='1026903_redpapaz' AND table_name='$table_name'";
	$result_table_columns = mysqli_query($redpa_old_conn,$table_columns_qry);

	if ($result_table_columns->num_rows > 0) {
	    while($row = mysqli_fetch_array($result_table_columns)) {
	    	
	    		$columns_array[] = $row['column_name'];
	    	
	    }
	} else {
	    $msg = '0 results';
		throw new Exception($msg,0);
	}
	
	$insert_qry = "INSERT INTO ".$table_name." (".implode(",", $columns_array).") values ";

	$get_table_data = "select * from ".$table_name." limit ".$start.", ".$end;
	$result=mysqli_query($redpa_old_conn,$get_table_data);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$individual_array[] = $row;
		}
		
		foreach ($individual_array as $key_outer => $value_outer) {
			
			foreach ($columns_array as $key => $value) {
	        	
	        	if(isset($individual_array[$key_outer][$value])){
	        		
	        		$dyn_columns_array[$key_outer][] = "'".$redpa_new_conn->real_escape_string($individual_array[$key_outer][$value])."'";	
	        		
	        	}else{
	        		$dyn_columns_array[$key_outer][] = 'NULL';
	        	}
	        	
	    	}
	    	$valuesArr[] = "(".implode(',', $dyn_columns_array[$key_outer]).")";
		}
	} else {
	    $msg = '0 results';
		throw new Exception($msg,0);
	}
	
	$insert_qry .= implode(',', $valuesArr);
	$suc_insert_qry = $redpa_new_conn->query($insert_qry);
	$data['executed_query'] = $insert_qry;
	if(!$suc_insert_qry){
		$msg = 'error in query';	
		throw new Exception(mysqli_error($redpa_new_conn),0);
	}

	$msg = 'transfer data successfully completed';

}catch (Exception $e) {
   $msg = $e->getMessage();
   $isuccess=$e->getCode();
}
$return_data['message'] = $msg;
$return_data['success'] = $isuccess;
$return_data['data'] = $data;
echo '<pre>';print_r($return_data);
?>
