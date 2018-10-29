<?php
$server_name='207.246.248.150';
$new_server_name='localhost';
$user_name='1026903_redpapaz';
$new_user_name='root';
$password='ddRYBajYjE9SGj7b';
$new_password='';
$database_name='1026903_redpapaz';
$database_new_name='redpapaz';
$redpa_old_conn = new mysqli($server_name, $user_name, $password,$database_name);
$redpa_new_conn = new mysqli($new_server_name, $new_user_name, $new_password,$database_new_name);
?>