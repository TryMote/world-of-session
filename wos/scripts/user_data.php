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

	if(array_key_exists('gender',$_POST)) {
		$gender = $_POST['gender'];
	}

	$image_name = "default.png"; 
/*	if(!empty($_FILES)) {
		$image_name = substr($email, 0, 2)."_".strtolower($first_name)."_".strtolower($last_name)."_wos";	
		if(preg_match('~(image)/(jpeg)|(png)|(gif)~', $_FILES['image']['type'])) {
			preg_match('~\.[a-z]+$~', $_FILES['image']['name'], $extension);
			$directory = "~/.git/world-of-session/wos/scripts";
			move_uploaded_file($_FILES['image']['tmp_name'], "$directory");
			die();
			$image_name$extension[0];
		}
	}*/
?>
