<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
</head>
<body>
	<?php
		
		function delete_material($conn, $item, $text_item_type) {
			$del_item_id = $_POST[$item.'_selection'];
			$query = "SELECT $item"."_name FROM $item"."s WHERE $item"."_id='$del_item_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$item_name = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			echo "<br><form action='editor.php' method='POST'>
				<label style='color:#f00' for='force_delete_$item'>$text_item_type <b> '$item_name[0]' </b> и весь входящий материал будут безвозвратно удалены!</label><br>
				<input type='password' name='pass' placeholder='Ключ' required>
				<input type='submit' name='force_delete_$item' value='Удалить'>
				<input type='text' name='del_$item"."_id' value='$del_item_id' style='display:none'>
				</form>";
		} 
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
			delete_material($conn, 'subject', 'Предмет');		
		}
		if(isset($_POST['delete_topic'])) {
			delete_material($conn, 'topic', 'Тема');		
		}
		if(isset($_POST['delete_lection'])) {
			delete_material($conn, 'lection', 'Лекция');	
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
				$query = "DELETE FROM lections WHERE topic_id='$row[0]'";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
				$query = "DELETE FROM topics WHERE topic_id='$row[0]'";
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
				$query = "DELETE FROM lections WHERE topic_id=$del_topic_id";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
			}
			$query = "DELETE FROM topics WHERE topic_id=$del_topic_id";
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
		echo "<br><form action='editor.php' method='POST'>
			<input type='submit' value='Отменить'>	
		</form>";

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

