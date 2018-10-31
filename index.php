<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(1000);
error_reporting(E_ALL);

include_once 'db_config.php';
include_once 'general_functions.php';

$start = 0;
$end = 1000;

if(isset($_GET["table_name"]) && $_GET["table_name"]){
	$table_name = $_GET["table_name"];
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
	$get_table_data = "select * from ".$table_name." limit ".$start.", ".$end;
	$result=mysqli_query($redpa_old_conn,$get_table_data);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$columns = array_keys($row);
			$values = array_values($row);
			$valuesArr = [];
			foreach ($values as $val) {
				if(isset($val)){
					array_push($valuesArr, "'".$redpa_new_conn->real_escape_string($val)."'");
				}else{
					array_push($valuesArr, 'NULL');
				}
			}

			$insert_query = "INSERT INTO `$table_name` (".implode(",", $columns).") VALUES (".implode(",", $valuesArr).")";
			try{
				$suc_insert_qry = $redpa_new_conn->query($insert_query);
				if(!$suc_insert_qry){
					$msg = ["error"=>$redpa_new_conn->error, "query" => $insert_query];
					wh_log(json_encode($msg),$table_name);	
				}
			} catch (\Exception $e) {
				$msg = ["error"=>$e->getMessage(), "query" => $insert_query];
				wh_log(json_encode($msg),$table_name);
			}
		}
	}
	$redpa_old_conn->close();
	$redpa_new_conn->close();
	header("Location: http://localhost/databasemigrationscript/?table_name=".$table_name);
	exit;

}else{
	throw new Exception("Please provide table name in query string \"table_name\"", 1);	
}
exit("Here");

?>
