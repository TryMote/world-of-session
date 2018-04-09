<!DOCTYPE html>
<html>
<head>
	<title>Темы</title>
	<meta charset='utf8'>
	<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<?php include_once '../../menu.php' ?>
<?php
	if(isset($_POST['create_topic'])) {
		echo "<h2>Новая тема</h2>
			<p>(новый босс)</p>
			<fieldset>
			<form action='topic_selection.php' method='POST' enctype='multipart/form-data'>
			<p>Имя босса будет отображаться на сайте
			<br>Оно может быть либо на русском языке, либо на английском</p>	
			<label for='n_topic_name'><b>Имя босса</b></label>
			<br><input type='text' name='n_topic_name' required>
			<br><br><hr>
			<ul>
			<p>Изображение босса будет отображаться при выборе материала для изучения</p>
			<li><label for='n_topic_image'><b>Изображение босса</b></label>
			<br><input type='file' name='n_topic_image' value='default'>
			<hr>
			<p>Изображение атаки босса демонстрируется, когда игрок выбрал не верный вариант ответа</p>
			<li><label for='attack_image'><b>Изображение атаки босса</b></label>
			<br><input type='file' name='attack_image' value='default'>
			<hr>
			<p>Изображения повреждения босса демонстрируется, когда игрок выбрал верный вариант ответа!</p>
			<li><label for='fail_image'><b>Изображение повреждения босса</b></label>
			<br><input type='file' name='fail_image' value='default'>
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
		$attack_image_type = fix_string($conn, trim($_FILES['attack_image']['type']));
		$fail_image_type = fix_string($conn, trim($_FILES['fail_image']['type']));
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
		$attack_filename = analize_file($attack_image_type, 'topic_attack', $topic_subject_id, $row_number);
		$fail_filename = analize_file($fail_image_type, 'topic_fail', $topic_subject_id, $row_number); 
		if($filename != 'default') {
			if(!move_uploaded_file($_FILES['n_topic_image']['tmp_name'], $img_location.$filename)) {
				$filename = 'default';
				die('Файл не был загружен на сервер! Тема будет добавлена в базу без изображения.'."\n".'Попробуйте после загрузки изменить данную тему, выбрав ее и нажав "Изменить" в главном меню');
			}
		}
		if($attack_filename != 'default') {
			if(!move_uploaded_file($_FILES['attack_image']['tmp_name'], $img_location.$attack_filename)) {
				$attack_filename = 'default';
				die("Ошибка при загрузке файла!");
			}

		}
		if($fail_filename != 'default') {
			if(!move_uploaded_file($_FILES['fail_image']['tmp_name'], $img_location.$fail_filename)) {
				$fail_filename = 'default';
				die("Ошибка при загрузке файла!");
			}
		}
		$query = "INSERT INTO topics(topic_name, topic_image, topic_attack, topic_fail, subject_id) VALUES(?,?,?,?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('sssss', $topic_name, $filename, $attack_filename, $fail_filename, $topic_subject_id);
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
		$topic_image_type = fix_string($conn, trim($_FILES['e_topic_image']['type']));
		$attack_image_type = fix_string($conn, trim($_FILES['e_attack_image']['type']));
		$fail_image_type = fix_string($conn, trim($_FILES['e_fail_image']['type']));
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
		$result = $conn->query("SELECT subject_id FROM topics WHERE topic_id='$topic_id'");
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die("Ошибка, ID предмета не найден");
		$subject_id = $row[0];  
		$result = $conn->query("SELECT topic_name FROM topics WHERE subject_id='$subject_id'");
		$index = $result->num_rows;
		$filename = analize_file($topic_image_type, 'topic', $subject_id.$topic_id, $index);
		$attack_filename = analize_file($attack_image_type, 'topic_attack', $subject_id.$topic_id, $index);
		$fail_filename = analize_file($fail_image_type, 'topic_fail', $subject_id.$topic_id, $index);
		if($filename != 'default') {
			if(!move_uploaded_file($_FILES['e_topic_image']['tmp_name'], $img_location.$filename)) {
				$result = $conn->query("UNLOCK TABLES");
				if(!$result) die($conn->connect_error);
				die("Основной файл не был загружен на сервер!");
			} else {
				$query = "UPDATE topics SET topic_image='$filename' WHERE topic_id='$topic_id'";
				$result = $conn->query($query);
				if(!$result) die($conn->connect_error);
			}
		}
		if($attack_filename != 'default') {
			if(!move_uploaded_file($_FILES['e_attack_image']['tmp_name'], $img_location.$attack_filename)) {
				$result = $conn->query("UNLOCK TABLES");
				if(!$result) die($conn->connect_error);
				die("Файл изображения-атаки не был загружен на сервер!");
			} else {
				$query = "UPDATE topics SET topic_attack='$attack_filename' WHERE topic_id='$topic_id'";
				$result = $conn->query($query);
				if(!$result) die($conn->connect_error);
			}	  
		}
		if($fail_filename != 'default') {
			if(!move_uploaded_file($_FILES['e_fail_image']['tmp_name'], $img_location.$fail_filename)) {
				$result = $conn->query("UNLOCK TABLES");
				if(!$result) die($conn->connect_error);
				die("Файл изображения-повреждения не был загружен на сервер!");
			} else {
				$query = "UPDATE topics SET topic_fail='$fail_filename' WHERE topic_id='$topic_id'";
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

