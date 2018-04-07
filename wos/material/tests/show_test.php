<?php

function show_test($test_id) {
	$done_index = 0;
	$data = get_db_data('tests');
	$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");
	$result = $conn->query("SELECT test_link FROM tests WHERE test_id='$test_id'");
	$row = $result->fetch_array(MYSQLI_NUM);
	echo "<form action='$row[0]' method='POST'>";
	$result = $conn->query("SELECT question_id FROM questions WHERE test_id='$test_id'");
	$row_number = $result->num_rows;
	if($row_number) {
		if(isset($_POST['next_q'])) {
			$setted_index = fix_string($conn, $_POST['done_index']);
			$reload = check_answer($conn);
			$done_index = ($reload)? $setted_index-1 : $setted_index;
		}
		if($done_index < $row_number) {
			show_result_line($conn, $result, $row_number);
			$result->data_seek($done_index);
			$row = $result->fetch_array(MYSQLI_NUM);
			show_question($conn, $row[0], $done_index, $reload);
		} else {
			
		}
	} else {
		echo "Ни одного вопроса не добавлено!";
	}

	$result->close();
	$conn->close();	
}

function check_answer($conn) {
	$reload = false;
	$prev_q_id = fix_string($conn, $_POST['id']);
	if(isset($_POST['single_answer'])) {
		if($_POST['single_answer']) {
			$input = fix_string($conn, trim($_POST['single_answer']));
			$result = $conn->query("SELECT answer_text FROM answers WHERE question_id='$prev_q_id'");
			$row = $result->fetch_array(MYSQLI_NUM);
			if(trim(strtolower($row[0])) != strtolower($input)) {
				$reload = true;
			} 
		} else {
			$reload = true;
		}
	} elseif(isset($_POST['radio_answer'])) {			
		if($_POST['radio_answer']) {
			$input = fix_string($conn, strtolower(trim($_POST['radio_answer'])));
			$result = $conn->query("SELECT is_right_answer FROM answers WHERE question_id='$prev_q_id' AND answer_order_id='$input'");
			if(!$result) $reload = true;
			$row = $result->fetch_array(MYSQLI_NUM);
			if($row[0] != 1) {
				$reload = true;
			}
		} else {
			$reload = true;
		}
	} elseif(isset($_POST['check_answer'])) {
		if($_POST['check_answer']) {
			$input = $_POST['check_answer'];
			$result = $conn->query("SELECT COUNT(is_right_answer) FROM answers WHERE question_id='$prev_q_id' AND is_right_answer='1'");
			$real_rights = $result->fetch_row()[0];
			$input_rights = 0; 
			foreach($input as $order_id) {
				$order_id = fix_string($conn, $order_id);
				$result = $conn->query("SELECT is_right_answer FROM answers WHERE question_id='$prev_q_id' AND answer_order_id='$order_id'");
				$is_it_right = $result->fetch_row()[0];
				if($is_it_right == 0) {
					break;
				}
				$input_rights++;
			}
			if($input_rights != $real_rights) {
				$reload = true;
			}
		} else {
			$reload = true;
		}
	} else {
		$reload = true;
	}
	return $reload;
}

function show_question($conn, $question_id, $done_index, $reload) {
	require_once '../../scripts/editor/data_analizer.php';
	$result = $conn->query("SELECT question_text, question_image, test_id FROM questions WHERE question_id='$question_id'");
	$row = $result->fetch_array(MYSQLI_NUM);
	
	echo "<div class='question_text'>
		<p>$row[0]</p>
	</div>";
	if($row[1]) {
		echo "<div class='question_image'>
			<img src='$img_location$row[1]' alt='$row[1]'>
		</div>";
	}
	$result = $conn->query("SELECT answer_text, answer_order_id, is_right_answer FROM answers WHERE question_id='$question_id'");
	$row_number = $result->num_rows;
	if($row_number == 1) {
		$row = $result->fetch_array(MYSQLI_NUM);
		echo "<div class='answer_text'>
			<input type='text' name='single_answer'>
		</div>";
	} else {
		$count_right = 0;
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			if($row[2]) {
				$count_right++;
			}
		}
		echo "<div class='answer_block'><ol>";
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			$answer_text = '';
			if(strpos($row[0], '_answer_')) {
				$answer_text = "<img src='http://localhost/wos/material/img/$row[0]' alt='$row[0]'>";
			} else {
				$answer_text = "$row[0]";
			}
			if($count_right > 1) {
				echo "<li><input type='checkbox' name='check_answer[]' value='$row[1]'>$answer_text";
			} else {
				echo "<li><input type='radio' name='radio_answer' value='$row[1]'>$answer_text";
			}
		}
		echo "</ol></div>";
	}
	if($reload) {
		$MISTAKES = array("Подуймай еще!", "Будь внимательнее", "Еще раз?", "Нет", "Мы подождем...", "Что-то не так в твоем ответе");
		$rand_index = rand(0, count($MISTAKES)-1);
		echo "<p style='color:red;'>".$MISTAKES[$rand_index]."</p>";
	}
	$done_index++;
	echo "<input type='hidden' name='done_index' value='$done_index'>
	<input type='hidden' name='id' value='$question_id'>
	<input type='submit' name='next_q' value='Далее'>
	</form>"; 

}

function show_result_line($conn, $result, $row_number) {
	for($i = 0; $i < $row_number; ++$i) {

	}	
}

?>
