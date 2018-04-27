<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
	<link rel='stylesheet' href='../../assets/css/styles.css'>
	<style>
		li {
			font-size:18pt;
			font-family: 'Times New Roman', sans-serif;
		}
	</style>
</head>
<body>
	<?php session_start(); ?>
	<?php include_once '../../menu.php' ?>
	<h2>Редактор материала сайта вас приветствует!</h2>
	<ul>
		<li>Последовательно выберите предмет, тему, а затем лекцию для перехода на страницу ее редактирования
		<li>При нажатии кнопки "Изменить" вы сможете поменять название материала или загрузить новое изображение для него
		<li>Для удаления материала, вам нужно будет ввести специальный ключ или отправить по электронной почте соответствующий запрос
		<li>Для отмены выбора нажмите кнопку "Отменить"
	</ul>
<fieldset>
	<?php	
		require_once '../db_data.php';
		require_once 'data_analizer.php';
		require_once '../session_starter.php';
		include_once "subject_selection.php";
		$conn = get_connection_object('editor');	
		start_editor_session();

		if($_SESSION['in'] == 1) {
			subject_page_work($conn);
		}
		
		
		if(isset($_POST['select_subject']) && isset($_POST['subject_selection'])) {
			$_SESSION['subject_selection'] = fix_string($conn, $_POST['subject_selection']);
			generate_block($conn, 'topic', 'subject', $_SESSION['subject_selection'], 'предмет');
		}

		if(isset($_POST['select_topic']) && $_POST['topic_selection']) {
			$_SESSION['topic_selection'] = fix_string($conn, $_POST['topic_selection']);
			generate_block($conn, 'lection',  'topic', $_SESSION['topic_selection'], 'тема');
		}

	
		if(isset($_POST['delete_subject'])) {
			delete_material($conn, 'subject', 'Предмет');		
		}
	
		if(isset($_POST['delete_topic'])) {
			delete_material($conn, 'topic', 'Тема');		
		}
		
		if(isset($_POST['delete_lection'])) {
			delete_material($conn, 'lection', 'Лекция');	
		}
	
		if(isset($_POST['force_delete_subject'])) {
			force_delete_material($conn, "subject");		 
		} 

		if(isset($_POST['force_delete_topic'])) {
			force_delete_material($conn, "topic");
		}
	
		if(isset($_POST['force_delete_lection'])) {
			force_delete_material($conn, "lection");
		}
	
		if(isset($_POST['edit_subject'])) {
			edit_material($conn, 'subject');
		}		
		
		if(isset($_POST['edit_topic'])) {
			edit_material($conn, 'topic');
		}
		
		if(isset($_POST['edit_lection'])) {
			edit_material($conn, 'lection');
		}
	
		if(isset($_POST['send_del_message'])) {
			$email = trim(fix_string($conn, $_POST['email']));
			if(!preg_match('~.+@.+\..+~i', $email)) die("Неверная электронная почта");
			$message = trim(fix_string($conn, $_POST['message']));
			require_once '../sender.php';
			send_mail('trymote@mail.ru', $email, $message, 3); 
		}
		$conn->close();
		echo "<br><form action='editor.php' method='POST'>
			<input type='submit' value='Отменить'>	
		</form>";

	function generate_block($conn, $item, $pre_block_name, $pre_select_id, $pre_block_text_type) {
		$pre_select_name = get_first_select_array($conn, "SELECT $pre_block_name"."_name FROM $pre_block_name"."s WHERE $pre_block_name"."_id='$pre_select_id'",
							MYSQLI_NUM);
		echo "<hr>
		<p>Выбран(-a) $pre_block_text_type</p> <h3><b>'$pre_select_name[0]'</b></h3>
		<hr>
		<fieldset>";	
		$result = get_first_query_result($conn, "SELECT * FROM $item"."s WHERE $pre_block_name"."_id='$pre_select_id'");
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if(!$row[$item.'_name'] && !$row[$item.'_id']) {
			switch($item) {
				case "topic": 
					echo "<p>Для данного предмета еще не добавлено тем</p><br>";
					break;
				case "lection":
					echo "<p>Для данной темы еще не добавлено лекций</p><br>";
					break;
			}
		} else {
			echo "<form action='editor.php' method='POST'>";
			if($item === 'lection') {
				echo "<h3>Лекции:</h3>";
			} else {
				echo "<h3>Темы:</h3>";
			}
			$row_number = $result->num_rows;
			echo "<br><select name='$item"."_selection'>";
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row[$item.'_id']."'>".$row[$item.'_name']."</option>";
			}
			echo "</select>";
			if($item == 'lection') {
				echo "<input type='submit' name='select_$item' formaction='formatter.php' value='Выбрать'><br>";
			} else {
				echo "<input type='submit' name='select_$item' value='Выбрать'><br>
				<input type='submit' name='add_test' value='Добавить/изменить тест' formaction='test_creator.php' style='width:200px;'>";
			}
			echo "<br><input type='submit' name='delete_$item' value='Удалить' style='width:200px'>
			<br><input type='submit' name='edit_$item' value='Изменить' style='width:200px'>
			</form>";
		}
		echo "<form action='$item"."_selection.php' method='POST'>";
				echo "<input type='text' name='chosen_$pre_block_name"."_name' value='$pre_select_name[0]' style='display:none'>
			<input type='text' name='chosen_$pre_block_name"."_id' value='$pre_select_id' style='display:none'>
			<input type='submit' name='create_$item' value='Добавить новую' style='width:200px'>
		</form>";
		echo "</fieldset>";
	}	



	function force_delete_material($conn, $item) {
		$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
		if(!$is_right) die();
		$del_item_id = fix_string($conn, trim($_POST["del_$item"."_id"]));
		if($item === "subject") {
			$result = get_first_query_result($conn, "SELECT topic_id FROM topics WHERE subject_id='$del_item_id'");
			$row_number = $result->num_rows;
			for($i=0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$topic_id = $result->fetch_row()[0];
				$test_id = get_first_select_array($conn, "SELECT test_id FROM tests WHERE topic_id='$topic_id'", MYSQLI_NUM)[0];
				if($test_id) {
					$q_result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
					$row_number = $q_result->num_rows;
					for($i = 0; $i < $row_number; ++$i) {
						$q_result->data_seek($i);
						$question_id = $q_result->fetch_row()[0];
						$q_result = get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
						$q_result = get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'");
					}
				}
				get_first_query_result($conn, "DELETE FROM tests WHERE topic_id='$topic_id'"); 
				get_first_query_result($conn, "DELETE FROM lections WHERE topic_id='$topic_id'");
				get_first_query_result($conn, "DELETE FROM topics WHERE topic_id='$topic_id'");	
			}
			get_first_query_result($conn, "DELETE FROM subjects WHERE subject_id='$del_item_id'");
		} elseif($item === "topic") {
			get_first_query_result($conn, "DELETE FROM lections WHERE topic_id='$del_item_id'");
			$test_id = get_first_select_array($conn, "SELECT test_id FROM tests WHERE topic_id='$del_item_id'", MYSQLI_NUM)[0];
			if($test_id) {
				$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
				$row_number = $result->num_rows;
				for($i = 0; $i < $row_number; ++$i) {
					$result->data_seek($i);
					$question_id = $result->fetch_row()[0];
					get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
					get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'");
				}
			}
			get_first_query_result($conn, "DELETE FROM tests WHERE topic_id='$del_item_id'");
			get_first_query_result($conn, "DELETE FROM topics WHERE topic_id='$del_item_id'");
		} elseif($item === "lection") {
			get_first_query_result($conn, "DELETE FROM lections WHERE lection_id='$del_item_id'");
		}
		echo "<p>Удаление прошло успешно!</p><br>";
	}
	
	function edit_material($conn, $item) {
		$item_type;
		switch($item) {
			case 'subject':
				$item_type = 'предмет';
				break;
			case 'topic':
				$item_type = 'тему';
				break;
			case 'lection':
				$item_type = 'лекцию';
				break;
		}
		$item_id = fix_string($conn, trim($_POST[$item.'_selection']));
		$row = get_first_select_array($conn, "SELECT * FROM $item"."s WHERE $item"."_id='$item_id'", MYSQLI_ASSOC);
		echo "<form action='$item"."_selection.php' method='POST' enctype='multipart/form-data'>
		<p>Изменить $item_type <b>'".$row[$item.'_name']."'</b></p><br>
		<br><label for='e_$item"."_name'>Новое название:  </label>
		<input type='text' name='e_$item"."_name' value='".$row[$item.'_name']."'><br>";
		if($item != 'lection') {
			echo "<br><label for='e_$item"."_image'>Новое изображение:  </label>
			<input type='file' name='e_$item"."_image' value='default'><br>";
		}
		if($item == 'topic') {
			echo "<br><label for='e_attack_image'>Новое изображения атаки босса:  </label>
			<input type='file' name='e_attack_image' value='default'><br>
			<br><label for='e_fail_image'>Новое изображение повреждения босса:  </label>
			<input type='file' name='e_fail_image' value='default'><br>";
		}
		echo "<br><input type='submit' name='force_edit_$item' value='Принять изменения'>
			<input type='text' name='e_$item"."_id' value='$item_id' style='display:none'>
		</form>"; 
	}

	?>
</fieldset>
<?php include_once '../../footer.php' ?>
</body>
</html>

