<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
	<link rel='stylesheet' href='http://localhost/wos/assets/css/styles.css'>
</head>
<body>
	<?php include_once '../../menu.php' ?>
<div class='center-block-main editor'>
	<h2>Редактор материала</h2>
	<ul>
		<li>Последовательно выберите предмет, тему, а затем лекцию для перехода на страницу ее редактирования
		<li>При нажатии кнопки "Изменить" вы сможете поменять название материала или загрузить новое изображение для него
		<li>Для удаления материала, вам нужно будет ввести специальный ключ или отправить по электронной почте соответствующий запрос
		<li>Для отмены выбора нажмите кнопку "Отменить"
	</ul>
	<?php	
		require_once 'data_analizer.php';
		require_once '../session_starter.php';
		include_once 'subject_selection.php';
		include_once 'topic_selection.php';
		include_once 'lection_selection.php';
		include_once 'formatter.php';
		include_once 'test_creator.php';
		$conn = get_connection_object('editor');	
		start_editor_session();

		if(isset($_SESSION['editor']) && $_SESSION['editor'] == 1 && isset($_SESSION['user_id']) && isset($_SESSION['in'])) {
			$id = $_SESSION['user_id'];
			$editor = get_first_select_array($conn, "SELECT editor FROM sign_in WHERE user_id='$id'", MYSQLI_NUM)[0];
			if($editor != 1) {
				die("Простите, но вы не имеете прав на редактирование материала");
			}
		}
			

		echo "<form action='editor.php' method='POST' enctype='multipart/form-data'>";


		
		if(isset($_POST['subject_selection'])) {
			$_SESSION['subject_id'] = fix_string($conn, $_POST['subject_selection']);
		}

		if(isset($_POST['topic_selection'])) {
			$_SESSION['topic_id'] = fix_string($conn, $_POST['topic_selection']);
		}

		if(isset($_POST['lection_selection'])) {
			$_SESSION['lection_id'] = fix_string($conn, $_POST['lection_selection']);
		}

		if(isset($_POST['save']) || isset($_POST['add_image'])) {
			save_lection($conn, $lections_location, $img_location);
		}
		
		if(isset($_POST['select_lection'])) {
			$_SESSION['formatter'] = 1;
		}	

		if(isset($_POST['add_test'])) {
			$_SESSION['test_creator'] = 1;
		}

		if(isset($_POST['cancel'])) {
			if(isset($_SESSION['formatter'])) {
				$_SESSION['formatter'] = 0;
			}
			if(isset($_SESSION['test_creator'])) {
				$_SESSION['test_creator'] = 0;
			}
		}

		if(isset($_SESSION['test_creator']) && $_SESSION['test_creator'] == 1) {
			if(isset($_POST['add_question'])) {
				show_question_editor($conn, 'a');
			} elseif(isset($_POST['force_add_question'])) {
				edit_question($conn, 'a');
			} elseif(isset($_POST['edit_question'])) {
				edit_question($conn, 'e');
			} elseif(isset($_POST['force_edit_question'])) {
				edit_question($conn, 'e');
			} elseif(isset($_POST['delete_test'])) {
				delete_material($conn, 'test', 'Тест');
			} elseif(isset($_POST['delete_question'])) {
				delete_material($conn, 'question', 'Вопрос');
			} elseif(isset($_POST['force_delete_question'])) {
				check_admin($conn, fix_string($conn, $_POST['pass']));
				$question_id = fix_string($conn, $_POST['del_question_id']);
				get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
				get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'"); 
			} elseif(isset($_POST['force_delete_test'])) {
				check_admin($conn, fix_string($conn, $_POST['pass']));
				$test_id = fix_string($conn, $_SESSION['test_id']);
				$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
				$row_number = $result->num_rows;
				for($i = 0; $i < $row_number; ++$i) {
					$result->data_seek($i);
					$question_id = $result->fetch_row()[0];
					get_first_query_result($conn, "DELETE FROM answers WHERE question_id='$question_id'");
					get_first_query_result($conn, "DELETE FROM questions WHERE question_id='$question_id'");
				}
				get_first_query_result($conn, "DELETE FROM tests WHERE tests_id='$test_id'");
				get_first_query_result($conn, "UPDATE topics SET test_id='0' WHERE test_id='$test_id'");
				echo "<p>Удаление прошло удачно!
				<br>Обновите страницу, если изменения еще не были применены</p>";
			}
			add_test($conn, $tests_location);
		}

		if(isset($_SESSION['formatter']) && $_SESSION['formatter'] == 1) {
			open_formatter($conn, $lections_location);
		}

		if(isset($_POST['create_subject'])) {
			new_subject();
		}

		if(isset($_POST['insert_subject'])) {
			insert_subject($conn, $img_location);
		}

		if(isset($_POST['force_edit_subject'])) {
			force_edit_subject($conn);
		}

		if(isset($_POST['create_topic'])) {
			new_topic();
		}

		if(isset($_POST['insert_topic'])) {
			insert_topic($conn);
		}		
	
		if(isset($_POST['force_edit_topic'])) {
			force_edit_topic($conn);
		}

		if(isset($_POST['create_lection'])) {
			new_lection();
		}

		if(isset($_POST['insert_lection'])) {
			insert_lection($conn);
		}

		if(isset($_POST['force_edit_lection'])) {
			force_edit_lection($conn);
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

		if($_SESSION['in'] == 1) {
			subject_page_work($conn);
		}

		if(isset($_POST['select_subject'])) {
			generate_block($conn, 'topic', 'subject', $_SESSION['subject_id'], 'предмет');
		}

		if(isset($_POST['select_topic'])) {
			generate_block($conn, 'lection',  'topic', $_SESSION['topic_id'], 'тема');
		}


		echo "<br><input name='cancel' class='cancel' type='submit' value='Отменить'></form>";
		$conn->close();

	function generate_block($conn, $item, $pre_block_name, $pre_select_id, $pre_block_text_type) {
		$pre_select_name = get_first_select_array($conn, "SELECT $pre_block_name"."_name FROM $pre_block_name"."s WHERE $pre_block_name"."_id='$pre_select_id'",
							MYSQLI_NUM)[0];
	
		$_SESSION[$pre_block_name] = $pre_select_name; 	
		echo "<hr>
		<p>Выбран(-a) $pre_block_text_type</p> <h3><b>'$pre_select_name'</b></h3>
		<hr>";
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
				echo "<option value='".$row[$item.'_id']."'";
				if(isset($_SESSION[$item.'_id']) && $_SESSION[$item.'_id'] == $row[$item.'_id']) {
					echo " selected ";
				}
				echo ">".$row[$item.'_name']."</option>";
			}
			echo "</select>";
			echo "<button type='submit' name='select_$item'></button>";
			if($item == 'topic') {
				echo "<br><input type='submit' name='add_test' value='Добавить/изменить тест' style='width:200px;'>";
			}
			echo "<br><input type='submit' name='delete_$item' value='Удалить' style='width:200px'>
			<br><input type='submit' name='edit_$item' value='Изменить' style='width:200px'><br>";
		}
			echo "<input type='submit' name='create_$item' value='Добавить новую' style='width:200px'>";
	}	



	function force_delete_material($conn, $item) {
		$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
		if(!$is_right) die();
		$del_item_id = fix_string($conn, trim($_SESSION["$item"."_id"]));
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
		echo "<br><p>Удаление прошло успешно!
			<br>Обновите страницу, если материал до сих пор в списке!</p><br>";
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
		echo "<p>Изменить $item_type <b>'".$row[$item.'_name']."'</b></p>
		<br><label for='e_$item"."_name'>Новое название:</label><br>
		<input type='text' name='e_$item"."_name' value='".$row[$item.'_name']."'>";
		if($item != 'lection') {
			echo "<br><br><label for='e_$item"."_image'>Новое изображение:</label><br>
			<input type='file' name='e_$item"."_image' value='default'><br>";
		}
		if($item == 'topic') {
			echo "<br><label for='e_attack_image'>Новое изображения атаки босса:  </label>
			<input type='file' name='e_attack_image' value='default'><br>
			<br><label for='e_fail_image'>Новое изображение повреждения босса:  </label>
			<input type='file' name='e_fail_image' value='default'><br>";
		}
		echo "<br><input type='submit' name='force_edit_$item' value='Принять изменения'><br>";
		}

	?>
</div>
<?php include_once '../../footer.php' ?>
</body>
</html>

