<?php 
	function lection_page_work($topic_selection, $conn) {
		$query = "SELECT topic_name FROM topics WHERE topic_id='$topic_selection'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$selected_topic_name = $result->fetch_array(MYSQLI_NUM);
		echo "<br><p>Выбрана тема '$selected_topic_name[0]'<p><br>";
		
		$query = "SELECT * FROM lections WHERE topic_id='$topic_selection'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if(!$row['lection_name'] && !$row['lection_id']) {
			echo "<p>Для данной темы еще не добавлено лекций</p><br>";
		} else {
			echo "<form action='editor.php' method='POST'>";
			echo "<select name='lection_selection'>";
			$row_number = $result->num_rows;
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row['lection_id']."'>".$row['lection_name']."</option>";
			}
			echo "</select>
				<input type='submit' name='select_lection' value='Выбрать лекцию'>
				<input type='submit' name='delete_lection' value='Удалить лекцию'>
			</form>";
		}
		echo "<form action='lection_selection.php' method='POST'>
			<input type='text' name='chosen_topic_name' value='$selected_topic_name[0]' style='display:none'>
			<input type='text' name='chosen_topic_id' value='$topic_selection' style='display:none'>
			<input type='submit' name='create_lection' value='Добавить новую лекцию'>
		</form>";
	}
	if(isset($_POST['create_lection'])) {
		echo "<!DOCTYPE html>
		<html>
		<head>
			<title>Лекции</title>
			<meta charset='utf8'>
		</head>
		<body>";
		echo "<h3>".$_POST['chosen_topic_name']."</h3><br>
		<form action='lection_selection.php' method='POST'>
			<label for='n_lection_name'>Название лекции:</label><br>
			<input type='text' name='n_lection_name' required><br>
			<label for='n_lection_link'>Имя файла HTML:</label><br>
			<input type='text' name='n_lection_link' required><br>
			<input type='text' name='selected_topic_id' value='".$_POST['chosen_topic_id']."' style='display:none'>
			<p style='font-size:11pt'>(для имени файла HTML использовать только латиницу, цифры и символ нижнего подчеркивания \"_\")</p><br>
			<input type='submit' name='insert_lection' value='Добавить лекцию'><br>
		</form>";
		echo "<form action='lection_selection.php' method='POST'>
			<input type='submit' name='cancel_creation' value='Отменить'>
		</form>";
		echo "</body></html>";
	} elseif(isset($_POST['cancel_creation'])) {
		header("Location: editor.php");
	} elseif(isset($_POST['insert_lection'])) {
		require_once '../db_data.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		$lection_name = fix_string($conn, trim($_POST['n_lection_name']));
		$lection_link = fix_string($conn, trim($_POST['n_lection_link']));
		$lection_link = preg_replace("~[^A-Za-z0-9_]+~", "", str_replace(" ", "", $lection_link));
		$selected_topic_id = fix_string($conn, trim($_POST['selected_topic_id']));
		$query = "SELECT topic_name FROM topics WHERE topic_id='$selected_topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) {
			die("<p>Что-то пошло не так! Попробуйте заного</p><br>
			<form action='editor.php'><input type='submit' name='error_back' value='Вернуться назад'></form>");
		}
		$query = "SELECT lection_link FROM lections WHERE lection_link='$lection_link'";
		$result = $conn->query($query);
		$query_name = "SELECT lection_name FROM lections WHERE topic_id='$selected_topic_id' AND lection_name='$lection_name'";
		$result_name = $conn->query($query_name);
		if(!$result || !$result_name) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		$row_name = $result_name->fetch_array(MYSQLI_NUM);	
		if($row[0] || $row_name[0]) {
			die("<p>Такая лекция уже существует!</p><br> 
				<p>Название лекции должно быть уникальным для данной темы</p><br>
				<p>Имя файла HTML должно быть уникальным для всех тем</p><br>
				<p>Вернитесь назад и повторите попытку</p>");
		}
		$query = "INSERT INTO lections(lection_name, lection_link, topic_id) VALUES(?,?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('ssi', $lection_name, $lection_link, $selected_topic_id);
		$result->execute();
		if(!$result->affected_rows) {
			die($conn->connect_error);
		} else {
			header("Location: succes.php");
		}
		$result->close();
		$conn->close();
	}
?>
