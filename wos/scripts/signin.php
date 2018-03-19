<?php
	require_once 'db_data.php';
	include_once 'error_page_func.php';
	
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) error_page('sie_c');
	$conn->query("SET NAMES 'utf8'");

	$login = trim(fix_string($conn, $_POST['login']));
        if((!preg_match('~[a-zA-Z0-9_-]+~', $login) || !preg_match('~.+@.+\..+~i', $login))
			&& strlen($login) >= 30) error_page('siw_l');
	$query = "SELECT user_id FROM $s_i WHERE nickname='$login' OR email='$login'";
        $result = $conn->query($query);
	if(!$result) error_page('sis_id');
     	$user_id = mysqli_fetch_row($result);
       	if(!$user_id[0]) error_page('sin_u');		
	$query = "SELECT password FROM $s_i WHERE user_id='$user_id[0]'";
	$result = $conn->query($query);
	if(!$result) error_page('sis_p');
	$id = mysqli_fetch_row($result);
	$pass = $id[0];
	if(!$pass) error_page('sin_p');
	$i_pass = fix_string($conn, $_POST['pass']);
	if(hash_equals($pass, crypt($i_pass, $pass))) {
		$query = "SELECT email_ver FROM $upd WHERE user_id='$user_id[0]'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$result->data_seek(0);
		$is_email_ver = $result->fetch_array(MYSQLI_NUM);

		if($is_email_ver == 1) {
			echo "You are in!<br>";
			$query = "SELECT first_name FROM $upd WHERE user_id='$user_id[0]'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$result->data_seek(0);
			$first_name = $result->fetch_array(MYSQLI_NUM);
			echo $first_name[0];
		} else {
			echo "Подтвердите свою электронную почту";
		}
	$result->close();
	$conn->close();	
	} else {
		error_page('wrong_password');
	}
?>
