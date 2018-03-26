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
		$topic_name = get_topic_name($conn, $filename);
		$lection_name = get_lection_name($conn, $filename);
		if($mode == 'w') {	
			create_lection_page($location.$filename, $topic_name, $lection_name, '');
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
		<h2>$topic_name</h2>
		<h3>$lection_name</h3>
		<form action='formatter.php' method='POST' enctype='multipart/form-data'>
		<br>
		<textarea cols='100' rows='15' name='content'>$text</textarea><br>
		<br>
		<input type='text' name='filename' value='$filename' style='display:none;'>
		<input type='hidden' name='location' value='$location'>
		<fieldset style='width:500px'>
		<input type='file' name='image' value='Изображение к лекции'>
		<input type='submit' name='add_image' value='Добавить изображение'><br>
		</fieldset>
		<br><input type='submit' name='save' value='Сохранить'>
		</form>
		</fieldset>";
	}
	
	function get_topic_name($conn, $filename) {
		$query = "SELECT topic_id FROM lections WHERE lection_link='$filename'";
		$result = $conn->query($query);
		if(!$result) die("Файл не найден");
		$row = $result->fetch_array(MYSQLI_NUM);
		$query = "SELECT topic_name FROM topics WHERE topic_id='$row[0]'";
		$result = $conn->query($query);
		if(!$result) die("Файл не найден");
		$row = $result->fetch_array(MYSQLI_NUM);

		return $row[0];
	}

	function get_lection_name($conn, $filename) {
		$query = "SELECT lection_name FROM lections WHERE lection_link='$filename'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		return $row[0];
	}

	function create_lection_page($full_location, $topic_name, $lection_name, $content) {
		$page = "<!DOCTYPE html>
<html>
<head>
<title>$topic_name</title>
<meta charset='utf8'>
<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<header>
<?php include_once '../../menu.php' ?>
</header>
<div id='lection_page'>
<div id='lections_list'>
<?php include_once 'lection_list.php' ?>
</div>
<div id='main_headers'>
<h1>$topic_name</h1>
<h2>$lection_name</h2>
</div>
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
	
	require_once '../db_data.php';
	require_once 'data_analizer.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");		

	if(isset($_POST['save']) || isset($_POST['add_image'])) {
			
		$content = trim(str_replace('</script>', '', str_replace('<script>', '', $_POST['content'])));
		$filename = fix_string($conn, trim($_POST['filename']));
		$location = fix_string($conn, trim($_POST['location']));
		$topic_name = get_topic_name($conn, $filename);	
		$lection_name = get_lection_name($conn, $filename);
		create_lection_page($location.$filename, $topic_name, $lection_name, $content);
		if(isset($_POST['save'])) {
			echo "<h2>Файл сохранен.</h2> 
			<h1><strong>Вернитесь назад</strong></h1>
			<hr>
			<p style='font-size:10pt'>или перейдите к выбору материала</p>
			<form action='editor.php' method='POST'>
			<input type='submit' name='back' value='Перейти' style='font-size:9pt'>
			</form>";
		} elseif(isset($_POST['add_image'])) {
			if(!$_FILES['image']['name']) die("Ошибка! Файл не был выбран!");
			$img_filename = get_lection_imgname($_FILES['image']['type'], fix_string($conn, $_FILES['image']['name']), $filename);
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $img_location.$img_filename)) die("Ошибка. Изображение не было загружено на сервер.");
			echo "<h2>Изображение добавлено.</h2>
			<p>Скопируйте строку ниже, вернитесь назад и вставьте ее в любом месте текста лекции</p>
			<textarea spellcheck='false' rows='1' cols='1000' readonly><img src='$img_location$img_filename' alt='image'></textarea>";		
		}	
	}
	
	if(isset($_POST['select_lection']) && $_POST['lection_selection']) { 
		$lection_id = fix_string($conn, trim($_POST['lection_selection'])); 
		$query = "SELECT lection_link, is_file_opened FROM lections WHERE lection_id='$lection_id'"; 
		$result = $conn->query($query); 
		if(!$result) die($conn->connect_error); 
		$row = $result->fetch_array(MYSQLI_NUM); 
		$filename = $row[0]; 
		$is_opened = $row[1]; 
		open_file($conn, $lections_location, $filename, $is_opened);     
	}


?>
