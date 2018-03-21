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
		if(isset($_GET['create_subject'])) {
			echo "<form action='editor.php' method='GET'>
				<br><label for='n_subject_id'>ID предмета</label>
				<input type='text' name='n_subject_id' size='5' required>
				<label for='n_subject_name'>Название предмета</label>
				<input type='text' name='n_subject_name' required>
				<input type='submit' name='insert_subject' value='Добавить предмет'><br>
			</form>";
			echo "<form action='editor.php' method='GET'>
				<input type='submit' name='cancel_creation' value='Отменить'>
			</form>";
		} elseif(isset($_GET['cancel_creation'])) {
			header("Location: editor.php");
		} elseif(isset($_GET['insert_subject'])) {
			$subject_id = fix_string($conn, trim($_GET['n_subject_id']));
			$subject_name = fix_string($conn, trim($_GET['n_subject_name']));

			// Написать проверку на то, что такие значения уже присутствуют
			// Добавить оповещение, что все материал был добавлен 

			$query = "INSERT INTO subjects VALUES(?,?)";
			$result = $conn->prepare($query);
			if(!$result) die($conn->connect_error);
			$result->bind_param('ss', $subject_id, $subject_name);
			$result->execute();
			if(!$result->affected_rows) die($conn->connect_error);
		}

// Написать описание добавления нового материала для остальных разделов

		if(isset($_GET['select_subject'])) {
			include_once "topic_selection.php";
			topic_page_work($_GET['subject_selection'], $conn);
		}
		if(isset($_GET['select_topic'])) {
			include_once "lection_selection.php";
			lection_page_work($_GET['topic_selection'], $conn);
		} 
	?>
</body>
</html>

