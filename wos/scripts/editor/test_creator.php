<!DOCTYPE html>
<html>
<head>
	<title>Создать тест</title>
	<meta charset='utf8'>
</head>
<body>
<?php 

	function open_editor($conn, $test_id, $topic_name) {
		$result = $conn->query("SELECT question_id FROM question WHERE test_id='$test_id'");
		if(!$result) {
			echo "<fieldset><p>Ни одного вопроса не добавлено</p></fieldset>";
		} else {
			$row_number = $result->num_rows;
			echo "Вопросов в тесте: $row_number";
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_NUM);
				$qt_result = $conn->query("SELECT question_text FROM questions WHERE question_id='$row[0]'");
				$q_text = $qt_result->fetch_array(MYSQLI_NUM);
				echo "<fieldset>";
				echo $q_text[0];
				echo "<hr>";
				$at_result = $conn->query("SELECT answer_text, is_right_answer FROM answers WHERE question_id='$row[0]'");
				if(!$at_result) {
					echo "Ответов к вопросу не добавлено";
				} else {
					$at_row_number = $at_result->num_rows;
					echo "<ul>";
					for($j = 0; $j < $at_row_number; ++$j) {
						$at_result->data_seek($j);
						$a_text = $at_result->fetch_array(MYSQLI_NUM);
						echo "<li>".$a_text[0];
						if($a_text[1]) {
							echo " --- верный ответ --- ";
						}
					}
					echo "</ul>";
				}
				echo "<form action='test_creator.php' method='POST'>
				<br><input type='submit' name='edit_question' value='Изменить вопрос'>
				<input type='hidden' name='question_id' value='$row[0]'>
				</form>";
				echo "</fieldset>";
			}
		}
		
		echo "<fieldset>
		<form action='test_creator.php' method='POST'>";
		echo "<input type='submit' name='add_question' value='Добавить вопрос'>
		<input type='submit' name='show_page' value='Перейти на страницу теста'>
		<input type='submit' name='back' formaction='editor.php' value='Вернуться к выбору предмета'>
		<input type='hidden' value='$test_id' name='test_id'>
		<input type='hidden' name='topic_selection' value='".$_POST['topic_selection']."'>";
		echo "</form></fieldset>";
	}

	require_once '../db_data.php';
	require_once 'data_analizer.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");

	if(isset($_POST['add_question'])) {
		echo "<fieldset>
		<form action='test_creator.php' method='POST' enctype='multipart/form-data'>
		<input type='hidden' name='topic_selection' value='".fix_string($conn, $_POST['topic_selection'])."'>
		<br>
		<label for='question_text' style='font-size:20pt'><strong>Вопрос</strong></label><br>
		<br><p>Вы можете ввести текст как самостоятельно в поле ниже, так и загрузив в формате <b>txt</b><br>
		Для загрузки txt файла нажмите кнопку 'Browse...' рядом с 'Выбрать содержание вопроса в виде txt файла' <br>и выберите его<br>
		</p>
		<br><p>После заполнения содержания вопроса переходите к полю \"Варианты ответов\"</p>
		<br><label for='question_text'><b>Содержание вопроса:</b></label><br>
		<textarea type='text' name='question_text' rows='5' cols='80'></textarea><br>
		<br><label style='font-size:11pt' for='add_image'>Добавить изображение</label>
		<input type='file' name='q_image'>
		<p style='font-size:11pt; padding:0; margin:0;'> изображение будет добавлено в конец введенного в поле текста</p>
		<br><br><label for='question_image'>Выбрать содержание вопроса в виде txt файла: </label>
		<br><input type='file' name='q_file'>	
		<br><input type='checkbox' value='0' name='cancel_q_file'> Отменить использование файла
		<p>Если вы уже выбрали файл, но не хотите его использовать как содержание вопроса<br>
		поставьте отмету на <b>'Отменить использование файла'</b><br>
		<br><strong>Внимание!</strong>
		Если был выбран txt файл, и не поставлена отметка на 'Отменить использование файла'<br>
		текст введенный в поле будет проигнорирован</p>
		<hr>";
		echo "<p style='font-size:20pt'><strong>Варианты ответов</strong></p>
		<p>Вместо ввода текста ответа, можно загрузить изображение в формате png или jpeg (jpg) <br>
		<br>Варианты ответов в виде изображений используются при невозможности их представления в виде текста
		<br>Если ответ приведен и в виде изображения и в виде текста, на странице будет показан вариант в виде изображения</p>
		<p>Заметьте, правильных выриантов ответа может быть несколько</p>
		<p>Если помечено несколько верных ответов, при прохождении теста, решение на этот вопрос будет зачтено только<br>
		если выбраны все правильные ответы и ни одного неправильного</p>
		<p><b>Первый вариант ответа является обязательным к заполнению</b> (при этом он может быть и не отмечен как верный)</p>
		<p><b>Если заполнен только один, первый вариант, то он НЕ должен быть в виде изображения</b></p>
		<p>Если заполнен только один, первый вариант ответа, то при прохождении теста, для решения данного вопроса<br>
		необходимо будет ввести значение, равное введенному вами сейчас в поле для первого варианта ответа</p>
		<ol>
			<li style='padding-bottom:25px'><input type='text' style='margin:10px' name='ans_1'>
			<input type='checkbox' name='is_right_1' value='1'> - это верный ответ
			<br>В виде изображения: <br><input type='file' name='img_ans_1'>
			<li style='padding-bottom:25px'><input type='text' style='margin:10px' name='ans_2'>
			<input type='checkbox' name='is_right_2' value='2'> - это верный ответ
			<br>В виде изображения: <br><input type='file' name='img_ans_2'>
			<li style='padding-bottom:25px'><input type='text' style='margin:10px' name='ans_3'>
			<input type='checkbox' name='is_right_3' value='3'> - это верный ответ
			<br>В виде изображения: <br><input type='file' name='img_ans_3'>
			<li><input type='text' style='margin:10px' name='ans_4'>
			<input type='checkbox' name='is_right_4' value='4'> - это верный ответ
			<br>В виде избражения: <br><input type='file' name='img_ans_4'>  	
		</ol>
		<input type='submit' value='Добавить' name='force_add_question'>
		</fieldset>";
	}
	
	if(isset($_POST['force_add_question'])) {
		if(!$_POST['ans_1'] && !$_FILES['img_ans_1']['type']) {
			die("<br><b>Первый вариант ответа должен быть заполнен обязательно!</b>
			<br><br>Если вы заполнили только один ответ, то он обязательно должен быть первым!");	
		}
		$right_number = 0;
		$rights = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
		for($i = 1; $i < 5; ++$i) {
			if(isset($_POST['is_right_'.$i])) {
				if(!$_POST['ans_'.$i] && !$_FILES['img_ans_'.$i]['type']) {
					die("<br><b>К правильному ответу <i>номер $i</i> не было добавлено ни текста, ни изображения</b><br>
					<br>Вернитесь назад и заполните его");
				}

				$rights[$i]++;
				$right_number++;
			}
			if($_FILES['img_ans_'.$i]['type']) {
					
			}		
		}
		if($right_number == 0) {
			die("<br><strong>Хотя бы один вариант должен быть помечен правильным</strong><br>
			<br>Ни один вариант ответа не был отмечен правильным. 
			<br>Вернитесь назад и поставьте галочку на верном варианте.<br><br>");
		}
		die(print_r($rights));
	}

	if(isset($_POST['topic_selection'])) {
		$topic_id = fix_string($conn, trim($_POST['topic_selection']));
		$query = "SELECT test_id, subject_id, topic_name FROM topics WHERE topic_id='$topic_id'";
		$result = $conn->query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$test_id = $row['test_id'];
		$topic_name = $row['topic_name'];
		if($test_id == 0) {
			$subject_id = $row['subject_id'];
			$filename = analize_file('.php', 'test', $subject_id.$topic_id, '');
			$location = $tests_location.$filename;
			$query = "INSERT INTO tests(test_link, topic_id) VALUES(?,?)";  
			$result = $conn->prepare($query);
			$result->bind_param('si', $filename, $topic_id);
			$result->execute();
			$query = "SELECT test_id FROM tests WHERE topic_id='$topic_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row = $result->fetch_array(MYSQLI_NUM);
			$test_id = $row[0];
			$query = "UPDATE topics SET test_id='$test_id' WHERE topic_id='$topic_id'";
			$conn->query($query);	
			create_test_page($location, $topic_name, ''); 
			open_editor($conn, $filename, $topic_name);
		} else {
			$query = "SELECT test_link FROM tests WHERE test_id='$test_id'";
			$result = $conn->query($query);
			if(!$result) die("Ошибка, попробуйте перезагрузить страницу");
			$row = $result->fetch_array(MYSQLI_NUM);
			$test_link = $row[0];
			open_editor($conn, $test_id, $topic_name);
		}
		
	}

	if(isset($_POST['show_page'])) {
		$result = $conn->query("SELECT test_link FROM tests WHERE test_id='".fix_string($conn, $_POST['test_id'])."'");
		$filename = $result->fetch_array(MYSQLI_NUM);
		if(!$filename[0]) die("Ошибка, при переходе на страницу теста. Попробуйте обновить редактор теста");
		header("Location: ".$tests_location.$filename[0]);
	}	
	
	$conn->close();
?>
</body>
</html>
