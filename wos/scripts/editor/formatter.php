<?php
	
	function open_file($conn, $location,  $filename, $file_is_opened) {
		if($file_is_opened == 0) {
			if(file_exists($location.$filename)) {
				echo "<p>Файл для данной лекции уже существует. В нем могу храниться данные о старой лекции, которая была уже удалена</p>
					<form action='editor.php' method='POST'>
						<input type='submit' name='rewrite' value='Перезаписать файл'>
						<input type='submit' name='continue' value='Продолжить редактирование в этом файле'>
						<input type='text' name='filename' value='$filename' style='display:none'>
					</form>";
			} else {
				$query = "UPDATE lections SET is_file_opened='1' WHERE lection_link='$filename'";
				$result = $conn->query($query);
				if(!$result) die($conn->connect_error);
				open_editor($conn, $location, $filename, 'w');
			}
		} else {
			open_editor($conn, $location, $filename, 'r');
		}
	}
	function open_editor($conn, $location, $filename, $mode) {
		if($mode == 'w') {
			$query = "SELECT topic_id FROM lections WHERE lection_link='$filename'";
			$result = $conn->query($query);
			if(!$result) die("Файл не найден");
			$row = $result->fetch_array(MYSQLI_NUM);
			$query = "SELECT topic_name FROM topics WHERE topic_id='$row[0]'";
			$result = $conn->query($query);
			if(!$result) die("Файл не найден");
			$row = $result->fetch_array(MYSQLI_NUM);
			
			create_lection_page($location.$filename, $row[0], '');
		}
		$content = file_get_contents($location.$filename);
		$line = explode("\n", $content);
		$flag = false;
		$text = '';
		for($i = 0; $i < count($line); ++$i) {
			if($flag) {
				if($line[$i] == "</div>") {
					$flag = false;
				} else {
					$text .= $line[$i];
				}
			}
			if($line[$i] == "<div id='lection_content'>") {
				$flag = true;
			}
		}
		echo "<fieldset>
		<form action='formatter.php' method='POST'>
		<br>
		<textarea cols='200' rows='30' name='content'>$text</textarea><br>
		<br>
		<input type='text' name='filename' value='$filename' style='display:none;'>
		<input type='hidden' name='location' value='$location'>
		<input type='submit' name='save' value='Сохранить'>
		</form>
		</fieldset>";
	}

	function create_lection_page($full_location, $topic_name, $content) {
		$page = "<!DOCTYPE html>
<html>
<head>
<title>$topic_name</title>
<meta charset='utf8'>
</head>
<body>
<header>
<?php include_once '../../menu.php' ?>
</header>
<div id='lection_page'>
<div id='lections_list'>
<?php include_once 'lection_list.php' ?>
</div>
<h1>$topic_name</h1>
<div id='lection_content'>
$content
</div>
<div id='lection_controller'>
<?php include_once 'lection_controller.php' ?>
</div>
</div>
<footer>
<?php include_once '../../footer.php' ?>
</footer>
</body>
</html>";
		$file = fopen($full_location, 'w');
		fwrite($file, $page);
		fclose($file);
	}

	if(isset($_POST['save'])) {
		require_once '../db_data.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");		
		
		$content = $_POST['content'];
		$filename = fix_string($conn, trim($_POST['filename']));
		$location = fix_string($conn, trim($_POST['location']));
		$query = "SELECT topic_id FROM lections WHERE lection_link='$filename'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die("Ошибка! Лекция не найдена!");
		$topic_id = $row[0];
		$query = "SELECT topic_name FROM topics WHERE topic_id='$topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		$topic_name = $row[0];
		create_lection_page($location.$filename, $topic_name, $content);
		
		header("Location: editor.php");
	}	
?>
