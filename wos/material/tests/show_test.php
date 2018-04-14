<?php

function show_test($test_id) {
	session_start();
	if(!isset($_SESSION['test_id'])) {
		require_once 'test_session.php';
		start_test($test_id);
	}
	$conn = get_connection_object('tests');
	$done_index = $_SESSION['done_index'];
	$health = $_SESSION['health'];
	$coins = $_SESSION['coins'];
	$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	$question_id = get_select_array($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'", $done_index, MYSQLI_NUM)[0];
	if(isset($_POST['next_q_'.$done_index])) {
		if(check_answer($conn, $question_id)) {
			$done_index++;
			$coins++;
		} else {
			$health--;
			show_boss_attack($conn, $test_id);
		}
	}

	if(isset($_POST['buy_health'])) {
		if($coins >= 2) {
			$health++;
			$coins -= 2;
		}
	}

	if(isset($_POST['buy_pass'])) {
		if($coins >= 3) {
			$done_index++;
			$coins -= 3;
		}
	}	

	$_SESSION['done_index'] = $done_index;
	$_SESSION['health'] = $health;
	$_SESSION['coins'] = $coins;

	$result = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
	$row_number = $result->num_rows;
	if($row_number == 0) {
		die("NO");
	} elseif($done_index >= $row_number) {
		show_congrates($test_link);
	} elseif($health <= 0) {
		require_once 'test_session.php';
		stop_test();
		die("END"); 
	} else {
		show_boss($conn, $test_id, $done_index-1);
		show_result($conn, $row_number, $done_index, $health, $coins);
		show_store($test_link);
		echo "<form action='$test_link' method='POST'>";
		show_question($conn, $test_id, $done_index);
	}
	$result->close();
	$conn->close();	
}

function show_boss_attack($conn, $test_id) {
	require_once '../../scripts/editor/data_analizer.php';
	$boss_attack = get_first_select_array($conn, "SELECT topic_attack FROM topics WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	$boss_name = get_first_select_array($conn, "SELECT topic_name FROM topics WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	echo "<div class='boss_image_attack'>";
	if($boss_attack != 'default') {
		echo "<img src='$topic_img_location$boss_attack'>";
	} else {
		echo "<p>$boss_name атакует!</p>";
	}
	echo "</div>";
}

function show_boss($conn, $test_id, $last_index) {
	require_once '../../scripts/editor/data_analizer.php';
	$boss_name = get_first_select_array($conn, "SELECT topic_name FROM topics WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	if(!isset($_POST['next_q_'.$last_index])) {
		echo "<div class='boss_images'>";
		$boss_image = get_first_select_array($conn, "SELECT topic_image FROM topics WHERE test_id='$test_id'", MYSQLI_NUM)[0];
		if($boss_image != 'default') {
			echo "<img width='500px' height='300px' src='$topic_img_location$boss_image'>";
		} else {
			echo "<p>$boss_name приготовился к вашей атаке!</p>";
		}	
		echo "</div>";
	}	
}

function show_store($test_link) {
	echo "<div class='test-store'>
		<form action='$test_link' method='POST'>
			<input type='submit' name='buy_health' value='' 
				style=' background-image:url(idea_plus.png); 
					height:60px; 
					width:54px;
					background-repeat:no-repeat;'>
			
			<input type='submit' name='buy_pass' value='' 
				style=' background-image:url(buy_pass.png); 
					height:60px; 
					width:54px;
					background-repeat:no-repeat;'>

		</form>
	</div>";
}

function show_congrates($test_link) {
	$CONGRATES = array("Сессия уже начинает дрожать от страха!", "Я горжусь тобой.", "+10 к интеллекту!", "Ты достоен пиццы! Пора пойти и заказать!", 
	"Сайт не был готов к такому!", "Можешь рассказать об этом одногруппникам!!! Представляешь!", "Ого, ты дошел до этой страницы!? Она же была сделана только на всякий случай!");
	$rand_index = rand(0, count($CONGRATES)-1);
	echo "<div class='congrats'>
	<h2>Поздравляем! Ты прошел тест!</h2>";
	echo "<h3 style='color:green;'>".$CONGRATES[$rand_index]."</h3></div>";
	echo "<form action='$test_link' method='POST'>
	<input type='submit' name='again' value='Повторить'>
	</form>";
	require_once 'test_session.php';
	stop_test();
}

function check_answer($conn, $question_id) {
	if(isset($_POST['single_answer'])) {
		if($_POST['single_answer']) {
			$input = fix_string($conn, trim($_POST['single_answer']));
			$answer = get_first_select_array($conn, "SELECT answer_text FROM answers WHERE question_id='$question_id'", MYSQLI_NUM)[0];
			if(trim(strtolower($answer)) != strtolower($input)) {
				return false;
			} 
		} else {
			return false;
		}
	} elseif(isset($_POST['radio_answer'])) {			
		if($_POST['radio_answer']) {
			$input = fix_string($conn, strtolower(trim($_POST['radio_answer'])));
			$result = $conn->query("SELECT is_right_answer FROM answers WHERE question_id='$question_id' AND answer_order_id='$input'");
			if(!$result) return false;
			$row = $result->fetch_array(MYSQLI_NUM);
			if($row[0] != 1) {
				return false;
			}
		} else {
			return false;
		}
	} elseif(isset($_POST['check_answer'])) {
		if($_POST['check_answer']) {
			$input = $_POST['check_answer'];
			$result = $conn->query("SELECT COUNT(is_right_answer) FROM answers WHERE question_id='$question_id' AND is_right_answer='1'");
			$real_rights = $result->fetch_row()[0];
			$input_rights = 0; 
			foreach($input as $order_id) {
				$order_id = fix_string($conn, $order_id);
				$result = $conn->query("SELECT is_right_answer FROM answers WHERE question_id='$question_id' AND answer_order_id='$order_id'");
				$is_it_right = $result->fetch_row()[0];
				if($is_it_right == 0) {
					break;
				}
				$input_rights++;
			}
			if($input_rights != $real_rights) {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
	return true;
}

function show_question($conn, $test_id, $done_index) {
	require_once '../../scripts/editor/data_analizer.php';
	$row = get_select_array($conn, "SELECT question_text, question_image, question_id FROM questions WHERE test_id='$test_id'", $done_index, MYSQLI_NUM);
	echo "<div class='question_text'>
		<p>$row[0]</p>
	</div>";
	if($row[1]) {
		echo "<div class='question_image'>
			<img src='$img_location$row[1]' alt='$row[1]'>
		</div>";
	}
	$result = get_first_query_result($conn, "SELECT answer_text, answer_order_id, is_right_answer FROM answers WHERE question_id='$row[2]'");
	$row_number = $result->num_rows;
	if($row_number == 1) {
		$row = $result->fetch_array(MYSQLI_NUM);
		echo "<div class='answer_text'>
			<input type='text' autofocus name='single_answer'>
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
	echo "<input type='submit' name='next_q_$done_index' value='Удар'>
	</form>"; 

}


function show_result($conn, $row_number, $done_index, $health, $coins) {

	if($done_index < $row_number) {
		$max_hp = ($row_number * 59);
		$hp = (($row_number - $done_index) * 59);
		echo "
	<div 'health-bar'>
		
		<p>$hp HP</p>
		<div class='health-form' style='background-color:red; 
						width:$max_hp"."px; 
						position:absolute;
						height:20px;
						color:white;'>
			<div class='left-health' style='background-color:#8bc34a; 
							width:$hp"."px;
							position:absolute;
							height:20px;'>
			
			</div>
			
		</div>
	</div>
<br>	<div class='user-status'>
<div class='user-health'>";
	for($i = 0; $i < $health; $i++) {
		echo '
<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
     width="50" height="50"
     viewBox="0 0 48 48"
     style="fill:#13B753;"><g id="surface1"><path style=" fill:#FFF59D;" d="M 44 22 C 44 33.046875 35.046875 42 24 42 C 12.953125 42 4 33.046875 4 22 C 4 10.953125 12.953125 2 24 2 C 35.046875 2 44 10.953125 44 22 Z "></path><path style=" fill:#FBC02D;" d="M 37 22 C 37 14.300781 30.398438 8.199219 22.5 9.101563 C 16.5 9.800781 11.699219 14.601563 11.101563 20.601563 C 10.601563 25.199219 12.5 29.300781 15.699219 31.898438 C 17.101563 33.101563 18 34.800781 18 36.699219 L 18 37 L 30 37 L 30 36.898438 C 30 35.101563 30.800781 33.300781 32.199219 32.101563 C 35.101563 29.699219 37 26.101563 37 22 Z "></path><path style=" fill:#FFF59D;" d="M 30.601563 20.199219 L 27.601563 18.199219 C 27.300781 18 26.800781 18 26.5 18.199219 L 24 19.800781 L 21.601563 18.199219 C 21.300781 18 20.800781 18 20.5 18.199219 L 17.5 20.199219 C 17.300781 20.398438 17.101563 20.601563 17.101563 20.898438 C 17.101563 21.199219 17.101563 21.5 17.300781 21.699219 L 21.101563 26.398438 L 21.101563 37 L 23.101563 37 L 23.101563 26 C 23.101563 25.800781 23 25.601563 22.898438 25.398438 L 19.601563 21.300781 L 21.101563 20.300781 L 23.5 21.898438 C 23.800781 22.101563 24.300781 22.101563 24.601563 21.898438 L 27 20.300781 L 28.5 21.300781 L 25.199219 25.398438 C 25.101563 25.601563 25 25.800781 25 26 L 25 37 L 27 37 L 27 26.398438 L 30.800781 21.699219 C 31 21.5 31.101563 21.199219 31 20.898438 C 30.898438 20.601563 30.800781 20.300781 30.601563 20.199219 Z "></path><path style=" fill:#5C6BC0;" d="M 27 44 C 27 45.65625 25.65625 47 24 47 C 22.34375 47 21 45.65625 21 44 C 21 42.34375 22.34375 41 24 41 C 25.65625 41 27 42.34375 27 44 Z "></path><path style=" fill:#9FA8DA;" d="M 26 45 L 22 45 C 19.800781 45 18 43.199219 18 41 L 18 36 L 30 36 L 30 41 C 30 43.199219 28.199219 45 26 45 Z "></path><path style=" fill:#5C6BC0;" d="M 30 41 L 18.398438 42.601563 C 18.699219 43.300781 19.300781 44 20 44.398438 L 29.398438 43.101563 C 29.800781 42.5 30 41.800781 30 41 Z "></path><path style=" fill:#5C6BC0;" d="M 18 38.699219 L 18 40.699219 L 30 39 L 30 37 Z "></path></g></svg>
';
	}
echo "</div>
	<div class='user-coins'>";
	for($i = 0; $i < $coins; ++$i) {
		echo '

<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
     width="50" height="50"
     viewBox="0 0 252 252"
     style="fill:#f39c12;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="none" stroke-linecap="butt" stroke-linejoin="none" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,252v-252h252v252z" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="M126,252c-69.58788,0 -126,-56.41212 -126,-126v0c0,-69.58788 56.41212,-126 126,-126v0c69.58788,0 126,56.41212 126,126v0c0,69.58788 -56.41212,126 -126,126z" fill="#ffffff" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><g><g id="surface1"><path d="M141.59249,126c0,8.60836 -6.98414,15.5925 -15.5925,15.5925c-8.60836,0 -15.5925,-6.98414 -15.5925,-15.5925c0,-8.60836 6.98414,-15.5925 15.5925,-15.5925c8.60836,0 15.5925,6.98414 15.5925,15.5925z" fill="#ffb300" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="M125.99999,32.445c-20.50576,0 -35.97645,40.21972 -35.97645,93.555c0,53.33528 15.47069,93.555 35.97645,93.555c20.50576,0 35.97645,-40.21972 35.97645,-93.555c0,-53.33528 -15.47068,-93.555 -35.97645,-93.555z" fill="none" stroke="#3f51b5" stroke-width="10.5" stroke-linejoin="round"></path><path d="M44.99209,79.2225c-10.25288,17.76489 16.83097,51.2644 63.01969,77.9219c46.16842,26.67779 88.72295,33.398 98.97583,15.6331c10.25288,-17.76489 -16.83097,-51.2644 -62.99938,-77.9422c-46.18872,-26.67779 -88.74325,-33.3777 -98.99613,-15.6128z" fill="none" stroke="#3f51b5" stroke-width="10.5" stroke-linejoin="round"></path><path d="M45.01239,172.7572c10.25288,17.7852 52.78711,11.06499 98.97583,-15.6128c46.16842,-26.65749 73.27257,-60.157 63.01969,-77.9219c-10.25288,-17.76489 -52.80742,-11.06499 -98.99613,15.6128c-46.16842,26.6778 -73.27257,60.1773 -62.99939,77.9219z" fill="none" stroke="#3f51b5" stroke-width="10.5" stroke-linejoin="round"></path></g></g><path d="M126,252c-69.58788,0 -126,-56.41212 -126,-126v0c0,-69.58788 56.41212,-126 126,-126v0c69.58788,0 126,56.41212 126,126v0c0,69.58788 -56.41212,126 -126,126z" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="M126,246.96c-66.80436,0 -120.96,-54.15564 -120.96,-120.96v0c0,-66.80436 54.15564,-120.96 120.96,-120.96h0c66.80436,0 120.96,54.15564 120.96,120.96v0c0,66.80436 -54.15564,120.96 -120.96,120.96z" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path></g></svg>

';

	}
echo "
	</div>

</div>";
	
	}
}

?>
