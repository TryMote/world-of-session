<?php 
	function topic_page_work($get_subject_selection, $conn) {
	
		$selected_subject_id = $get_subject_selection;
		$query = "SELECT subject_name FROM subjects WHERE subject_id='$selected_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$selected_subject_name = $result->fetch_array(MYSQLI_NUM);
		echo "<br><p>Выбран предмет '$selected_subject_name[0]'<p><br>";
		
		$query = "SELECT * FROM topics WHERE subject_id='$selected_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if(!$row['topic_name'] && !$row['topic_id']) {
			echo "<p>Для данного предмета еще не добавлено тем</p><br>";
		} else {
			echo "<form action='editor.php' method='GET'>";
			$row_number = $result->num_rows;
			echo "<select name='topic_selection'>";
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row['topic_id']."'>".$row['topic_name']."</option>";
			}
			echo "</select>
				<input type='submit' name='select_topic' value='Выбрать тему'>
				<input type='submit' name='delete_topic' value='Удалить тему'>
			</form>";
		}
		echo "<form action='topic_selection.php' method='GET'>
			<input type='text' name='chosen_subject' value='$get_subject_selection' style='display:none'>
			<input type='submit' name='create_topic' value='Добавить новую тему'>
		</form>";
	}
	if(isset($_GET['create_topic'])) {
		echo "<!DOCTYPE html>
		<html>
		<head>
			<title>Темы</title>
			<meta charset='utf8'>
		</head>
		<body>";
		echo "<form action='topic_selection.php' method='GET'>
			<label for='n_topic_name'>Название темы</label>
			<input type='text' name='n_topic_name' required>
			<label for='topic_subject_id'>ID добавленного предмета новой темы</label>
			<input type='text' name='topic_subject_id' size='3' value='".$_GET['chosen_subject']."' required>
			<p style='font-size:9pt'>(в форме вписан ID выбранного вами предмета)</p>
			<input type='submit' name='insert_topic' value='Добавить тему'><br>
		</form>";
		echo "<form action='topic_selection.php' method='GET'>
			<input type='submit' name='cancel_creation' value='Отменить'>
		</form>";
		echo "</body></html>";
	} elseif(isset($_GET['cancel_creation'])) {
		header("Location: editor.php");
	} elseif(isset($_GET['insert_topic'])) {
		require_once '../db_data.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");

		$topic_name = fix_string($conn, trim($_GET['n_topic_name']));
		$topic_subject_id = fix_string($conn, trim($_GET['topic_subject_id']));
		$query = "SELECT subject_name FROM subjects WHERE subject_id='$topic_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die("<p>Такого ID предмета не существует</p><br>");
		$query = "SELECT * FROM topics WHERE topic_name='$topic_name' OR subject_id='$topic_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if($row[0] || $row[1] || $row[2]) {
			die("<p>Такая тема уже существует!</p><br>
			<p>Название темы должно быть уникальным для любого предмета</p><br>
			<p>Вернитель назад и повторите попытку</p>");
		}
		$query = "INSERT INTO topics(topic_name, subject_id) VALUES(?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('ss', $topic_name, $topic_subject_id);
		$result->execute();
		if(!$result->affected_rows) {
			die($conn->connect_error);
		} else {
			header("Location: succes.php");
		}
	} elseif(isset($_GET['back'])) header("Location: ".$_SERVER['HTTP_REFERER']);
?>

