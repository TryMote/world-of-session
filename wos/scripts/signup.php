<?php
	require_once 'db_data.php';
	require_once 'user_data.php';
	include_once 'error_page_func.php';

	$conn = get_connection_object();
	
	get_first_query_result($conn, "LOCK TABLES user_primary_data WRITE");
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

	$query = "INSERT INTO $s_i(user_id, email, nickname, profile_link, password) VALUES(?,?,?,?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_si');
	$result->bind_param('issss', $insertID, $email, $login, $profile_link, $pass);
	$result->execute();
	if(!$result->affected_rows) error_page('srn_rsi');
	$result->close();	
	$conn->close();
	$_SESSION['in'] = 1;
	$_SESSION['user_id'] = $id;
	header("Location: http://localhost/wos/users/".$profile_link); 
/*
	require_once 'sender.php';
	send_mail($email, 'trymote@mail.ru', '', 1);
*/
?>
