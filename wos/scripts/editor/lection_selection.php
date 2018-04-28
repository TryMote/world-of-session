<?php 
	function new_lection() {
		echo "<h3>".$_SESSION['topic']."</h3><br>
			<label for='n_lection_name'>Название лекции:</label><br>
			<input type='text' name='n_lection_name' required><br>
			<input type='submit' name='insert_lection' value='Добавить лекцию'><br>";
	}
	
	} elseif(isset($_POST['insert_lection'])) {
		$lection_name = fix_string($conn, trim($_POST['n_lection_name']));
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
		$query = "SELECT subject_id FROM topics WHERE topic_id='$selected_topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		$subject_id = $row[0];
		$query = "SELECT lection_id FROM lections WHERE topic_id='$selected_topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$lection_index = $result->num_rows;
		require_once 'data_analizer.php';
		$filename = analize_file('php', 'lection', $subject_id.$selected_topic_id, $lection_index); 
		$query = "INSERT INTO lections(lection_name, lection_link, topic_id) VALUES(?,?,?)";
		$result = $conn->prepare($query);
		if(!$result) die($conn->connect_error);
		$result->bind_param('ssi', $lection_name, $filename, $selected_topic_id);
		$result->execute();
		if(!$result->affected_rows) {
			die($conn->connect_error);
		} else {
			header("Location: succes.php");
		}
		$result->close();
		$conn->close();
	} elseif(isset($_POST['force_edit_lection'])) {
		require_once '../db_data.php';
		$data = get_db_data('editor');
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		
		$lection_id = fix_string($conn, trim($_POST['e_lection_id']));
		$lection_name = fix_string($conn, trim($_POST['e_lection_name']));
		$query = "SELECT lection_name FROM lections WHERE lection_name='$lection_name'";
		$result = $conn->query($query);
		$row = $result->fetch_array(MYSQLI_NUM);
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		if(!$row[0]) {
			$query = "LOCK TABLES lections WRITE";
			$query = "UPDATE lections SET lection_name='$lection_name' WHERE lection_id='$lection_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$result = $conn->query("UNLOCK TABLES");
			if(!$result) die($conn->connect_error);
		}
		$conn->close(); 
		header('Location: succes.php'); 
	}
?>
