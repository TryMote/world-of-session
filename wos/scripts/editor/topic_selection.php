<!DOCTYPE html>
<html>
<head>
	<title>Темы</title>
	<meta charset='utf8'>
	<style>
		h2 {
			margin-left:10%;
		}
	</style>
</head>
<body>

<?php
	if(isset($_POST['create_topic'])) {
		echo "<h2>Новая тема</h2>
			<fieldset>
			<form action='topic_selection.php' method='POST' enctype='multipart/form-data'>
			<p>Название темы будет отображаться на сайте
			<br>Оно может быть либо на русском языке, либо на английском</p>	
			<label for='n_topic_name'><b>Название темы</b></label>
			<br><input type='text' name='n_topic_name' required>
			<br><br><hr>
			<p>Изображение темы будет отображаться при выборе материала для изучения</p>
			<label for='n_topic_image'><b>Изображение темы</b></label>
			<br><input type='file' name='n_topic_image' value='default'>
			<br><br><hr>
			<input type='hidden' name='subject_selection' value='".$_POST['chosen_subject_id']."'>
			<input type='submit' name='insert_topic' value='Добавить тему'><br>
		</form>";
		echo "<form action='editor.php' method='POST'>
			<input type='submit' name='cancel_creation' value='Отменить'>
		</form>
		</fieldset>";
	} elseif(isset($_POST['cancel_creation'])) {
		header("Location: editor.php");
	} elseif(isset($_POST['insert_topic'])) {
		require_once '../db_data.php';
		$data = get_db_data('editor');
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");

		$topic_name = fix_string($conn, trim($_POST['n_topic_name']));
		$topic_subject_id = fix_string($conn, trim($_POST['subject_selection']));
		$topic_image_type = fix_string($conn, trim($_FILES['n_topic_image']['type']));
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
		$query = "SELECT * FROM topics WHERE subject_id='$topic_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row_number = $result->num_rows;
		require_once 'data_analizer.php';
		$filename = analize_file($topic_image_type, 'topic', $topic_subject_id, $row_number);
		$file_location = $img_location.$filename;
		if($filename !== 'default') {
			if(!move_uploaded_file($_FILES['n_topic_image']['tmp_name'], $file_location)) {
				$filename = 'default';
				die('Файл не был загружен на сервер! Тема будет добавлена в базу без изображения.'."\n".'Попробуйте после загрузки изменить данную тему, выбрав ее и нажав "Изменить" в главном меню');
			}
		}
		$query = "INSERT INTO topics(topic_name, topic_image, subject_id) VALUES(?,?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('sss', $topic_name, $filename, $topic_subject_id);
		$result->execute();
		$conn->close();
		if(!$result->affected_rows) {
			die($conn->connect_error);
		} else {
			header("Location: succes.php");
		}
		$result->close();
	} elseif(isset($_POST['force_edit_topic'])) {
		require_once '../db_data.php';
		$data = get_db_data('editor');
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");

		$topic_id = fix_string($conn, trim($_POST['e_topic_id']));
		$topic_name = fix_string($conn, trim($_POST['e_topic_name']));
		$topic_image_type = (isset($_FILES['e_topic_image']['type']))? fix_string($conn, trim($_FILES['e_topic_image']['type'])) : '';
		$query = "SELECT topic_name FROM topics WHERE topic_name='$topic_name'";
		$result = $conn->query($query);
		$row = $result->fetch_array(MYSQLI_NUM);
		$query = "LOCK TABLES topics WRITE";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		if(!$row[0]) {
			$query = "UPDATE topics SET topic_name='$topic_name' WHERE topic_id='$topic_id'";
			$result = $conn->query($query);
		}
		if(!$result) die($conn->connect_error);
		require_once 'data_analizer.php';
		if($topic_image_type && isset($_FILES['e_topic_image']['tmp_name'])) {
			$result = $conn->query("SELECT subject_id FROM topics WHERE topic_id='$topic_id'");
			$row = $result->fetch_array(MYSQLI_NUM);
			if(!$row[0]) die("Ошибка, ID предмета не найден");
			$subject_id = $row[0];  
			$result = $conn->query("SELECT topic_name FROM topics WHERE subject_id='$subject_id'");
			$index = $result->num_rows;
			$filename = analize_file($topic_image_type, 'topic', $subject_id.$topic_id, $index);
			if(!move_uploaded_file($_FILES['e_topic_image']['tmp_name'], $img_location.$filename)) {
				$result = $conn->query("UNLOCK TABLES");
				if(!$result) die($conn->connect_error);
				die("Файл не был загружен на сервер!");
			} else {
				$query = "UPDATE topics SET topic_image='$filename' WHERE topic_id='$topic_id'";
				$result = $conn->query($query);
				if(!$result) die($conn->connect_error);
			}
		}       
		$result = $conn->query("UNLOCK TABLES");
		if(!$result) die($conn->connect_error);
		$conn->close(); 
		header('Location: succes.php'); 
	}
?>

