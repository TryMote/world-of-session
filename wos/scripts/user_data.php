<?php
	require_once 'db_data.php';
	require_once 'generator.php';
	include_once 'error_page_func.php';

	$data = get_db_data('');	
	$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
	$conn->query("SET NAMES 'utf8'");
	if($conn->connect_error) die($conn->connect_error);
	
	$first_name = fix_string($conn, $_POST['first_name']);
	$first_name = mb_strtolower(trim(preg_replace('~[^A-Za-zА-ЯёЁ]+~iu', '', $first_name)));
	$first_name = mb_convert_case($first_name, MB_CASE_TITLE, "utf-8");

	$last_name = fix_string($conn, $_POST['last_name']);
	$last_name = mb_strtolower(trim(preg_replace('~[^A-Za-zА-Яа-яёЁ]+~iu','', $last_name)));
	$last_name = mb_convert_case($last_name, MB_CASE_TITLE, "utf-8");

	$login = trim(fix_string($conn, $_POST['login']));
	if(preg_match('~[^a-zA-Z0-9_-]+~', $login)) error_page('srw_l');
	$email = trim(fix_string($conn, $_POST['email']));
	if(!preg_match('~.+@.+\..+~i', $email))	error_page('suw_e');	
	$query = "SELECT user_id FROM $s_i WHERE nickname='$login' OR email='$email'";
	$result = $conn->query($query);
	$id = mysqli_fetch_row($result);
	if($id[0]) error_page('sue_lore');

	$pass = fix_string($conn, $_POST['pass']);
	$r_pass = fix_string($conn, $_POST['pass']);
	if(strlen($pass) >= 6 && strlen($pass) <= 30 && $pass == $r_pass ) {
		$salt = generate();
		$pass = crypt($pass, $salt);
	} else {
		error_page('suw_p');
	}
	
	$gender = "";
	if(array_key_exists('gender',$_POST)) {
		$gender = fix_string($conn, $_POST['gender']);
	}

	$image_name = "default.png"; 
	$conn->close();
?>
