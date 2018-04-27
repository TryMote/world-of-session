<?php
	session_start();
	require_once 'db_data.php';
	include_once 'error_page_func.php';
	
	$conn = get_connection_object('');

	$login = trim(fix_string($conn, $_POST['login']));
        if((!preg_match('~[a-zA-Z0-9_-]+~', $login) || !preg_match('~.+@.+\..+~i', $login))
			&& strlen($login) >= 30) error_page('siw_l');
	$user_id = get_first_select_array($conn, "SELECT user_id FROM sign_in WHERE nickname='$login' OR email='$login'", MYSQLI_NUM)[0];
       	if(!$user_id) error_page('sin_u');		
	$pass = get_first_select_array($conn, "SELECT password FROM sign_in WHERE user_id='$user_id'", MYSQLI_NUM)[0];
	if(!$pass) error_page('sin_p');
	$i_pass = fix_string($conn, $_POST['pass']);
	if(hash_equals($pass, crypt($i_pass, $pass))) {
		$is_email_ver = get_first_select_array($conn, "SELECT email_ver FROM user_primary_data WHERE user_id='$user_id'", MYSQLI_NUM)[0];
		if($is_email_ver == 1) {
			if(isset($_SESSION['editor'])) {
				$editor = get_first_select_array($conn, "SELECT editor FROM sign_in WHERE user_id='$user_id[0]'", MYSQLI_NUM)[0];
				if($editor == 1) {
					$_SESSION['in'] = 1;
					header("Location: http://localhost/wos/scripts/editor/editor.php");
				} else {
					die("Простите, но вы не имеете прав на редактирование материала");
				}
			}
		} else {
			echo "Подтвердите свою электронную почту";
		}
	$result->close();
	$conn->close();	
	} else {
		error_page('wrong_password');
	}
?>
