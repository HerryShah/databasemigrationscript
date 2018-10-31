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

$table_name = 'leads';

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
	
	$get_table_data = "select * from ".$table_name." limit ".$start.", ".$end;
	$result=mysqli_query($redpa_old_conn,$get_table_data);

	//$insert_qry = "INSERT INTO ".$table_name." (".implode(",", $columns_array).") values ";

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
	    	$valuesArrString = '';
	    	$valuesArrString = "(".implode(',', $dyn_columns_array[$key_outer]).")";
	    	
	    	$insert_qry123 = "INSERT INTO ".$table_name." (".implode(",", $columns_array).") values ";
	    	$insert_qry = $insert_qry123 . $valuesArrString;
	    	
			$suc_insert_qry = $redpa_new_conn->query($insert_qry);

			if(!$suc_insert_qry){
				$msg = $insert_qry;	
				wh_log($msg,$table_name);
			}

		}
	} else {
	    $msg = '0 results';
		throw new Exception($msg,0);
	}
	
	/*$insert_qry .= implode(',', $valuesArr);
	$suc_insert_qry = $redpa_new_conn->query($insert_qry);
	$data['executed_query'] = $insert_qry;
	if(!$suc_insert_qry){
		$msg = 'error in query';	
		throw new Exception(mysqli_error($redpa_new_conn),0);
	}*/
	$new_db_record_qry = "select count(*) as cnt_records from ".$table_name;
	$result_new_db_record=mysqli_query($redpa_new_conn,$new_db_record_qry);
	$new_db_records=mysqli_fetch_array($result_new_db_record);
	$new_db_records_cnt=$new_db_records['cnt_records'];

	$old_db_record_qry = "select count(*) as cnt_records from ".$table_name;
	$result_old_db_record=mysqli_query($redpa_old_conn,$old_db_record_qry);
	$old_db_records=mysqli_fetch_array($result_old_db_record);
	$old_db_records_cnt=$old_db_records['cnt_records'];	

	if($new_db_records_cnt < $old_db_records_cnt){

		header("Location: " . "http://" . $_SERVER['HTTP_HOST'] . "/mysql_script/ajax_data.php");
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
