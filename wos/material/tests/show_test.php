<?php

function show_test($test_id) {
	$conn = get_connection_object('tests');
	$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	$user_ip = $_SERVER['REMOTE_ADDR'];
	if(!strpos($_SERVER['HTTP_REFERER'], $test_link)) {
		$exist = get_first_select_array($conn, "SELECT user_ip FROM test_progress WHERE user_ip='$user_ip'", MYSQLI_NUM)[0];
		if($exist) {
			$date = get_first_select_array($conn, "SELECT test_progress_date FROM test_progress WHERE user_ip='$user_ip'", MYSQLI_NUM)[0];
			if(strpos($date, date('d'))) {
				die("<form action='$test_link' method='POST'>
				<input type='submit' name='start_again' value='Начать заного'>
				<input type='submit' name='continue' value='Продолжить'>
				</form>");
			}
		} else {
			$result = $conn->prepare("INSERT INTO test_progress(test_id, user_ip) VALUES(?,?)");
			$user_ip .= substr(crypt($user_ip, $user_ip), 0, 6);
			$result->bind_param('is', $test_id, $user_ip);
			$result->execute();
			$test_progress_id = get_first_select_array($conn, "SELECT test_progress_id FROM test_progress WHERE test_id='$test_id' AND user_ip='$user_ip'", MYSQLI_NUM)[0];
			die("<form action='$test_link' method='POST'>
			<input type='hidden' name='test_progress_id' value='$test_progress_id'>
			<input type='submit' name='go' value='В бой'>
			</form>");
		}
	}
	if(isset($_POST['start_again'])) { 	
		get_first_query_result($conn, "UPDATE test_progress SET test_id='$test_id', 
						health_counter='5', 
						coin_counter='0', 
						done_index='0', 
						test_progress_date='".date(DATE_RSS)."' 
					WHERE user_ip='$user_ip'");
	} 
	$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	echo "<form action='$test_link' method='POST'>";
	$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
	$row_number = $result->num_rows;
	if($row_number) {
		$reload = false;
		if(isset($_POST['next_q']) && !isset($_POST['again']) || isset($_POST['pass'])) {
			$setted_index = fix_string($conn, $_POST['done_index']);
			$reload = check_answer($conn);
			$done_index = ($reload)? $setted_index-1 : $setted_index;
			if(isset($_POST['pass'])) {
				$done_index++;
				$reload = false;
			}
		}
		show_result_line($conn, $result, $row_number, $done_index);
		$result->data_seek($done_index);
		$row = $result->fetch_array(MYSQLI_NUM);
		if($done_index == $row_number-1) {
			show_question($conn, $row[0], $done_index, $reload, true);
		} elseif($done_index < $row_number) {
			show_question($conn, $row[0], $done_index, $reload, false);
		} else {
			show_congrates();
		}
	} else {
		echo "Ни одного вопроса не добавлено!";
	}

	$result->close();
	$conn->close();	
}

function show_congrates() {
	$CONGRATES = array("Сессия уже начинает дрожать от страха!", "Я горжусь тобой.", "+10 к интеллекту!", "Ты достоен пиццы! Пора пойти и заказать!", 
	"Сайт не был готов к такому!", "Можешь рассказать об этом одногруппникам!!! Представляешь!", "Ого, ты дошел до этой страницы!? Она же была сделана только на всякий случай!");
	$rand_index = rand(0, count($CONGRATES)-1);
	echo "<div class='congrats'>
	<h2>Поздравляем! Ты прошел тест!</h2>";
	echo "<h3 style='color:green;'>".$CONGRATES[$rand_index]."</h3></div>";
	echo "<input type='submit' name='again' value='Повторить'>
	</form>";
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

function show_question($conn, $question_id, $done_index, $reload, $last) {
	require_once '../../scripts/editor/data_analizer.php';
	$row = get_first_select_array($conn, "SELECT question_text, question_image, test_id FROM questions WHERE question_id='$question_id'", MYSQLI_NUM);
	echo "<div class='question_text'>
		<p>$row[0]</p>
	</div>";
	if($row[1]) {
		echo "<div class='question_image'>
			<img src='$img_location$row[1]' alt='$row[1]'>
		</div>";
	}
	$result = get_first_query_result($conn, "SELECT answer_text, answer_order_id, is_right_answer FROM answers WHERE question_id='$question_id'");
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
		$trys = fix_string($conn, $_POST['trys']);
		if($trys > 1) {
			$hint = null;
			$result = $conn->query("SELECT question_hint FROM questions WHERE question_id='$question_id'");
			if($result) $hint = $result->fetch_row()[0];
			echo "<h4 style='color:green;'>Подсказка!</h4>";
			if(!$hint) {
				echo "<p style='color:green;'>Можешь снова почитать лекции или попробовать еще пару раз ответить на вопрос
				<br>Экстренный пропуск вопроса появиться через две неверные попытки!</p>
				<p style='font-size:10pt;color:green;'>Появиться... Если это не последний вопрос</p>";
			} else {
				echo "<p style='color:green;'>$hint</p>";
			}
		}
		if($trys > 3) {
			die("Очень жаль, но ты проиграл");
		}
		$trys++;
		echo "<p style='color:red;'>".$MISTAKES[$rand_index]."</p>
		<input type='hidden' name='trys' value='$trys'>";
	} else {
		echo "<input type='hidden' name='trys' value='0'>";
	}
	
	$done_index++;
	echo "<input type='hidden' name='done_index' value='$done_index'>
	<input type='hidden' name='id' value='$question_id'>";
	echo "<input type='submit' name='next_q' value='Далее'>";
	echo "</form>"; 

}


function show_result_line($conn, $result, $row_number, $done_index) {
	if($done_index < $row_number) {
		$max_hp = $row_number * 100;
		$hp = (($row_number - $done_index) * 99)-1;
		$blood_block = ($row_number * 99)-1;
		echo "
	<div 'health-bar'>
	$hp HP
		<div class='health-form' style='background-color:#000; 
						width:$max_hp"."px; 
						border-radius:50px; 
						position:absolute;
						height:20px;
						color:white;'>
			<div class='blood-health' style='background-color:red; 
							width:$blood_block"."px;
							position:absolute;
							top:2.5px;
							left:2.5px;
							border-radius:50px;
							height:15px;'>
			
			</div>
	
			<div class='left-health' style='background-color:green; 
							width:$hp"."px;
							position:absolute;
							top:2.5px;
							left:2.5px;
							border-radius:50px;
							height:15px;'>
			
			</div>
			
		</div>
	</div>";	
	}
}

?>
