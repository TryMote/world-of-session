<?php 
	function new_lection() {
		echo "<h3>".$_SESSION['topic']."</h3><br>
			<label for='n_lection_name'>Название лекции:</label><br>
			<input type='text' name='n_lection_name'><br>
			<input type='submit' name='insert_lection' value='Добавить лекцию'><br>";
	}
	
	function insert_lection($conn) {
		$lection_name = fix_string($conn, trim($_POST['n_lection_name']));
		$topic_id = fix_string($conn, trim($_SESSION['topic_id']));
		$lection_link = get_first_select_array($conn, "SELECT lection_link FROM lections WHERE lection_name='$lection_name'", MYSQLI_NUM)[0];
		$db_lection_name = get_first_select_array($conn, "SELECT lection_name FROM lections WHERE topic_id='$topic_id' AND lection_name='$lection_name'", MYSQLI_NUM)[0];
		if($lection_link || $db_lection_name) {
			echo "<p>Такая лекция уже существует!</p><br> 
				<p>Название лекции должно быть уникальным для данной темы</p><br>
				<p>Имя файла HTML должно быть уникальным для всех тем</p><br>
				<p>Вернитесь назад и повторите попытку</p>";
		} else {
			$subject_id = get_first_select_array($conn, "SELECT subject_id FROM topics WHERE topic_id='$topic_id'", MYSQLI_NUM)[0];
			$result = get_first_query_result($conn, "SELECT lection_id FROM lections WHERE topic_id='$topic_id'");
			$lection_index = $result->num_rows;
			$filename = analize_file('php', 'lection', $subject_id.$topic_id, $lection_index); 
			$query = "INSERT INTO lections(lection_name, lection_link, topic_id) VALUES(?,?,?)";
			$result = $conn->prepare($query);
			if(!$result) die($conn->connect_error);
			$result->bind_param('ssi', $lection_name, $filename, $topic_id);
			$result->execute();
			if(!$result->affected_rows) {
				die($conn->connect_error);
			}
			echo "<br><p>Новая лекция была успешно добавлена!
			<br>Выбирете необходимый предмет и тему, и перейдите к редактированию новинки!
			<br>Если материал еще не появился в списке, попробуйте обновить страницу или добавить материал снова</p>";
		}
	}
	
	function  force_edit_lection($conn) {
		$lection_id = fix_string($conn, trim($_SESSION['lection_id']));
		$lection_name = fix_string($conn, trim($_POST['e_lection_name']));
		$db_lection_name = get_first_select_array($conn, "SELECT lection_name FROM lections WHERE lection_name='$lection_name'", MYSQLI_NUM)[0];
		if(!$db_lection_name) {
			get_first_query_result($conn, "LOCK TABLES lections WRITE");
			get_first_query_result($conn, "UPDATE lections SET lection_name='$lection_name' WHERE lection_id='$lection_id'");
			get_first_query_result($conn, "UNLOCK TABLES");
		}
		echo "<br><p>Данные лекции были успешно обновленны</p>";
	}
?>
