<?php
	require_once 'user_data.php';
	require_once 'db_data.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->error) die($conn->error);
	echo "Connected";
?>
