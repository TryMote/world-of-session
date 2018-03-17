<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	require_once 't_names.php';
	include_once 'error_page_func.php';

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) error_page('ssp_c8');

	$query = "INSERT INTO $upd VALUES(NULL, '$first_name', '$last_name', '$email')";
	$result = $conn->query($query);
	if(!$result) error_page('ssi_upd12');
	$insertID = $conn->insert_id;
	
	$query = "INSERT INTO $usd(user_id) VALUES($insertID)";
	$result = $conn->query($query);
	if(!$result) error_page('ssi_usd17');  	

	$query = "INSERT INTO $s_i(user_id, email, nickname, password) VALUES($insertID,'$email', '$login', '$pass')";
	$result = $conn->query($query);
	if(!$result) error_page('ssi_si21');
	
	$conn->close();
	header("Location: https://localhost/wos/index.php");
?>
