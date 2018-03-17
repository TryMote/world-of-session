<?php
	require_once 'salt.php';
	require_once 'db_data.php';
	include_once 'error_page_func.php';
	
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	
	$first_name = fix_string($conn, $_POST['first_name']);
	$first_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$first_name)));
	$first_name = ucfirst($first_name);

	$last_name = fix_string($conn, $_POST['last_name']);
	$last_name = mb_strtolower(trim(preg_replace('~[^A-Za-z]+~','',$last_name)));
	$last_name = ucfirst($last_name);

	$login = trim(fix_string($conn, $_POST['login']));
	if(preg_match('~[^a-zA-Z0-9_-]+~', $login) || strlen($login) >= 30) error_page('suw_l');
	$email = trim(fix_string($conn, $_POST['email']));
	if(!preg_match('~.+@.+\..+~i', $email))	error_page('suw_e');	
	$query = "SELECT user_id FROM $s_i WHERE nickname='$login' OR email='$email'";
	$result = $conn->query($query);
	$id = mysqli_fetch_row($result);
	if($id[0]) error_page('sue_lore');

	$pass = fix_string($conn, $_POST['pass']);
	$r_pass = fix_string($conn, $_POST['pass']);
	if(strlen($pass) >= 6 && strlen($pass) <= 30 && $pass == $r_pass ) {
		$pass = crypt($pass, 'ls');
	} else {
		error_page('suw_p');
	}

	if(array_key_exists('gender',$_POST)) {
		$gender = fix_string($conn, $_POST['gender']);
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
	$conn->close();
?>
