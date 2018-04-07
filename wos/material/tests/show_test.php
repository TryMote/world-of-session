<?php

function show_test($test_id) {
	$data = get_db_data('tests');
	$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");
	
	$result = $conn->query("SELECT question_id FROM questions WHERE test_id='$test_id'");
	$row_number = $result->num_rows;
	if($row_number) {
		show_result_line($conn, $row_number);
		if(!isset($_POST['next_q'])) {
			$result->data_seek(0);
			$row = $result->fetch_array(MYSQLI_NUM);
			show_question($conn, $row[0]);
		}
	} else {
		echo "Ни одного вопроса не добавлено!";
	}

	$result->close();
	$conn->close();	
}

function show_question($conn, $question_id) {
	require_once '../../scripts/editor/data_analizer.php';
	$result = $conn->query("SELECT question_text, question_image FROM questions WHERE question_id='$question_id'");
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
			<input type='text' name='$row[1]'>
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
				echo "<li><input type='checkbox' name='answer' value='$row[1]'>$answer_text";
			} else {
				echo "<li><input type='radio' name='answer' value='$row[1]'>$answer_text";
			}
		}
		echo "</ol></div>";
	} 

}

function show_result_line($conn, $row_number) {


}

?>
