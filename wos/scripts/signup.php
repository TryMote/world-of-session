<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	require_once 't_names.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->error) die($conn->error);
	$query = "INSERT INTO $upd(first_name, last_name, email) VALUES(
				'$first_name', '$last_name', '$email')";
	$result = $conn->query($query);
	if(!$result) die ($conn->error);
	
	$user_id = "SELECT user_id FROM $upd WHERE email='$email'";
	$query = "INSERT INTO $usd(user_id) $user_id";
	$result = $conn->query($query);
	if(!$result) die ($conn->error);
?>
