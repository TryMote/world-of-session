<?php
	function new_topic() {	
		echo "<h2>Новая тема</h2>
			<p>(новый босс)</p>
			<p>Имя босса будет отображаться на сайте
			<br>Оно может быть либо на русском языке, либо на английском</p>	
			<label for='n_topic_name'><b>Имя босса</b></label>
			<br><input type='text' name='n_topic_name' >
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
			<input type='submit' name='insert_topic' value='Добавить тему'><br>";
	}

	function insert_topic($conn) {
		$topic_name = fix_string($conn, trim($_POST['n_topic_name']));
		$topic_subject_id = $_SESSION['subject_id'];
		$topic_image_type = fix_string($conn, trim($_FILES['n_topic_image']['type']));
		$attack_image_type = fix_string($conn, trim($_FILES['attack_image']['type']));
		$fail_image_type = fix_string($conn, trim($_FILES['fail_image']['type']));
		$subject_name = get_first_select_array($conn, "SELECT subject_name FROM subjects WHERE subject_id='$topic_subject_id'", MYSQLI_NUM)[0];
		if(!$subject_name) {
			echo "<p>Такого ID предмета не существует</p><br>";
		} else {
			$row = get_first_select_array($conn, "SELECT * FROM topics WHERE topic_name='$topic_name'", MYSQLI_NUM);
			if($row[0] || $row[1]) {
				echo "<br><p>Такая тема уже существует!</p><br>
				<p>Название темы должно быть уникальным для любого предмета</p><br>
				<p>Попробуйте повторите попытку</p>";
			} else {
				$result = get_first_query_result($conn, "SELECT * FROM topics WHERE subject_id='$topic_subject_id'");
				$row_number = $result->num_rows;
				$filename = analize_file($topic_image_type, 'topic', $topic_subject_id, $row_number);
				$attack_filename = analize_file($attack_image_type, 'topic_attack', $topic_subject_id, $row_number);
				$fail_filename = analize_file($fail_image_type, 'topic_fail', $topic_subject_id, $row_number); 
				if($filename != 'default') {
					if(!move_uploaded_file($_FILES['n_topic_image']['tmp_name'], $topic_img_location.$filename)) {
						$filename = 'default';
						echo "<br><p>Файл не был загружен на сервер! Тема будет добавлена в базу без изображения 
					<br>Попробуйте после загрузки изменить данную тему, выбрав ее и нажав 'Изменить' в главном меню</p>";
					}
				}
				if($attack_filename != 'default') {
					if(!move_uploaded_file($_FILES['attack_image']['tmp_name'], $topic_img_location.$attack_filename)) {
						$attack_filename = 'default';
						echo "<br><p>Ошибка при загрузке файла!</p>";
					}
				}
				if($fail_filename != 'default') {
					if(!move_uploaded_file($_FILES['fail_image']['tmp_name'], $topic_img_location.$fail_filename)) {
						$fail_filename = 'default';
						echo "<br><p>Ошибка при загрузке файла!</p>";
					}
				}
				$query = "INSERT INTO topics(topic_name, topic_image, topic_attack, topic_fail, subject_id) VALUES(?,?,?,?,?)";
				$result = $conn->prepare($query);
				if(!$result) die($conn->connect_error);
				$result->bind_param('sssss', $topic_name, $filename, $attack_filename, $fail_filename, $topic_subject_id);
				$result->execute();
				if(!$result->affected_rows) {
					die("Ошибка подключения!");
				}
				echo "<br><p>Тема успешно добавлена!
				<br>Обновите страницу, если новая тема еще не появилась в списке!</p>";
			}
		}
	}

	function force_edit_topic($conn) {
		$topic_id = fix_string($conn, trim($_SESSION['topic_id']));
		$topic_name = fix_string($conn, trim($_POST['e_topic_name']));
		$topic_image_type = fix_string($conn, trim($_FILES['e_topic_image']['type']));
		$attack_image_type = fix_string($conn, trim($_FILES['e_attack_image']['type']));
		$fail_image_type = fix_string($conn, trim($_FILES['e_fail_image']['type']));
		$db_topic_name = get_first_select_array($conn, "SELECT topic_name FROM topics WHERE topic_name='$topic_name'", MYSQLI_NUM)[0];
		get_first_query_result($conn, "LOCK TABLES topics WRITE");
		if(!$db_topic_name) {
			get_first_query_result($conn, "UPDATE topics SET topic_name='$topic_name' WHERE topic_id='$topic_id'");
		}
		require_once 'data_analizer.php';
		$subject_id = get_first_select_array($conn, "SELECT subject_id FROM topics WHERE topic_id='$topic_id'", MYSQLI_NUM)[0];
		$result = get_first_query_result($conn, "SELECT topic_name FROM topics WHERE subject_id='$subject_id'");
		$index = $result->num_rows;
		$filename = analize_file($topic_image_type, 'topic', $subject_id.$topic_id, $index);
		$attack_filename = analize_file($attack_image_type, 'topic_attack', $subject_id.$topic_id, $index);
		$fail_filename = analize_file($fail_image_type, 'topic_fail', $subject_id.$topic_id, $index);
		if($filename != 'default') {
			if(!move_uploaded_file($_FILES['e_topic_image']['tmp_name'], $topic_img_location.$filename)) {
				get_first_query_result($conn, "UNLOCK TABLES");
			} else {
				get_first_query_result($conn, "UPDATE topics SET topic_image='$filename' WHERE topic_id='$topic_id'");
			}
		}
		if($attack_filename != 'default') {
			if(!move_uploaded_file($_FILES['e_attack_image']['tmp_name'], $topic_img_location.$attack_filename)) {
				get_first_query_result($conn, "UNLOCK TABLES");
			} else {
				get_first_query_result($conn, "UPDATE topics SET topic_attack='$attack_filename' WHERE topic_id='$topic_id'");
			}	  
		}
		if($fail_filename != 'default') {
			if(!move_uploaded_file($_FILES['e_fail_image']['tmp_name'], $topic_img_location.$fail_filename)) {
				get_first_query_result($conn, "UNLOCK TABLES");
			} else {
				get_first_query_result($conn, "UPDATE topics SET topic_fail='$fail_filename' WHERE topic_id='$topic_id'");
			}     
		}
		get_first_query_result($conn, "UNLOCK TABLES");
		echo "<br><p>Тема была успешно изменена!
		<br>Обновите страницу, если изменения еще не было применены в списке!</p>";
	}
?>
