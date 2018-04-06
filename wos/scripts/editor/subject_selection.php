<!DOCTYPE html>
<html>
<head>
	<title>Предметы</title>
	<meta charset='utf8'>
	<style>
	h2 {
		margin-left: 5%;	
	}
	</style>
</head>
<body>

<?php
	function subject_page_work($conn) {
		$query = "SELECT * FROM subjects";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0] && !$row[1]) {
			echo "<p>Ни одного предмета еще не добавлено</p><br>";
		} else {
			echo "<h3>Предметы:</h3>";
			echo "<form action='editor.php' method='POST'>
			<br><select name='subject_selection'>";
			$row_number = $result->num_rows;
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row['subject_id']."'>".$row['subject_name']."</option>";
			}
			echo "</select>
			<input type='submit' name='select_subject' value='Выбрать'><br>
			<br><input type='submit' name='delete_subject' value='Удалить' style='width:200px'>
			<br><input type='submit' name='edit_subject' value='Изменить' style='width:200px'>
			</form>";
		}
		echo "<form action='subject_selection.php' method='POST'>
		<input type='submit' name='create_subject' value='Добавить новый' style='width:200px'>
		</form>";
	}
	        if(isset($_POST['create_subject'])) {
			echo "<h2>Новый предмет</h2>
				<fieldset>
				<form action='subject_selection.php' method='POST' enctype='multipart/form-data'>
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
                        </form>";
                        echo "<form action='subject_selection.php' method='POST'>
                                <input type='submit' name='cancel_creation' value='Отменить'>
                        </form>
			</fieldset>";
                } elseif(isset($_POST['cancel_creation'])) {
                        header("Location: editor.php");
                } elseif(isset($_POST['insert_subject'])) {
			require_once '../db_data.php';
			$data = get_db_data('editor');
			$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
			if($conn->connect_error) die($conn->connect_error);
			$conn->query("SET NAMES 'utf8'");
			
                        $subject_id = fix_string($conn, trim($_POST['n_subject_id']));
                        $subject_name = fix_string($conn, trim($_POST['n_subject_name']));
			$subject_image_type = fix_string($conn, trim($_FILES['n_subject_image']['type']));
			require_once 'data_analizer.php';
			$filename = analize_file($subject_image_type, 'subject', $subject_id, '');
			$file_location = $img_location.$filename;	
                        $query = "SELECT * FROM subjects WHERE subject_id='$subject_id' OR subject_name='$subject_name'";
                        $result = $conn->query($query);
                        if(!$result) die($conn->connect_error);
                        $row = $result->fetch_array(MYSQLI_NUM);
                        if($row[0] || $row[1]) {
                                die("<p>Такой предмет или ID предмета уже добавлены в базу!</p><br>
					<p>Вернитесь назад и попробуйте поменять имя или ID</p>");
                        }
			if($filename !== 'default') {
				if(!move_uploaded_file($_FILES['n_subject_image']['tmp_name'], $file_location)) {
					$filename = 'default';
					echo 'Файл не был загружен на сервер! Предмет будет добавлен в базу без изображения.'."\n".'Попробуйте после загрузки изменить данный предмет, выбрав его и нажав "Изменить" в главном меню';
					echo "<form action='editor.php' method='POST'>
						<input type='submit' name='back' value='Отменить добавление'>
						</form>";
				}
			}
                        $query = "INSERT INTO subjects VALUES(?,?,?)";
                        $result = $conn->prepare($query);
                        if(!$result) die($conn->connect_error);
                        $result->bind_param('sss', $subject_id, $subject_name, $filename);
                        $result->execute();
                        if(!$result->affected_rows) {
                                die($conn->connect_error);
                        } else {
				$result->close();
				$conn->close();
                                header("Location: succes.php");
                        }
		} elseif(isset($_POST['force_edit_subject'])) {
			require_once '../db_data.php';
			$data = get_db_data('editor');
			$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
			if($conn->connect_error) die($conn->connect_error);
			$conn->query("SET NAMES 'utf8'");
			
			$subject_id = fix_string($conn, trim($_POST['e_subject_id']));
			$subject_name = fix_string($conn, trim($_POST['e_subject_name']));
			$subject_image_type = (isset($_FILES['e_subject_image']['type']))? fix_string($conn, trim($_FILES['e_subject_image']['type'])) : '';

			$query = "SELECT subject_name FROM subjects WHERE subject_name='$subject_name'";
			$result = $conn->query($query);
			$row = $result->fetch_array(MYSQLI_NUM);
			$query = "LOCK TABLES subjects WRITE";
			$result = $conn->query($query); 
			if(!$result) die($conn->connect_error);
			if(!$row[0]) {
				$query = "UPDATE subjects SET subject_name='$subject_name' WHERE subject_id='$subject_id'";
				$result = $conn->query($query);
			} 
			if(!$result) die($conn->connect_error);
			require_once 'data_analizer.php';
			if($subject_image_type && isset($_FILES['e_subject_image']['tmp_name'])) {
				$filename = analize_file($subject_image_type, 'subject', $subject_id, '');
				if(!move_uploaded_file($_FILES['e_subject_image']['tmp_name'], $img_location.$filename)) {
					$result = $conn->query("UNLOCK TABLES");
					if(!$result) die($conn->connect_error);
				 	die("Файл не был загружен на сервер!");
				} else {
					$query = "UPDATE subjects SET subject_image='$filename' WHERE subject_id='$subject_id'";
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
</body>
</html>
