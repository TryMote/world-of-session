<?php
	require_once 'salt.php';
	$first_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$_POST['first_name'])));
	$first_name = ucfirst($first_name);
	$last_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$_POST['last_name'])));
	$last_name = ucfirst($last_name);
	$login = trim($_POST['login']);
	$email = trim($_POST['email']);
	if(strlen($_POST['pas']) > 6) {
		$pas = crypt($_POST['pas'], $salt);
		echo $pas;
	} else {
		echo "To low pass";
	}
?>
