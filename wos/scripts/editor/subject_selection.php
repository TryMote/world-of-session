<?php
	function subject_page_work($conn) {
		$query = "SELECT * FROM subjects";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		
		$row = $result->fetch_array(MYSQLI_NUM);
		echo "<fieldset>";
		if(!$row[0] && !$row[1]) {
			echo "<p>Ни одного предмета еще не добавлено</p><br>";
		} else {
			echo "<form action='editor.php' method='POST'>
			<label for='subject_name'>Предмет:</label><br>
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
			</form>";
			echo "<form action='subject_selection.php' method='POST'>
				<input type='submit' name='edit_subject' value='Изменить' style='width:200px'>
			</form>";
		}
		echo "<form action='subject_selection.php' method='POST'>
		<input type='submit' name='create_subject' value='Добавить новый' style='width:200px'>
		</form>";
		echo "</fieldset>";
	}
	        if(isset($_POST['create_subject'])) {
			echo "<!DOCTYPE html>
				<html>
				<head>
					<title>Предметы</title>
					<meta charset='utf8'>
				</head>
				<body>";
                        echo "<form action='subject_selection.php' method='POST' enctype='multipart/form-data'>
                                <br><label for='n_subject_id'>ID предмета</label>
                                <input type='text' name='n_subject_id' size='5' required>
                                <label for='n_subject_name'>Название предмета</label>
                                <input type='text' name='n_subject_name' required><br>
				<label for='n_subject_image'>Изображение к предмету</label>
				<input type='file' name='n_subject_image' value='default'><br>
				<br><input type='submit' name='insert_subject' value='Добавить предмет'><br>
                        </form>";
                        echo "<form action='subject_selection.php' method='POST'>
                                <input type='submit' name='cancel_creation' value='Отменить'>
                        </form>";
			echo "</body></html>";
                } elseif(isset($_POST['cancel_creation'])) {
                        header("Location: editor.php");
                } elseif(isset($_POST['insert_subject'])) {
			require_once '../db_data.php';
			$conn = new mysqli($hn, $un, $pw, $db);
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
					die('Файл не был загружен на сервер! Предмет будет добавлен в базу без изображения.'."\n".'Попробуйте после загрузки изменить данный предмет, выбрав его и нажав "Изменить" в главном меню');
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
		} elseif(isset($_POST['edit_subject'])) {

		}
?>
