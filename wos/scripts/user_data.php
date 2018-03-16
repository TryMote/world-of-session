<?php
	$first_name =trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$login = trim($_POST['login']);
	$email = trim($_POST['email']);
	$salt = crypt("hash-salt","salt");
	$pas = crypt($_POST['pas'], $salt);
?>
