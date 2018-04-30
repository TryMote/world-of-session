<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	include_once 'error_page_func.php';

	$data = get_connection_object('');

	$query = "LOCK TABLES $upd WRITE";
	$result = $conn->query($query);
	if(!$result) error_page('srl_upd');
	$query = "INSERT INTO $upd(first_name, last_name) VALUES(?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_upd');
	$result->bind_param('ss', $first_name, $last_name);
	$result->execute();
	if(!$result->affected_rows) error_page('srn_rupd');
	$insertID = $conn->insert_id;
	$query = "UNLOCK TABLES";
	$result = $conn->query($query);
	if(!$result) error_page('srul_upd');
	
	$query = "INSERT INTO $usd(user_id, image, gender) VALUES(?,?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_usd');
	if(!$gender) $gender = NULL;  	
	$result->bind_param('iss',$insertID, $image_name, $gender);
	$result->execute();
	if(!$result->affected_rows) error_page('srn_rusd');

	$query = "INSERT INTO $s_i(user_id, email, nickname, password) VALUES(?,?,?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_si');
	$result->bind_param('isss', $insertID, $email, $login, $pass);
	$result->execute();
	if(!$result->affected_rows) error_page('srn_rsi');
	
	$result->close();	
	$conn->close();
	require_once 'sender.php';
	send_mail($email, 'trymote@mail.ru', '', 1);
	
	header("Location: http://localhost/wos/email_ver.php");
?>
