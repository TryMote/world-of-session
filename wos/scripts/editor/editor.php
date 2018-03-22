<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
</head>
<body>
	<?php 
		require_once "../db_data.php";
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		
		include_once "subject_selection.php";
		subject_page_work($conn);
		
		if(isset($_POST['select_subject'])) {
			include_once "topic_selection.php";
			topic_page_work($_POST['subject_selection'], $conn);
		}
		if(isset($_POST['select_topic']) && $_POST['topic_selection']) {
			include_once "lection_selection.php";
			lection_page_work($_POST['topic_selection'], $conn);
		}
		if(isset($_POST['delete_subject'])) {
			$del_subject_id = $_POST['subject_selection'];
			$result = $conn->query("SELECT subject_name FROM subjects WHERE subject_id='$del_subject_id'");
			if(!$result) die($conn->connect_error);
			$subject_name = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			echo "<br><form action='editor.php' method='POST'>
				<label style='color:#f00' for='force_delete_subject'>Предмет <b>'$subject_name[0]'</b> и все входящие в него лекции и темы будут безвозвратно удалены!</label><br>
				<input type='password' name='pass' placeholder='Ключ' required>
				<input type='submit' name='force_delete_subject' value='Удалить'>
				<input type='text' name='del_subject_id' value='$del_subject_id' style='display:none'> 
				<input type='submit' name='cancel' value='Отменить'>
				</form>";
		}
		if(isset($_POST['delete_topic'])) {
			$del_topic_id = $_POST['topic_selection'];
			$result = $conn->query("SELECT topic_name FROM topics WHERE topic_id='$del_topic_id'");
			if(!$result) die($conn->connect_error);
			$topic_name = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			echo "<br><form action='editor.php' method='POST'>
				<label style='color:#f00' for='force_delete_topic'>Тема <b>'$topic_name[0]'</b> и все входящие в нее лекции будут безвозвратно удалены!</label><br>
				<input type='password' name='pass' placeholder='Ключ' required>
				<input type='submit' name='force_delete_topic' value='Удалить'>
				<input type='text' name='del_topic_id' value='$del_topic_id' style='display:none'> 
				<input type='submit' name='cancel' value='Отменить'>
				</form>";
	
		}
		if(isset($_POST['delete_lection'])) {
			$del_lection_id = $_POST['lection_selection'];
			$result = $conn->query("SELECT lection_name FROM lections WHERE lection_id='$del_lection_id'");
			if(!$result) die($conn->connect_error);
			$lection_name = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			echo "<br><form action='editor.php' method='POST'>
				<label style='color:#f00' for='force_delete_lection'>Лекция <b>'$lection_name[0]'</b> будет безвозвратно удалена!</label><br>
				<input type='password' name='pass' placeholder='Ключ' required>
				<input type='submit' name='force_delete_lection' value='Удалить'>
				<input type='text' name='del_lection_id' value='$del_lection_id' style='display:none'> 
				<input type='submit' name='cancel' value='Отменить'>
				</form>";
	
		}
		if(isset($_POST['force_delete_subject'])) {
			$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
			if(!$is_right) die();
			$del_subject_id = fix_string($conn,trim( $_POST['del_subject_id']));
			$query = "SELECT topic_id FROM topics WHERE subject_id='$del_subject_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row_number = $result->num_rows;
			for($i=0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_NUM);
				$query = "DELETE FROM lections WHERE topic_id=$row[0]";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
				$query = "DELETE FROM topics WHERE topic_id=$row[0]";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
			}
			$query = "DELETE FROM subjects WHERE subject_id='$del_subject_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			echo "<p>Удаление прошло успешно!</p><br>";
			 
		} 

		if(isset($_POST['force_delete_topic'])) {
			$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
			if(!$is_right) die();
			$del_topic_id = fix_string($conn,trim( $_POST['del_topic_id']));
			$query = "SELECT lection_id FROM lections WHERE topic_id='$del_topic_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row_number = $result->num_rows;
			for($i=0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_NUM);
				$query = "DELETE FROM lections WHERE topic_id=$row[0]";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
			}
			$query = "DELETE FROM topics WHERE topic_id='$del_topic_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			echo "<p>Удаление прошло успешно!</p><br>";

		}
		if(isset($_POST['force_delete_lection'])) {
			$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
			if(!$is_right) die();
			$del_lection_id = fix_string($conn,trim( $_POST['del_lection_id']));
			$query = "DELETE FROM lections WHERE lection_id='$del_lection_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			echo "<p>Удаление прошло успешно!</p><br>";

		}	

		$conn->close();

	function check_admin($conn, $pass) {
		$result = $conn->query("SELECT password FROM sign_in WHERE user_id=1");
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!hash_equals($row[0], crypt($pass, $row[0]))) {
			die("Неверный пароль");
		} else {
			return true;
		}
	} 
	?>
</body>
</html>

