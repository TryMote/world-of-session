<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	include_once 'error_page_func.php';

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) error_page('srp_c');

	$query = "LOCK TABLES $upd WRITE";
	$result = $conn->query($query);
	if(!$result) error_page('srl_upd');
	$query = "INSERT INTO $upd(first_name, last_name, email) VALUES(?,?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_upd');
	$result->bind_param('sss', $first_name, $last_name, $email);
	$result->execute();
	if(!$result->affected_rows) error_page('srn_rupd');
	$insertID = $conn->insert_id;
	$query = "UNLOCK TABLES";
	$result = $conn->query($query);
	if(!$result) error_page('srul_upd');
	
	$query = "INSERT INTO $usd(user_id, image) VALUES(?,?)";
	$result = $conn->prepare($query);
	if(!$result) error_page('sri_usd');  	
	$result->bind_param('is',$insertID, $image_name);
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
	mail($email, "Верификация вашей почты", "Привет");
	echo "
<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	 <link rel='stylesheet' href='../assets/css/styles.css'>
	<title>Верификация электронной почты</title>
</head>
<body>
	<h4>Верификация e-mail</h4>
	<p>Вам на почту отправлено письмо для подтверждения электронной почты</p><br>
	<a href='../index.php'>На главную</a>
</body>
</html>";
?>
