<?php	
	function subject_page_work($conn) {
		$result = get_first_query_result($conn, "SELECT * FROM subjects");
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0] && !$row[1]) {
			echo "<br><p>Ни одного предмета еще не добавлено</p><br>";
		} else {
			echo "<br><h3>Предметы:</h3>
			<br><select name='subject_selection'>";
			$row_number = $result->num_rows;
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row['subject_id']."'";
				if(isset($_SESSION['subject_id']) && $_SESSION['subject_id'] == $row['subject_id']) {
					echo " selected ";
				}
				echo ">".$row['subject_name']."</option>";
			}
			echo "</select>
			<input type='submit' name='select_subject' value='Выбрать'><br>
			<br><input type='submit' name='delete_subject' value='Удалить' style='width:200px'>
			<br><input type='submit' name='edit_subject' value='Изменить' style='width:200px'>";
		}
		echo "<br><input type='submit' name='create_subject' value='Добавить новый' style='width:200px'>";
	}

	function new_subject() {
		echo "<h2>Новый предмет</h2>
				<fieldset>
				<p>Название предмета на русском или английском языке будет отображаться на сайте<br></p>
				<label for='n_subject_name'><b>Название предмета</b></label><br>
                                <input type='text' name='n_subject_name' size='20' required><br>
				<br><hr>
				<p><b>Идентификатор должен состоять из символов латиницы!</b><br>
				Идентификатор предмета используется для его краткого обозначения при добавлении новых тем<br>
				Например, для предмета 'Алгоритмизация и программирование' можно ввести 'A' или 'AP' или 'AandP' и т.п.</p>
                                <br><label for='n_subject_id'><b>Идентификатор предмета</b></label><br>
                                <input type='text' maxlength='5' name='n_subject_id' size='20' required><br>
				<br><hr>
				<br><p>Изображение будет отображаться на сайте при выборе материала для изучения<br>
                                <br><label for='n_subject_image'><b>Изображение к предмету</b></label>
				<input type='file' name='n_subject_image' value='default'><br>
				<hr>
				<br><input type='submit' name='insert_subject' value='Добавить предмет'><br>
			</fieldset>";
	} 

                        
	function insert_subject($conn, $img_location) {
		$subject_id = fix_string($conn, trim($_POST['n_subject_id']));
		$subject_name = fix_string($conn, trim($_POST['n_subject_name']));
		$subject_image_type = fix_string($conn, trim($_FILES['n_subject_image']['type']));
		$filename = analize_file($subject_image_type, 'subject', $subject_id, '');
		$file_location = $img_location.$filename;	
		$row = get_first_select_array($conn, "SELECT * FROM subjects WHERE subject_id='$subject_id' OR subject_name='$subject_name'", MYSQLI_NUM);
		if(!$row[0] && !$row[1]) {
			if($filename !== 'default') {
				if(!move_uploaded_file($_FILES['n_subject_image']['tmp_name'], $file_location)) {
					$filename = 'default';
					echo "<br><p>Файл не был загружен на сервер! Предмет будет добавлен в базу без изображения.
					<br>Попробуйте после загрузки изменить данный предмет, выбрав его и нажав 'Изменить' в главном меню</p>";
				}
			}
			$query = "INSERT INTO subjects VALUES(?,?,?)";
			$result = $conn->prepare($query);
			if(!$result) die($conn->connect_error);
			$result->bind_param('sss', $subject_id, $subject_name, $filename);
			$result->execute();
			if(!$result->affected_rows) {
				die($conn->connect_error);
			} 
		} else {
			echo "<br><p>Помните, что название предмета и идентификатор всегда должны быть уникальными!</p>";
		}
		echo "<br><p>Предмет успешно добавлен!
		<br>Обновите страницу, если он еще не появился в списке</p>";
	}
		
	function force_edit_subject($conn) {
		$subject_id = $_SESSION['subject_id'];
		$subject_name = fix_string($conn, trim($_POST['e_subject_name']));
		$subject_image_type = (isset($_FILES['e_subject_image']['type']))? fix_string($conn, trim($_FILES['e_subject_image']['type'])) : '';
		$db_subject_name = get_first_select_array($conn, "SELECT subject_name FROM subjects WHERE subject_name='$subject_name'", MYSQLI_NUM)[0];
		get_first_query_result($conn, "LOCK TABLES subjects WRITE");
		if(!$db_subject_name) {
			get_first_query_result($conn, "UPDATE subjects SET subject_name='$subject_name' WHERE subject_id='$subject_id'");
		} 
		if($subject_image_type && isset($_FILES['e_subject_image']['tmp_name'])) {
			$filename = analize_file($subject_image_type, 'subject', $subject_id, '');
			if(!move_uploaded_file($_FILES['e_subject_image']['tmp_name'], $img_location.$filename)) {
				get_first_query_result($conn, "UNLOCK TABLES");
			 	echo "<br><p>Файл не был загружен на сервер!</p>";
			} else {
				get_first_query_result($conn, "UPDATE subjects SET subject_image='$filename' WHERE subject_id='$subject_id'");
			}
		}
		get_first_query_result($conn, "UNLOCK TABLES");
		echo "<br><p>Предмет успешно изменен!
		<br>Обновите страницу, если в списке еще ничего не поменялось!</p>";
	}
?>
