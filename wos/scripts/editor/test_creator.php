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
			echo "<p>Ни одного вопроса не добавлено</p>";
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
		<form action='test_creator.php' method='POST'>
		<input type='hidden' name='topic_selection' value='".fix_string($conn, $_POST['topic_selection'])."'>
		<label for='question_text'>Вопрос:</label><br>
		<input type='text' name='question_text' placeholder='Сколько цифр в числе ПИ?'>";  	
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
		header("Location: ".$tests_location.fix_string($conn, $_POST['filename']));
	}	
	
	$conn->close();
?>
</body>
</html>
