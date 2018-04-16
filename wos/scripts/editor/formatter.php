<!DOCTYPE html>
<html>
<head>
	<title>Редактор лекции</title>
	<meta charset='utf8'>
	<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<?php include_once '../../menu.php' ?>
<h2>Редактор лекции приветствует вас!</h2>
<?php
	
	function open_file($conn, $location,  $filename, $file_is_opened) {
		if($file_is_opened == 0) {
			if(file_exists($location.$filename)) {
				echo "<p>Файл для данной лекции уже существует. В нем могу храниться данные о старой лекции, которая была уже удалена</p>
					<form action='formatter.php' method='POST'>
						<input type='submit' name='rewrite' value='Перезаписать файл'>
						<input type='submit' name='continue' value='Продолжить редактирование в этом файле'>
						<input type='hidden' name='lection_selection' value='".$_POST['lection_selection']."'>
						<input type='text' name='filename' value='$filename' style='display:none'>
					</form>";
			} else {
				get_first_query_result($conn, "UPDATE lections SET is_file_opened='1' WHERE lection_link='$filename'");
				open_editor($conn, $location, $filename, 'w');
			}
		} else {
			open_editor($conn, $location, $filename, 'r');
		}
	}
	function open_editor($conn, $location, $filename, $mode) {
		$row = get_first_select_array($conn, "SELECT topic_id FROM lections WHERE lection_link='$filename'", MYSQLI_NUM)[0];
		$topic_name = get_first_select_array($conn, "SELECT topic_name FROM topics WHERE topic_id='$row'", MYSQLI_NUM)[0];
		$lection_name = get_first_select_array($conn, "SELECT lection_name FROM lections WHERE lection_link='$filename'", MYSQLI_NUM)[0];

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
			if($line[$i] == "<div class='lection_content'>") {
				$flag = true;
			}
		}
		$text = get_clear_content($text);
		echo "<fieldset>
		<h2>$topic_name</h2>
		<h3>$lection_name</h3>";

		echo "<form action='formatter.php' method='POST' enctype='multipart/form-data'>
		<br>
		<p>Заполните самостоятельно поле ниже содержанием лекции, или выберите txt файл с содержанием, 
		<br>нажав на \"Browse...\" рядом с \"Выбрать txt файл\"
		<br>Если вы уже выбрали txt файл, но не хотите его использовать, просто поставьте отметку на \" - игнорировать файл\"<p>
		<label for='content'><b>Содержание лекции</b></label><br>
		<textarea cols='100' rows='15' name='content'>$text</textarea><br>
		<br>
		<input type='text' name='filename' value='$filename' style='display:none;'>
		<hr>
		<p>Для того, чтобы добавить изображение в лекцию, выберите его, нажав на 'Browse...'. Изображение может быть в формате jpg, png, tiff
		<br>После выбора нажмите кнопку \"Добавить изображение\". При удачной загрузке, сверху появится сообщение с готовой строкой, которую 
		<br>необходимо будет скорировать и вставить в любое место в содержании вашей лекции (либо в форму выше, либо в ваш txt файл)</p>
		<label for='image'><b>Выбрать изображение</b></lable><br>
		<input type='file' name='image' value='Изображение к лекции'>
		<input type='submit' name='add_image' value='Добавить изображение'><br>
		<hr><br>
		<p>Заметьте, что txt файл не нужен, если вы самостоятельно заполнили форму выше
		<br>Если форма выше уже заполнена и выбран txt файл, то к тексту в форме будет добавлен текст из txt
		<br>Если вы уже выбрали txt файл и не хотите его использовать, поставьте отметку на \"- игнорировать файл\"<br>
		<br><label for='txt'><b>Выбрать txt файл</b></lable><br>
		<input type='file' name='txt'><input type='checkbox' name='ignore_txt'> - игнорировать файл<br>
		<p>После выбора txt файла нажмите \"Сохранить\"
		<br><hr><br>";
		echo "<br><a href='$location$filename'>Страница лекции</a><br>";
		selection_form();
		echo "<br><input type='submit' name='save' value='Сохранить'><br>
		<br><hr><br>
		<br><input type='submit' name='back' formaction='editor.php' value='К выбору предмета'>
		</form>
		</fieldset>";
	}
	
	
	function selection_form() {
		echo "<input type='hidden' name='lection_selection' value='".$_POST['lection_selection']."'>";
	}


	function save_page($conn, $location) {
		$content = fix_content($_POST['content']);
		$filename = fix_string($conn, trim($_POST['filename']));
		$row = get_first_select_array($conn, "SELECT topic_id FROM lections WHERE lection_link='$filename'", MYSQLI_NUM)[0];
		$topic_name = get_first_select_array($conn, "SELECT topic_name FROM topics WHERE topic_id='$row'", MYSQLI_NUM)[0];
		$lection_name = get_first_select_array($conn, "SELECT lection_name FROM lections WHERE lection_link='$filename'", MYSQLI_NUM)[0];
		$txt_content = '';
		if($_FILES['txt']['name'] && !isset($_POST['ignore_txt'])) {
			if($_FILES['txt']['type'] != 'text/plain') {
				echo "<h1><u>Файл с содержанием лекции должен быть в формате txt</u></h1>";
			} else {
				$txt_content = fix_content(trim(file_get_contents($_FILES['txt']['tmp_name'])));
				echo "<h3>К содержанию лекции был добавлен материал из txt файла ".$_FILES['txt']['name'];
			}
		}
		$content .= $txt_content;
		create_lection_page($location.$filename, $topic_name, $lection_name, trim($content));
	}
	
	require_once '../db_data.php';
	require_once 'data_analizer.php';
	$conn = get_connection_object('editor');
	
	if(isset($_POST['save']) || isset($_POST['add_image'])) {
		save_page($conn, $lections_location);	
		if(isset($_POST['save'])) {
			echo "<h2>Файл сохранен.</h2>";
		} elseif(isset($_POST['add_image'])) {
			if(!$_FILES['image']['name']) die("Ошибка! Выберите файл, нажав 'Browse...'!");
			$img_filename = get_lection_imgname($_FILES['image']['type'], fix_string($conn, $_FILES['image']['name']), fix_string($conn, $_POST['filename']));
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $img_location.$img_filename)) die("Ошибка. Изображение не было загружено на сервер.");
			echo "<h2>Изображение добавлено.</h2>
			<p>Скопируйте строку ниже, и вставьте ее в любом месте текста лекции</p>
			<textarea spellcheck='false' rows='1' cols='1000' readonly><img src='$img_location$img_filename' alt='image'></textarea>";		
		}	
	}


	if(isset($_POST['select_lection']) || isset($_POST['lection_selection'])) { 
		$lection_id = fix_string($conn, trim($_POST['lection_selection'])); 
		$query = "SELECT lection_link, is_file_opened FROM lections WHERE lection_id='$lection_id'"; 
		$result = $conn->query($query); 
		if(!$result) die($conn->connect_error); 
		$row = $result->fetch_array(MYSQLI_NUM); 
		$filename = $row[0]; 
		$is_opened = $row[1];
		if(isset($_POST['continue']) || isset($_POST['rewrite'])) {
			$query = "UPDATE lections SET is_file_opened='1' WHERE lection_link='$filename'"; 
			$result = $conn->query($query); 
			if(!$result) die($conn->connect_error);
			if(isset($_POST['continue'])) {
				open_editor($conn, $lections_location, $filename, 'r');
			} else {
				open_editor($conn, $lections_location, $filename, 'w');
			} 
		} else {
			open_file($conn, $lections_location, $filename, $is_opened);
		}     
	}
	


?>
</body>
</html>
