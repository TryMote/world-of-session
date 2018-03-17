<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	include_once 'error_page_func.php';

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) error_page('srp_c');

	$query = "LOCK TABLES $upd WRITE";
	$result = $conn->query($query);
	if(!$result) error_page('srl_upd');
	$query = "INSERT INTO $upd VALUES(NULL, '$first_name', '$last_name', '$email')";
	$result = $conn->query($query);
	if(!$result) error_page('sri_upd');
	$insertID = $conn->insert_id;
	$query = "UNLOCK TABLES";
	$result = $conn->query($query);
	if(!$result) error_page('srul_upd');
	
	$query = "INSERT INTO $usd(user_id, image) VALUES($insertID, '$image_name')";
	$result = $conn->query($query);
	if(!$result) error_page('sri_usd');  	

	$query = "INSERT INTO $s_i(user_id, email, nickname, password) VALUES($insertID,'$email', '$login', '$pass')";
	$result = $conn->query($query);
	if(!$result) error_page('sri_si');
	
	$conn->close();
	header("Location: https://localhost/wos/index.php");
?>
