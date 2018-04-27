<?php 
	function check_session() {
		if(!session_start()) {
			session_start();
			$_SESSION = array();
			setcookie(session_name(), '', time() - 2592000, '/');
			session_destroy();
			session_start();	 
		}
	}

	function start_editor_session() {
		if(!isset($_SESSION['in'])) {
			$_SESSION['in'] = 0;
		}
		$_SESSION['editor'] = 1;
		if($_SESSION['in'] == 0) {
			echo "<form action='../signin.php' method='POST'>
			<input type='text' name='login' placeholder='Электронная почта или логин'><br>
			<input type='password' name='pass' placeholder='Пароль'>
			<br><input type='submit' name='okay' value='Войти'>
			</form>";
		}
	}
