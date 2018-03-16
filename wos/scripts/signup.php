<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	require_once 't_names.php';
	include_once 'error_page_func.php';

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->error) die($conn->error);
	
	$check = "SELECT * FROM $s_i WHERE email=$email";
	$result = $conn->query($check);
	if($result) error_page($undefined_error);

	$query = "INSERT INTO $upd(first_name, last_name, email) VALUES(
				'$first_name', '$last_name', '$email')";
	$result = $conn->query($query);
	if(!$result) error_page($ex_email_error);
	
	$query = "INSERT INTO $usd(photo) VALUES('link on photo')";
	$result = $conn->query($query);
	if(!$result) die($conn->error);  	

	$query = "INSERT INTO $s_i(email, nickname, password) VALUES('$email', '$login', '$pass')";
	$result = $conn->query($query);
	if(!$result) error_page($ex_email_error);
	
	$conn->close();
	header("Location: https://localhost/wos/index.php");
?>
