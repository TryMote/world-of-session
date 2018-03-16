<?php
	require_once 'salt.php';
	include_once 'error_page_func.php';
	$first_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$_POST['first_name'])));
	$first_name = ucfirst($first_name);

	$last_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$_POST['last_name'])));
	$last_name = ucfirst($last_name);

	$login = trim($_POST['login']);
	if(!preg_match('~[a-zA-Z0-9_-]+~', $login)) {
		error_page($wr_login_error);
	}

	$email = trim($_POST['email']);
	if(!preg_match('~.+@.+\..+~i', $email)) {
		error_page($wr_email_error);	
	}

	if(strlen($_POST['pass']) >= 6) {
		$pass = crypt($_POST['pass'], $salt);
	} else {
		error_page($toshot_pass_error);
	}
	if($_POST['pass'] != $_POST['r_pass']) {
		error_page($wr_r_pass_error);
	}

	$gender = $_POST['gender'];
	

?>
