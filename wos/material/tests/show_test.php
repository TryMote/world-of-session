<?php

function show_test($test_id, $topic_name) {
	if(!isset($_SESSION['test_id'])) {
		require_once 'test_session.php';
		start_test($test_id);
	}
	$conn = get_connection_object();
	$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	
	$done_index = $_SESSION['done_index'];
	$health = $_SESSION['health'];
	$coins = $_SESSION['coins'];
	$question_id = get_select_array($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'", $done_index, MYSQLI_NUM)[0];
	if(isset($_POST['next_q_'.$done_index])) {
		if(check_answer($conn, $question_id)) {
			$done_index++;
			$coins++;
			show_boss($conn, $test_id, $topic_name, 'topic_fail');
		} else {
			$health--;
			show_boss($conn, $test_id, $topic_name, 'topic_attack');
		}
	} else {
		show_boss($conn, $test_id, $topic_name, 'topic_image');
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
		if($_SESSION['in'] == 1) {
			$user_id = $_SESSION['user_id'];
			$xp_plus = $health*$row_number*5;
			$health_left = get_first_select_array($conn, "SELECT MAX(health_left) FROM users_results WHERE test_id='$test_id' AND user_id='$user_id'", MYSQLI_NUM)[0];
			if($health_left) {
				if($health_left < $health) {
					get_first_query_result($conn, "UPDATE users_results SET health_left=$health WHERE test_id='$test_id' AND user_id='$user_id'");
				} else {
					$xp_plus = 0;
				}
			} else {
				$result = $conn->prepare("INSERT INTO users_results(user_id, health_left, test_id) VALUES(?,?,?)");
				$result->bind_param('iii', $user_id, $health, $test_id);
				$result->execute();
			}
			get_first_query_result($conn, "UPDATE user_second_data SET user_xp=user_xp+$xp_plus WHERE user_id='$user_id'");
		}
		show_congrates($test_link);
	} elseif($health <= 0) {
		require_once 'test_session.php';
		stop_test();
		die("END"); 
	} else {
		show_store($test_link);
		show_result($conn, $row_number, $done_index, $health, $coins);
		echo "<form action='$test_link' method='POST'>";
		show_question($conn, $test_id, $done_index);
	}
	$result->close();
	$conn->close();	
}

function show_boss($conn, $test_id, $boss_name, $mode) {
	$DEFAULT_PHRASE = array("скучает, ожидая твой ход!", "зачем-то ожидает твой ход...", "готовиться к атаке!", "уже на готове! Твой ход!", "атакует задачей!");
	$ATTACK_PHRASE = array("знатно наподдал тебе этим вопросом!");
	$FAIL_PHRASE = array("переживает твое точное попадание!");
	$rand_d_phrase = rand(0, count($DEFAULT_PHRASE)-1);
	$rand_a_phrase = rand(0, count($ATTACK_PHRASE)-1);
	$rand_f_phrase = rand(0, count($FAIL_PHRASE)-1);
	$boss_image = get_first_select_array($conn, "SELECT $mode FROM topics WHERE test_id='$test_id'", MYSQLI_NUM)[0];
	if($boss_image == 'default') {
		switch($mode) {
			case 'topic_image':
				$boss_image = "<p>$boss_name ".$DEFAULT_PHRASE[$rand_d_phrase]."</p>";
				break;
			case 'topic_attack':
				$boss_image = "<p>$boss_name ".$ATTACK_PHRASE[$rand_a_phrase]."</p>";
				break;
			case 'topic_fail':
				$boss_image = "<p>$boss_name ".$FAIL_PHRASE[$rand_f_phrase]."</p>";
				break; 
		}
	} else {
		require_once '../../scripts/editor/data_analizer.php';
		$boss_image = "<img width='500px' height='300px' src='$topic_img_location$boss_image'>";
	}
	echo $boss_image;
}	

function show_store($test_link) {
	echo "<div class='test-store'>
		<form action='$test_link' method='POST'>
			<a class='buy_health'><input class='buy_health' type='submit' name='buy_health' value=''></a>
			| +1 к жизни | -2 монета			
			<br><a class='buy_pass'><input class='buy_pass' type='submit' name='buy_pass' value=''></a>
			| пропустить вопрос | -3 монеты
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
			<img src='../../material/img/$row[1]' alt='$row[1]'>
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
		<div class='health-form' style='width:$max_hp"."px; 
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
     viewBox="0 0 252 252"
     style="fill:#2A2D34;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,252v-252h252v252z" fill="none"></path><g id="Ð¡Ð»Ð¾Ð¹_1"><g><path d="M90.75938,59.25938c14.175,0 26.97187,6.10313 35.24062,15.75c8.6625,-9.64687 21.45938,-15.75 35.24063,-15.75c25.9875,0 47.44687,20.67188 47.44687,46.4625c0,14.56875 -6.89062,27.36562 -17.325,35.83125v0l-65.3625,50.59687l-64.18125,-49.6125v0c-11.41875,-8.6625 -18.50625,-21.85313 -18.50625,-36.81563c0,-25.39688 21.06563,-46.4625 47.44688,-46.4625z" fill="#ed5d64"></path><path d="M90.75938,59.25938c14.175,0 26.97187,6.10313 35.24062,15.75c8.6625,-9.64687 21.45938,-15.75 35.24063,-15.75c25.9875,0 47.44687,20.67188 47.44687,46.4625c0,14.56875 -6.89062,27.36562 -17.325,35.83125v0l-65.3625,50.59687l-64.18125,-49.6125v0c-11.41875,-8.6625 -18.50625,-21.85313 -18.50625,-36.81563c0,-25.39688 21.06563,-46.4625 47.44688,-46.4625z" fill="#e74c3c"></path><g fill="#0c0f13" opacity="0.1"><path d="M161.24063,59.25938c-4.52813,0 -8.85937,0.59062 -13.19063,1.77187c19.6875,5.5125 34.05937,23.23125 34.05937,44.49375c0,14.56875 -7.0875,20.08125 -17.325,35.83125l-38.78438,50.79375l65.3625,-50.59687c10.43437,-8.46563 17.325,-21.2625 17.325,-35.83125c0,-25.79062 -21.45938,-46.4625 -47.44687,-46.4625z"></path></g><g fill="#ffffff" opacity="0.8"><path d="M64.37813,111.62813c-3.34688,0 -5.90625,-2.55938 -5.90625,-5.90625c0,-17.52188 14.37187,-31.69688 32.2875,-31.69688c3.34687,0 5.90625,2.55937 5.90625,5.90625c0,3.34687 -2.55938,5.90625 -5.90625,5.90625c-11.22188,0 -20.475,8.85938 -20.475,19.88438c0,3.34687 -2.75625,5.90625 -5.90625,5.90625z"></path></g><g fill="#454b54"><path d="M126,198.05625c-1.18125,0 -2.55937,-0.39375 -3.54375,-1.18125l-64.18125,-49.80937c-13.19063,-9.84375 -20.86875,-25.00313 -20.86875,-41.34375c0,-28.94063 23.82188,-52.36875 53.35313,-52.36875c13.3875,0 25.9875,4.725 35.4375,13.3875c9.64688,-8.46562 22.24688,-13.3875 35.24063,-13.3875c29.33438,0 53.35312,23.42813 53.35312,52.36875c0,15.55312 -7.0875,30.31875 -19.49062,40.35938l-65.75625,50.79375c-0.98437,0.7875 -2.3625,1.18125 -3.54375,1.18125zM90.75938,65.16563c-22.8375,0 -41.54063,18.30937 -41.54063,40.55625c0,12.6 5.90625,24.4125 16.14375,31.89375l60.6375,47.05312l61.81875,-47.84062c9.45,-7.67813 14.9625,-19.09688 14.9625,-31.10625c0,-22.24688 -18.70312,-40.55625 -41.54062,-40.55625c-11.8125,0 -23.03438,5.11875 -30.90938,13.78125c-1.18125,1.18125 -2.75625,1.96875 -4.52813,1.96875c-1.77187,0 -3.34687,-0.7875 -4.33125,-1.96875c-7.48125,-8.6625 -18.70312,-13.78125 -30.7125,-13.78125z"></path></g></g></g></g></svg>
';
	}
echo "</div>
	<div class='user-coins'>";
	for($i = 0; $i < $coins; ++$i) {
		echo '
<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
     width="50" height="50"
     viewBox="0 0 252 252"
     style="fill:#2A2D34;"><g transform="translate(5.292,5.292) scale(0.958,0.958)"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="none" stroke-linecap="butt" stroke-linejoin="none" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g stroke="#2a2d34" stroke-width="11" stroke-linejoin="round"><g id="surface1 1"><path d="M208.6875,122.0625c0,47.83447 -38.79053,86.625 -86.625,86.625c-47.83447,0 -86.625,-38.79053 -86.625,-86.625c0,-47.83447 38.79053,-86.625 86.625,-86.625c47.83447,0 86.625,38.79053 86.625,86.625z" fill="#e8d24b"></path><path d="M205.88819,50.04932c5.55249,5.82934 10.45898,12.24316 14.61181,19.13379v25.31689l-134.30566,134.30566c-13.42749,-5.19873 -25.7168,-12.98145 -36.14502,-22.91748z" fill="#ffefca"></path><path d="M32.36133,184.20117l140.88867,-140.88867h23.625l-154.50073,154.50073c-3.67603,-4.27588 -7.02905,-8.82861 -10.01294,-13.61206z" fill="#ffefca"></path><path d="M25.04004,156.08496l124.58496,-124.58496l11.8125,7.875l-130.76806,130.76807c-2.19946,-4.55273 -4.07593,-9.2439 -5.6294,-14.05811z" fill="#ffefca"></path><path d="M232.3125,122.0625c0,60.89282 -49.35718,110.25 -110.25,110.25c-60.89282,0 -110.25,-49.35718 -110.25,-110.25c0,-60.89282 49.35718,-110.25 110.25,-110.25c60.89282,0 110.25,49.35718 110.25,110.25zM200.90479,157.97681c14.07348,-30.90015 8.79785,-67.16821 -13.47363,-92.77734c-0.95362,-1.09204 -1.93799,-2.1687 -2.95312,-3.22998l-2.67627,-2.63012c-28.20849,-26.87036 -70.875,-31.60767 -104.28223,-11.58179c-33.40723,20.02588 -49.3418,59.87769 -38.95972,97.40699c10.39746,37.54468 44.55835,63.52295 83.50268,63.52295c33.94556,0 64.7688,-19.82593 78.84229,-50.71069z" fill="#e4bf32"></path><path d="M72.84375,114.1875l4.87573,8.90552l8.90552,4.87573l-8.90552,4.87573l-4.87573,8.90552l-4.87573,-8.90552l-8.90552,-4.87573l8.90552,-4.87573z" fill="#fff8f8"></path><path d="M170.97363,78.75l3.33765,6.13696l6.18311,3.39917l-6.18311,3.33765l-3.33765,6.18311l-3.39917,-6.18311l-6.13696,-3.33765l6.13696,-3.39917z" fill="#fff8f8"></path><path d="M170.69678,140.56567l2.32251,4.21435l4.16821,2.29175l-4.16821,2.32251l-2.32251,4.16821l-2.29175,-4.16821l-4.21435,-2.32251l4.21435,-2.29175z" fill="#fff8f8"></path><path d="M148.30225,41.52832c-0.27686,-1.43042 0.27685,-2.8916 1.43042,-3.79907c1.13818,-0.90747 2.69165,-1.10742 4.02978,-0.52295c3.09156,1.15357 6.1062,2.46094 9.05933,3.9375c1.93799,0.98438 2.72241,3.35303 1.75342,5.3064c-0.98437,1.93799 -3.35303,2.72241 -5.3064,1.73804c-2.69165,-1.35352 -5.44483,-2.55322 -8.25952,-3.6145c-1.39966,-0.43066 -2.44556,-1.59961 -2.70703,-3.04541z" fill="#2a2d34"></path><path d="M236.25,122.0625c0,63.06152 -51.12598,114.1875 -114.1875,114.1875c-63.06152,0 -114.1875,-51.12598 -114.1875,-114.1875c0,-63.06152 51.12598,-114.1875 114.1875,-114.1875c63.06152,0 114.1875,51.12598 114.1875,114.1875zM228.375,122.0625c0,-58.70874 -47.60376,-106.3125 -106.3125,-106.3125c-58.70874,0 -106.3125,47.60376 -106.3125,106.3125c0,58.70874 47.60376,106.3125 106.3125,106.3125c58.70874,0 106.3125,-47.60376 106.3125,-106.3125z" fill="#2a2d34"></path><path d="M126,200.8125v7.875c0,2.1687 -1.7688,3.9375 -3.9375,3.9375c-2.1687,0 -3.9375,-1.7688 -3.9375,-3.9375v-7.875c0,-2.1687 1.7688,-3.9375 3.9375,-3.9375c2.1687,0 3.9375,1.7688 3.9375,3.9375z" fill="#2a2d34"></path><path d="M147.7793,204.75c0.56909,2.10718 -0.67676,4.29126 -2.78393,4.86035c-2.10718,0.58447 -4.27588,-0.66138 -4.86035,-2.76855l-2.04565,-7.59814c-0.47681,-2.07642 0.75366,-4.15283 2.81469,-4.70654c2.04566,-0.55371 4.16821,0.63061 4.78345,2.64551z" fill="#2a2d34"></path><path d="M87.47095,189.50757c0.26147,1.01514 0.12305,2.0918 -0.41528,2.99927l-3.9375,6.81372c-1.1228,1.7688 -3.46069,2.35327 -5.29102,1.29199c-1.83032,-1.06128 -2.4917,-3.36841 -1.52271,-5.22949l3.9375,-6.81372c0.52295,-0.90747 1.38428,-1.58423 2.39941,-1.86108c1.01514,-0.26147 2.0918,-0.12305 2.99927,0.3999c0.90747,0.52295 1.56885,1.39966 1.83032,2.39941z" fill="#2a2d34"></path><path d="M167.82056,195.38306c0.969,1.86108 0.30762,4.16821 -1.52271,5.22949c-1.83032,1.06128 -4.16821,0.47681 -5.29102,-1.29199l-3.9375,-6.81372c-0.98437,-1.87646 -0.30762,-4.18359 1.52271,-5.22949c1.81494,-1.06128 4.15283,-0.49219 5.29102,1.29199z" fill="#2a2d34"></path><path d="M106.00488,199.19751l-2.06103,7.59814c-0.323,1.39966 -1.38428,2.52246 -2.76855,2.90699c-1.38428,0.38452 -2.87622,-0.01538 -3.87598,-1.0459c-1.01514,-1.03052 -1.3689,-2.53784 -0.95362,-3.90674l2.04565,-7.56738c0.56909,-2.0918 2.70703,-3.33765 4.81421,-2.78393c2.0918,0.55371 3.33765,2.70703 2.79931,4.79883z" fill="#2a2d34"></path><path d="M63.58447,180.54053c1.33813,1.55347 1.26123,3.89136 -0.19995,5.33716c-1.46118,1.46118 -3.78369,1.55347 -5.35254,0.21533c-27.87012,-27.76245 -34.6377,-70.33667 -16.74975,-105.37427c17.90332,-35.0376 56.37085,-54.50977 95.19214,-48.18823c2.1687,0.33838 3.66065,2.38403 3.30689,4.56811c-0.35376,2.1687 -2.39941,3.64526 -4.56811,3.30689c-26.20898,-4.18359 -52.83325,4.44507 -71.62866,23.17896c-32.28442,32.2998 -32.28442,84.65625 0,116.95605z" fill="#2a2d34"></path><path d="M186.09302,186.09302c-0.969,1.1228 -2.47632,1.59961 -3.92212,1.26123c-1.43042,-0.35376 -2.55322,-1.47656 -2.90698,-2.90698c-0.33838,-1.4458 0.15381,-2.95312 1.27661,-3.90674c32.28442,-32.2998 32.28442,-84.65625 0,-116.95605c-2.38403,-2.36865 -4.89112,-4.61426 -7.52124,-6.69067c-1.23047,-0.81518 -1.89184,-2.26099 -1.72266,-3.72217c0.16919,-1.47656 1.13819,-2.72241 2.52246,-3.22998c1.38428,-0.52295 2.95313,-0.21533 4.04517,0.76904c2.87622,2.27637 5.6294,4.72192 8.22876,7.32129c35.34521,35.37598 35.34521,92.68506 0,128.06104z" fill="#2a2d34"></path><path d="M149.625,86.625h-19.99512c4.27588,4.24512 7.02905,9.78223 7.875,15.75h12.12012c2.1687,0 3.9375,1.7688 3.9375,3.9375c0,2.1687 -1.7688,3.9375 -3.9375,3.9375h-12.12012c-1.93799,13.42749 -13.36597,23.45581 -26.94727,23.625l26.39355,32.96118c1.27661,1.69189 0.969,4.10669 -0.69214,5.42944c-1.66113,1.33814 -4.07593,1.10742 -5.46021,-0.50757l-31.5,-39.375c-0.92285,-1.18433 -1.10742,-2.79932 -0.44604,-4.15283c0.64599,-1.35352 2.01489,-2.23022 3.52222,-2.23022h7.875c10.87427,0 19.6875,-8.81323 19.6875,-19.6875c0,-10.87427 -8.81323,-19.6875 -19.6875,-19.6875h-15.75c-2.1687,0 -3.9375,-1.7688 -3.9375,-3.9375c0,-2.1687 1.7688,-3.9375 3.9375,-3.9375h55.125c2.1687,0 3.9375,1.7688 3.9375,3.9375c0,2.1687 -1.7688,3.9375 -3.9375,3.9375z" fill="#2a2d34"></path><path d="M118.125,110.25h-23.625c-2.1687,0 -3.9375,-1.7688 -3.9375,-3.9375c0,-2.1687 1.7688,-3.9375 3.9375,-3.9375h23.625c2.1687,0 3.9375,1.7688 3.9375,3.9375c0,2.1687 -1.7688,3.9375 -3.9375,3.9375z" fill="#2a2d34"></path></g></g><path d="M0,252v-252h252v252z" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><g stroke="none" stroke-width="1" stroke-linejoin="miter"><g id="surface1"><path d="M208.6875,122.0625c0,47.83447 -38.79053,86.625 -86.625,86.625c-47.83447,0 -86.625,-38.79053 -86.625,-86.625c0,-47.83447 38.79053,-86.625 86.625,-86.625c47.83447,0 86.625,38.79053 86.625,86.625z" fill="#e8d24b"></path><path d="M50.04932,205.88819c10.42822,9.93603 22.71753,17.71875 36.14502,22.91748l134.30566,-134.30566v-25.31689c-4.15283,-6.89062 -9.05933,-13.30445 -14.61181,-19.13379z" fill="#ffefca"></path><path d="M42.37427,197.81323l154.50073,-154.50073h-23.625l-140.88867,140.88867c2.98389,4.78345 6.33691,9.33618 10.01294,13.61206z" fill="#ffefca"></path><path d="M30.66944,170.14307l130.76806,-130.76807l-11.8125,-7.875l-124.58496,124.58496c1.55347,4.81421 3.42993,9.50537 5.6294,14.05811z" fill="#ffefca"></path><path d="M122.0625,11.8125c-60.89282,0 -110.25,49.35718 -110.25,110.25c0,60.89282 49.35718,110.25 110.25,110.25c60.89282,0 110.25,-49.35718 110.25,-110.25c0,-60.89282 -49.35718,-110.25 -110.25,-110.25zM122.0625,208.6875c-38.94434,0 -73.10522,-25.97827 -83.50268,-63.52295c-10.38208,-37.5293 5.55249,-77.3811 38.95972,-97.40699c33.40723,-20.02588 76.07373,-15.28857 104.28223,11.58179l2.67627,2.63012c1.01513,1.06128 1.99951,2.13794 2.95313,3.22998c22.27148,25.60913 27.54712,61.8772 13.47363,92.77734c-14.07349,30.88477 -44.89673,50.71069 -78.84229,50.71069z" fill="#e4bf32"></path><path d="M72.84375,114.1875l4.87573,8.90552l8.90552,4.87573l-8.90552,4.87573l-4.87573,8.90552l-4.87573,-8.90552l-8.90552,-4.87573l8.90552,-4.87573z" fill="#fff8f8"></path><path d="M170.97363,78.75l3.33765,6.13696l6.18311,3.39917l-6.18311,3.33765l-3.33765,6.18311l-3.39917,-6.18311l-6.13696,-3.33765l6.13696,-3.39917z" fill="#fff8f8"></path><path d="M170.69678,140.56567l2.32251,4.21435l4.16821,2.29175l-4.16821,2.32251l-2.32251,4.16821l-2.29175,-4.16821l-4.21435,-2.32251l4.21435,-2.29175z" fill="#fff8f8"></path><path d="M151.00928,44.57373c2.81469,1.06128 5.56787,2.26098 8.25952,3.6145c1.95337,0.98438 4.32202,0.19995 5.3064,-1.73804c0.969,-1.95337 0.18457,-4.32202 -1.75342,-5.3064c-2.95312,-1.47656 -5.96777,-2.78393 -9.05933,-3.9375c-1.33813,-0.58447 -2.8916,-0.38452 -4.02978,0.52295c-1.15357,0.90747 -1.70728,2.36865 -1.43042,3.79907c0.26147,1.4458 1.30737,2.61475 2.70703,3.04541z" fill="#2a2d34"></path><path d="M122.0625,7.875c-63.06152,0 -114.1875,51.12598 -114.1875,114.1875c0,63.06152 51.12598,114.1875 114.1875,114.1875c63.06152,0 114.1875,-51.12598 114.1875,-114.1875c0,-63.06152 -51.12598,-114.1875 -114.1875,-114.1875zM122.0625,228.375c-58.70874,0 -106.3125,-47.60376 -106.3125,-106.3125c0,-58.70874 47.60376,-106.3125 106.3125,-106.3125c58.70874,0 106.3125,47.60376 106.3125,106.3125c0,58.70874 -47.60376,106.3125 -106.3125,106.3125z" fill="#2a2d34"></path><path d="M122.0625,196.875c-2.1687,0 -3.9375,1.7688 -3.9375,3.9375v7.875c0,2.1687 1.7688,3.9375 3.9375,3.9375c2.1687,0 3.9375,-1.7688 3.9375,-3.9375v-7.875c0,-2.1687 -1.7688,-3.9375 -3.9375,-3.9375z" fill="#2a2d34"></path><path d="M145.6875,197.18262c-0.61523,-2.01489 -2.73779,-3.19922 -4.78345,-2.64551c-2.06103,0.55371 -3.2915,2.63013 -2.81469,4.70654l2.04565,7.59814c0.58447,2.10718 2.75317,3.35303 4.86035,2.76855c2.10718,-0.56909 3.35303,-2.75317 2.78393,-4.86035z" fill="#2a2d34"></path><path d="M85.64063,187.10815c-0.90747,-0.52295 -1.98413,-0.66137 -2.99927,-0.3999c-1.01513,0.27685 -1.87646,0.95361 -2.39941,1.86108l-3.9375,6.81372c-0.96899,1.86108 -0.30762,4.16821 1.52271,5.22949c1.83032,1.06128 4.16821,0.47681 5.29102,-1.29199l3.9375,-6.81372c0.53833,-0.90747 0.67676,-1.98413 0.41528,-2.99927c-0.26147,-0.99975 -0.92285,-1.87646 -1.83032,-2.39941z" fill="#2a2d34"></path><path d="M163.88306,188.56934c-1.13819,-1.78418 -3.47608,-2.35327 -5.29102,-1.29199c-1.83032,1.0459 -2.50708,3.35303 -1.52271,5.22949l3.9375,6.81372c1.1228,1.7688 3.46069,2.35327 5.29102,1.29199c1.83032,-1.06128 2.4917,-3.36841 1.52271,-5.22949z" fill="#2a2d34"></path><path d="M103.20557,194.39868c-2.10718,-0.55371 -4.24512,0.69214 -4.81421,2.78393l-2.04565,7.56738c-0.41528,1.3689 -0.06152,2.87622 0.95362,3.90674c0.99975,1.03051 2.4917,1.43042 3.87598,1.0459c1.38428,-0.38452 2.44555,-1.50733 2.76855,-2.90699l2.06103,-7.59814c0.53833,-2.0918 -0.70752,-4.24512 -2.79931,-4.79883z" fill="#2a2d34"></path><path d="M63.58447,63.58447c18.79541,-18.73389 45.41968,-27.36255 71.62866,-23.17896c2.1687,0.33838 4.21435,-1.13819 4.56811,-3.30689c0.35376,-2.18408 -1.13818,-4.22974 -3.30689,-4.56811c-38.82129,-6.32153 -77.28882,13.15064 -95.19214,48.18823c-17.88794,35.0376 -11.12036,77.61182 16.74975,105.37427c1.56885,1.33814 3.89136,1.24585 5.35254,-0.21533c1.46118,-1.4458 1.53809,-3.78369 0.19995,-5.33716c-32.28442,-32.2998 -32.28442,-84.65625 0,-116.95605z" fill="#2a2d34"></path><path d="M186.09302,58.03198c-2.59936,-2.59936 -5.35254,-5.04492 -8.22876,-7.32129c-1.09204,-0.98437 -2.66089,-1.29199 -4.04517,-0.76904c-1.38428,0.50757 -2.35327,1.75342 -2.52246,3.22998c-0.16919,1.46118 0.49219,2.90699 1.72266,3.72217c2.63013,2.07642 5.13721,4.32202 7.52124,6.69067c32.28442,32.2998 32.28442,84.65625 0,116.95605c-1.12281,0.95361 -1.61499,2.46094 -1.27661,3.90674c0.35376,1.43042 1.47656,2.55322 2.90698,2.90698c1.4458,0.33838 2.95313,-0.13843 3.92212,-1.26123c35.34521,-35.37598 35.34521,-92.68506 0,-128.06104z" fill="#2a2d34"></path><path d="M153.5625,82.6875c0,-2.1687 -1.7688,-3.9375 -3.9375,-3.9375h-55.125c-2.1687,0 -3.9375,1.7688 -3.9375,3.9375c0,2.1687 1.7688,3.9375 3.9375,3.9375h15.75c10.87427,0 19.6875,8.81323 19.6875,19.6875c0,10.87427 -8.81323,19.6875 -19.6875,19.6875h-7.875c-1.50732,0 -2.87622,0.87671 -3.52222,2.23022c-0.66138,1.35352 -0.47681,2.96851 0.44604,4.15283l31.5,39.375c1.38428,1.61499 3.79907,1.8457 5.46021,0.50757c1.66113,-1.32275 1.96875,-3.73755 0.69214,-5.42944l-26.39355,-32.96118c13.5813,-0.16919 25.00928,-10.19751 26.94727,-23.625h12.12012c2.1687,0 3.9375,-1.7688 3.9375,-3.9375c0,-2.1687 -1.7688,-3.9375 -3.9375,-3.9375h-12.12012c-0.84595,-5.96777 -3.59912,-11.50488 -7.875,-15.75h19.99512c2.1687,0 3.9375,-1.7688 3.9375,-3.9375z" fill="#2a2d34"></path><path d="M122.0625,106.3125c0,-2.1687 -1.7688,-3.9375 -3.9375,-3.9375h-23.625c-2.1687,0 -3.9375,1.7688 -3.9375,3.9375c0,2.1687 1.7688,3.9375 3.9375,3.9375h23.625c2.1687,0 3.9375,-1.7688 3.9375,-3.9375z" fill="#2a2d34"></path></g></g><path d="" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path><path d="" fill="none" stroke="none" stroke-width="1" stroke-linejoin="miter"></path></g></g></svg>
';

	}
echo "
	</div>

</div>";
	
	}
}

?>
