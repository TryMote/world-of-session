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
				<input type='submit' name='force_delete_subject' value='Удалить'>
				<input type='text' name='del_subject_id' value='$del_subject_id' style='display:none'> 
				<input type='submit' name='cancel' value='Отменить'>
				</form>";
		}
		if(isset($_POST['force_delete_subject'])) {
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
			echo "<p>Удаление прошло успешно! Нажмите \"Обновить\", для продолжения работы</p><br>";
			 
		} 
		$conn->close();
	?>
</body>
</html>

