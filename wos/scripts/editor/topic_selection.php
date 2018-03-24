<?php 
	if(isset($_POST['create_topic'])) {
		echo "<!DOCTYPE html>
		<html>
		<head>
			<title>Темы</title>
			<meta charset='utf8'>
		</head>
		<body>";
		echo "<form action='topic_selection.php' method='POST'>
			<label for='n_topic_name'>Название темы</label>
			<input type='text' name='n_topic_name' required>
			<label for='topic_subject_id'>ID добавленного предмета новой темы</label>
			<input type='text' name='topic_subject_id' size='3' value='".$_POST['chosen_subject_id']."' required>
			<p style='font-size:9pt'>(в форме вписан ID выбранного вами предмета)</p>
			<input type='submit' name='insert_topic' value='Добавить тему'><br>
		</form>";
		echo "<form action='editor.php' method='POST'>
			<input type='submit' name='cancel_creation' value='Отменить'>
		</form>";
		echo "</body></html>";
	} elseif(isset($_POST['cancel_creation'])) {
		header("Location: editor.php");
	} elseif(isset($_POST['insert_topic'])) {
		require_once '../db_data.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");

		$topic_name = fix_string($conn, trim($_POST['n_topic_name']));
		$topic_subject_id = fix_string($conn, trim($_POST['topic_subject_id']));
		$query = "SELECT subject_name FROM subjects WHERE subject_id='$topic_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die("<p>Такого ID предмета не существует</p><br>");
		$query = "SELECT * FROM topics WHERE topic_name='$topic_name'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if($row[0] || $row[1]) {
			die("<p>Такая тема уже существует!</p><br>
			<p>Название темы должно быть уникальным для любого предмета</p><br>
			<p>Вернитель назад и повторите попытку</p>");
		}
		$query = "INSERT INTO topics(topic_name, subject_id) VALUES(?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('ss', $topic_name, $topic_subject_id);
		$result->execute();
		$conn->close();
		if(!$result->affected_rows) {
			die($conn->connect_error);
		} else {
			header("Location: succes.php");
		}
		$result->close();
	}
?>

