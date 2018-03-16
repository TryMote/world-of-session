<?php
	require_once 'login.php';
	require_once 'user_data.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) {
		 die($conn->connect_error);
	} 
	$query = "INSERT INTO user_primary_data(first_name, last_name, email) VALUES('".$first_name."',
 '".$last_name."', '".$email."')";
	echo $query;

?>
