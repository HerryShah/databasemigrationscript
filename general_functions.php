<?php
function rand_string( $length ) {
	$str ='';
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

	$size = strlen( $chars );
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}

	return $str;
}
function wh_log($log_message,$table_name){

	$log_filename = "log";
	if (!file_exists($log_filename)) 
	{
	    // create directory/folder uploads.
	    mkdir($log_filename, 0777, true);
	}
	$log_file_data = $log_filename.'/' .$table_name . '.log';
	file_put_contents($log_file_data, $log_message . "\n", FILE_APPEND);
}
?>