<!DOCTYPE html>
<html>
<head>
	<title>Создать тест</title>
	<meta charset='utf8'>
	<link rel='stylesheet' href='../../assets/css/styles.css'>  
</head>
<body>
<?php include_once '../../menu.php' ?>
<h2>Редактор тестов</h2>
<?php 

	function open_editor($conn, $test_id, $topic_name) {
		$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
		$row = $result->fetch_row()[0];
		if(!$row) {
			echo "<fieldset><p>Ни одного вопроса не добавлено</p></fieldset>";
		} else {
			$row_number = $result->num_rows;
			echo "<fieldset>";
			echo "<h3>Вопросов в тесте: $row_number</h3>";
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_row()[0];
				$q_text = get_first_select_array($conn,"SELECT question_text FROM questions WHERE question_id='$row'", MYSQLI_NUM)[0];
				$q_text = fix_content($q_text);
				echo "<fieldset> $q_text <hr>";
				$at_result = get_first_query_result($conn, "SELECT answer_text, is_right_answer FROM answers WHERE question_id='$row'");
				$a_row = $at_result->fetch_array(MYSQLI_NUM);
				if(!$a_row[0]) {
					echo "Ответов к вопросу не добавлено";
				} else {
					$at_row_number = $at_result->num_rows;
					echo "<ul>";
					for($j = 0; $j < $at_row_number; ++$j) {
						$at_result->data_seek($j);
						$a_text = $at_result->fetch_array(MYSQLI_NUM);
						if(!$a_text[0]) continue;
						if(strpos($a_text[0], '_answer_')) {
							echo "<li><img src='../../material/img/$a_text[0]' width='300px' height='200px' alt='$a_text[0]'>";
						} else { 
							echo "<li>".$a_text[0];
						
						}
						if($a_text[1]) {
							echo "   (<u><i>верный ответ</i></u>)";
						}
					}
					echo "</ul>";
				}
				echo "<form action='test_creator.php' method='POST'>
				<br><input type='submit' name='edit_question' value='Изменить вопрос'>
				<input type='hidden' name='topic_selection' value='".fix_string($conn, $_POST['topic_selection'])."'>
				<input type='hidden' name='question_id' value='$row[0]'>
				<input type='hidden' name='test_id' value='$test_id'>
				<input type='submit' name='delete_question' value='Удалить вопрос'>
				</form> </fieldset>";
			}
			echo "</fieldset>";
		}
		
		$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
		echo "<fieldset>
		<a style='margin:3%;text-decoration:none;' href='../../material/tests/$test_link'>Страница теста</a>
		<form action='test_creator.php' method='POST'>
		<br><input type='submit' name='add_question' value='Добавить вопрос'>
		<input type='submit' name='delete_test' value='Удалить тест'>
		<input type='submit' name='back' formaction='editor.php' value='Вернуться к выбору предмета'>
		<input type='hidden' value='$test_id' name='test_id'>
		<input type='hidden' name='topic_selection' value='".$_POST['topic_selection']."'>
		</form></fieldset>";
	}

	function show_question_editor($conn, $mode) {	
		if($mode == 'e') {
			$question_id = fix_string($conn, $_POST['question_id']);
		}	
		echo "<fieldset>
		<form action='test_creator.php' method='POST' enctype='multipart/form-data'>
		<input type='hidden' name='test_id' value='".fix_string($conn, $_POST['test_id'])."'>
		<input type='hidden' name='topic_selection' value='".fix_string($conn, $_POST['topic_selection'])."'>
		<br>
		<label for='question_text' style='font-size:20pt'><strong>Вопрос</strong></label><br>
		<br><p>Вы можете ввести текст как самостоятельно в поле ниже, так и загрузив в формате <b>txt</b><br>
		Для загрузки txt файла нажмите кнопку 'Browse...' рядом с 'Выбрать содержание вопроса в виде txt файла' <br>и выберите его<br>
		Добавление к вопросу изображения не обязательно. Также, вместо самостоятельного <br>
		заполнения, можно загрузить изображение с полным содержанием вопроса
		</p>
		<p>После заполнения содержания вопроса переходите к полю \"Варианты ответов\"</p>
		<br><label for='question_text'><b>Содержание вопроса:</b></label><br>
		<textarea type='text' name='question_text' rows='5' cols='80'>";
		if($mode == 'e') {
			$question_text = get_first_select_array($conn, "SELECT question_text FROM questions WHERE question_id='$question_id'", MYSQLI_NUM)[0];
			echo "$question_text";
		}
		echo "</textarea><br>
		<br><label style='font-size:11pt' for='add_image'><b>Добавить изображение</b></label>
		<input type='file' name='q_image'>
		<input type='checkbox' name='ignore_q_image'> - игнорировать изображение
		<p style='font-size:11pt; padding:0; margin:0;'> изображение будет добавлено в конец введенного в поле текста
		<br>Если поставлена отметка '- игнорировать изображение', выбранное вами изображение не будет загружено и использовано в тесте</p>
		<hr>
		<br><br><label for='question_image'><b>Добавить содержание вопроса в виде txt файла: </b></label>
		<br><input type='file' name='q_file'>	
		<input type='checkbox' value='0' name='cancel_q_file'> - игнорировать файл
		<p>Если вы уже выбрали файл, но не хотите его использовать как содержание вопроса<br>
		поставьте отмету на <b>'- игнорировать файл'</b><br>
		<br><strong>Внимание!</strong>
		Если был выбран txt файл, и не поставлена отметка на '- игнорировать файл'<br>
		текст введенный в поле будет проигнорирован</p>
		<hr>
		<p style='font-size:20pt'><strong>Варианты ответов</strong></p>
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
		<p>При отметке '- игнорировать файл', изображение выбранное вами не будет загружено на сервер и использовано в тесте</p>
		<ol>";

		for($i = 1; $i <= 4; ++$i) {
			$seek_index = $i - 1;
			echo "<li style='padding-bottom:25px'><input type='text' style='margin:10px' name='ans_$i'";
			if($mode == 'e') {
				$answer_text = get_select_array($conn, "SELECT answer_text FROM answers WHERE question_id='$question_id' AND answer_order_id='$i'", $seek_index, MYSQLI_NUM)[0];
				if($answer_text ) {
					echo "value='$answer_text'";
				}
			}
			echo "><input type='checkbox' name='is_right_$i' value='1' ";
			if($mode == 'e') {
				$is_right_answer = get_select_array($conn, "SELECT is_right_answer FROM answers WHERE question_id='$question_id' and answer_order_id='$i'", $seek_index, MYSQLI_NUM)[0];
				if($is_right_answer) {
					echo "checked";
				}
			}

			echo  "> - это верный ответ		
			<br>В виде изображения: <br><input type='file' name='img_ans_$i'>
			<br><input type='checkbox' name='ignore_img_$i' value='1'> - игнорировать файл";
		}
		echo "</ol>";
		if($mode == 'a') {
			echo "<input type='submit' value='Добавить' name='force_add_question'>";
		} else {
			echo "<input type='hidden' name='question_id' value='$question_id'>
			<input type='submit' value='Изменить' name='force_edit_question'>";
		}
		echo "</fieldset>";

	}

	function edit_question($conn, $mode) {

		if(!$_POST['ans_1'] && !$_FILES['img_ans_1']['type'] || ($_FILES['img_ans_1']['type'] && isset($_POST['ignore_img_1']) && !$_POST['ans_1'])) {
			die("<br><b>Первый вариант ответа должен быть заполнен обязательно!</b>
			<br><br>Если вы заполнили только один ответ, то он обязательно должен быть первым!");	
		}

		$right_number = 0;
		$rights = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
		for($i = 1; $i < 5; ++$i) {
			if(isset($_POST['is_right_'.$i])) {
				if((!$_POST['ans_'.$i] && !$_FILES['img_ans_'.$i]) || ($_FILES['img_ans_'.$i]['name'] && isset($_POST['ignore_img_'.$i]) && !$_POST['ans_'.$i])) {
					die("<br><b>К правильному ответу <i>номер $i</i> не было добавлено ни текста, ни изображения</b><br>
					<br>Вернитесь назад и заполните его");
				} else {
					$rights[$i]++;
					$right_number++;
				}
			}
			if($_FILES['img_ans_'.$i]['type'] && !isset($_POST['ignore_img_'.$i])) {
				$rights[$i] += 2;	
			}		
		}
		if($right_number == 0) {
			die("<br><strong>Хотя бы один вариант должен быть помечен правильным</strong><br>
			<br>Ни один вариант ответа не был отмечен правильным. 
			<br>Вернитесь назад и поставьте галочку на верном варианте.<br><br>");
		}
		if($rights[2] == 0 && $rights[3] == 0 && $rights[4] == 0) {
			if($rights[1] == 0) {
				die("<h2>Ни один вариант ответа не заполнен!<h2>
				<p>Вернитесь назад и заполните хотя бы первый, обязательный, вариант!</p>");
			}
		}

		if((!$_POST['question_text'] && !$_FILES['q_image']['type'] && !$_FILES['q_file']['type']) || ($_FILES['q_file']['name'] && isset($_POST['cancel_q_file']) && !$_POST['question_text'])) {
			die("<p><b>Отсутствует содержание вопроса!</b></p>
			<p>Как минимум должно быть заполнено поле \"Содержание вопроса\", <br>
			либо загружено изображение с содержанием, либо txt файл!</p><br>");	
		} elseif(!isset($_POST['cancel_q_file']) && $_FILES['q_file']['type'] != 'text/plain' && $_FILES['q_file']['name']) {
			die("<p><b>Файл с содержанием вопроса должен быть в формате txt</b></p>");
		}
	//////////////////////////////////////////////////////////////////////////////////
	
		$question_text = '';
		$question_image = '';
		if(!isset($_POST['cancel_q_file']) && $_FILES['q_file']['name']) {
			$question_text = fix_content(file_get_contents($_FILES['q_file']['tmp_name']));	
		} else {
			$question_text = fix_content($_POST['question_text']);
		}

		$test_id = fix_string($conn, $_POST['test_id']);
		$topic_id = fix_string($conn, $_POST['topic_selection']);
		$subject_id = get_first_select_array($conn, "SELECT subject_id FROM topics WHERE topic_id='$topic_id'", MYSQLI_NUM)[0];
		$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
		$row_number = $result->num_rows;
		if($_FILES['q_image']['name']) {
			$question_image = analize_file($_FILES['q_image']['type'], 'question', $topic_id.$test_id, $row_number);
			if(!move_uploaded_file($_FILES['q_image']['tmp_name'], '../../material/img/'.$question_image)) {
				die("Изображение не было загружено на сервер!");
			}
		}
		if($row_number > 0) {
			$result->data_seek($row_number-1);
		}
		$row = $result->fetch_row()[0];
		$row = get_first_select_array($conn, "SELECT question_text FROM questions WHERE question_id='$row' and test_id='$test_id'", MYSQLI_ASSOC);
		if(!$row || $row['question_text'] != $question_text || $mode == 'e') {
				if($mode == 'a') {
					$result = $conn->prepare("INSERT INTO questions(question_text, question_image, test_id) VALUES(?,?,?)");
					$result->bind_param('ssi', $question_text, $question_image, $test_id);
					$result->execute();
				} else {
					$question_id = fix_string($conn, $_POST['question_id']);
					if($question_image) {
						get_first_query_result($conn, "UPDATE questions SET question_text='$question_text', question_image='$question_image' WHERE question_id='$question_id'");
					} else {
						if(!isset($_POST['ignore_q_image'])) {
							get_first_query_result($conn, "UPDATE questions SET question_text='$question_text' WHERE question_id='$question_id'");
						} else {
							get_first_query_result($conn, "UPDATE questions SET question_text='$question_text', question_image='' WHERE question_id='$question_id'"); 
						}
					}
				}
			}

		$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
		$row_number = $result->num_rows;
		$result->data_seek($row_number-1);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die("Непредвиденная ошибка! Попробуйте заного");
		$question_id = $row[0];
			
		$result = $conn->query("SELECT answer_id FROM answers WHERE question_id='$question_id'");
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) {
			foreach($rights as $index => $mode) {
				$is_right_answer = 0;
				if($mode == 1 || $mode == 3) {
					$is_right_answer = 1;
				}
				if($mode == 0 || $mode == 1) {
					$answer_text = fix_string($conn, $_POST['ans_'.$index]);
				} elseif($mode == 2 || $mode == 3) {
					$answer_text = analize_file($_FILES['img_ans_'.$index]['type'], 'answer', $test_id.$question_id, $index);
					if(!move_uploaded_file($_FILES['img_ans_'.$index]['tmp_name'], '../../material/img/'.$answer_text)) die("Не удалось загрузить файл на сервер!");  
				}
				if((!$_POST['ans_'.$index] && !$_FILES['img_ans_'.$index]['name']) || ($_FILES['img_ans_'.$index]['type'] && isset($_POST['ignore_img_'.$index]) && !$_POST['ans_'.$index])) {
					continue;
				}
				$result = $conn->prepare("INSERT INTO answers(answer_text, answer_order_id, is_right_answer, question_id) VALUE(?,?,?,?)");
				$result->bind_param('siii', $answer_text, $index, $is_right_answer, $question_id);
				$result->execute();  
			}
		} elseif($row[0] && $mode == 'e') {
			foreach($rights as $index => $mode) {
				$is_right_answer = 0;
				if($mode == 1 || $mode == 3) {
					$is_right_answer = 1;
				}
				if($mode == 0 || $mode == 1) {
					$answer_text = fix_string($conn, $_POST['ans_'.$index]);
				} elseif($mode == 2 || $mode == 3) {
					$answer_text = analize_file($_FILES['img_ans_'.$index]['type'], 'answer', $test_id.$question_id, $index);
					if(!move_uploaded_file($_FILES['img_ans_'.$index]['tmp_name'], $img_location.$answer_text)) die("Не удалось загрузить файл на сервер!");  
				}
				if((!$_POST['ans_'.$index] && !$_FILES['img_ans_'.$index]['name']) || ($_FILES['img_ans_'.$index]['type'] && isset($_POST['ignore_img_'.$index]) && !$_POST['ans_'.$index])) {
					continue;
				}
				get_first_query_result($conn, "UPDATE answers SET answer_text='$answer_text', answer_order_id='$index', is_right_answer='$is_right_answer' WHERE question_id='$question_id'");
			}
		}
	}	

	require_once '../db_data.php';
	require_once 'data_analizer.php';
	$conn = get_connection_object('editor');
	
	if(isset($_POST['add_question'])) {
		show_question_editor($conn, 'a');
	}
	
	if(isset($_POST['force_add_question'])) {
		edit_question($conn, 'a');
	}

	if(isset($_POST['edit_question'])) {
		show_question_editor($conn, 'e');	
	}

	if(isset($_POST['force_edit_question'])) {
		edit_question($conn, 'e');
	}

	if(isset($_POST['topic_selection'])) {
		$topic_id = fix_string($conn, trim($_POST['topic_selection']));
		$row = get_first_select_array($conn, "SELECT test_id, subject_id, topic_name FROM topics WHERE topic_id='$topic_id'", MYSQLI_ASSOC);
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
			$test_id = get_first_select_array($conn, "SELECT test_id FROM tests WHERE topic_id='$topic_id'", MYSQLI_NUM)[0];
			$query = "UPDATE topics SET test_id='$test_id' WHERE topic_id='$topic_id'";
			$conn->query($query);	
			create_test_page($location, $topic_name, $test_id); 
			open_editor($conn, $test_id, $topic_name);
		} else {
			$query = "SELECT test_link FROM tests WHERE test_id='$test_id'";
			$result = $conn->query($query);
			if(!$result) die("Ошибка, попробуйте перезагрузить страницу");
			$row = $result->fetch_array(MYSQLI_NUM);
			$test_link = $row[0];
			open_editor($conn, $test_id, $topic_name);
		}
		
	}


	if(isset($_POST['delete_test'])) {
		delete_material($conn, 'test', 'Тест');
	}

	if(isset($_POST['delete_question'])) {
		delete_material($conn, 'question', 'Вопрос'); 
	}

	if(isset($_POST['force_delete_question'])) {
		check_admin($conn, fix_string($conn, $_POST['pass']));
		$question_id = fix_string($conn, $_POST['del_question_id']);
		get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
		get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'");
	}	

	if(isset($_POST['force_delete_test'])) {
		check_admin($conn, fix_string($conn, $_POST['pass']));
		$test_id = fix_string($conn, $_POST['del_test_id']);
		$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
		$row_number = $result->num_rows;
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$question_id = $result->fetch_row()[0];
			get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
			get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'");
		}
		get_first_query_result($conn, "DELETE FROM tests WHERE test_id='$test_id'");
		get_first_query_result($conn, "UPDATE topics SET test_id='0' WHERE test_id='$test_id'");
		header("Location: http://localhost/wos/scripts/editor/"); 
	}
	
	$conn->close();
?>
</body>
</html>
