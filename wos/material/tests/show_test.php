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
			$_SESSION['coins'] = $coins;
			$_SESSION['done_index'] = $done_index;
		} else {
			$health--;
			$_SESSION['health'] = $health;
		}
	}
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
		echo "<form action='$test_link' method='POST'>";
		show_result_line($conn, $row_number, $done_index, $health, $coins);
		show_store();
		show_question($conn, $test_id, $done_index);
	}
	$result->close();
	$conn->close();	
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

function show_store($test_link) {
	echo "<div class='test-store' style='float:right;margin-right:50%;'>
		<form action='$test_link' method='POST'>
			<label for='buy_health'>
			<input type='submit' name='buy_health' value='+1 жизнь'>
	</div>";
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
	echo "<input type='submit' name='next_q_$done_index' value='Удар'>
	</form>"; 

}


function show_result_line($conn, $row_number, $done_index, $health, $coins) {
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
	</div>
<br>	<div class='user-status'>
<div class='user-helth user-coins'>
$health 	
<svg xmlns='http://www.w3.org/2000/svg' x='0px' y='0px'
     width='30' height='30'
     viewBox='0 0 252 252'
     style='fill:#c0392b;'><g fill='none' fill-rule='nonzero' stroke='none' stroke-width='1' stroke-linecap='butt' stroke-linejoin='miter' stroke-miterlimit='10' stroke-dasharray='' stroke-dashoffset='0' font-family='none' font-weight='none' font-size='none' text-anchor='none' style='mix-blend-mode: normal'><path d='M0,252v-252h252v252z' fill='none'></path><g id='Ð¡Ð»Ð¾Ð¹_1'><g><g fill='#c0392b'><g><g><path d='M90.5625,59.65313c14.175,0 26.775,6.10312 35.4375,15.75c8.6625,-9.64687 21.2625,-15.75 35.4375,-15.75c26.18437,0 47.25,20.67187 47.25,46.26562c0,14.56875 -6.89062,27.36563 -17.52188,36.02812v0l-65.16562,50.4l-63.98437,-49.6125v0c-11.41875,-8.46562 -18.70312,-21.85313 -18.70312,-36.81563c0,-25.59375 21.06563,-46.26562 47.25,-46.26562z'></path></g></g></g><g fill='#454b54'><g><g><path d='M126,198.25313c-1.18125,0 -2.55937,-0.39375 -3.54375,-1.18125l-64.18125,-49.6125c-0.19688,-0.19688 -0.39375,-0.39375 -0.59063,-0.39375c-12.79687,-10.04063 -20.27813,-25.00313 -20.27813,-41.14688c0,-28.74375 23.82188,-52.17187 53.15625,-52.17187c13.19063,0 25.79063,4.725 35.4375,13.3875c9.64688,-8.46563 22.24687,-13.3875 35.4375,-13.3875c29.33438,0 53.15625,23.42813 53.15625,52.17188c0,15.75 -7.0875,30.31875 -19.29375,40.35938c-0.19688,0.19688 -0.19688,0.19688 -0.39375,0.39375l-65.3625,50.59688c-0.98437,0.59062 -2.3625,0.98438 -3.54375,0.98438zM66.15,138.6l59.85,46.4625l61.425,-47.64375l0.19687,-0.19687c9.64688,-7.67813 15.15938,-19.09688 15.15938,-31.30313c0,-22.24688 -18.50625,-40.35937 -41.34375,-40.35937c-11.8125,0 -23.23125,5.11875 -31.10625,13.78125c-2.16563,2.55938 -6.49688,2.55938 -8.85937,0c-7.875,-8.85937 -19.09688,-13.78125 -31.10625,-13.78125c-22.8375,0 -41.34375,18.1125 -41.34375,40.35938c0,12.6 5.90625,24.4125 16.34063,32.09063c0.39375,0.19688 0.59062,0.39375 0.7875,0.59063z'></path></g></g></g></g><g fill='#ffffff'><path d='M56.89687,111.825c-3.34687,0 -5.90625,-2.55937 -5.90625,-5.90625c0,-21.45937 17.71875,-38.78437 39.57188,-38.78437c3.34688,0 5.90625,2.55937 5.90625,5.90625c0,3.34687 -2.55937,5.90625 -5.90625,5.90625c-15.35625,0 -27.75938,12.00938 -27.75938,26.97187c0,3.34688 -2.55937,5.90625 -5.90625,5.90625z'></path></g></g></g></svg>
$coins 
".'
<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
     width="30" height="30"
     viewBox="0 0 48 48"
     style="enable-background:new 0 0 48 48;;fill:#c0392b;"><g>	<path style="fill:#FFAB00;" d="M44,24c0,11.044-8.956,20-20,20S4,35.044,4,24S12.956,4,24,4S44,12.956,44,24z"></path>	<path style="fill:#FFCC80;" d="M24,8C15.15,8,8,15.15,8,24c0,8.85,7.15,16,16,16s16-7.15,16-16C40,15.15,32.85,8,24,8z"></path>	<g>		<path style="fill:#FFAB00;" d="M24,12c0.295,0,0.583,0.023,0.872,0.044L25.077,10h-2.369l0.206,2.055			C23.272,12.023,23.633,12,24,12z"></path>		<path style="fill:#FFAB00;" d="M17.446,13.95l-1.092-1.688c-0.646,0.431-1.292,0.969-1.831,1.4l1.387,1.486			C16.39,14.71,16.9,14.306,17.446,13.95z"></path>		<path style="fill:#FFAB00;" d="M21.065,12.375l-0.512-1.944c-0.754,0.215-1.508,0.431-2.154,0.754l0.806,1.814			C19.801,12.74,20.424,12.536,21.065,12.375z"></path>		<path style="fill:#FFAB00;" d="M14.548,16.615l-1.641-1.231c-0.431,0.646-0.862,1.292-1.292,1.831l1.85,1.028			C13.78,17.668,14.147,17.128,14.548,16.615z"></path>		<path style="fill:#FFAB00;" d="M32.145,15.198l1.332-1.537c-0.538-0.538-1.185-0.969-1.831-1.4l-1.092,1.688			C31.121,14.32,31.65,14.74,32.145,15.198z"></path>		<path style="fill:#FFAB00;" d="M12.665,20.081l-1.912-0.604c-0.215,0.754-0.431,1.508-0.538,2.262l1.959,0.294			C12.285,21.361,12.449,20.709,12.665,20.081z"></path>		<path style="fill:#FFAB00;" d="M34.555,18.279l1.721-0.956c-0.323-0.754-0.754-1.4-1.292-1.831l-1.52,1.14			C33.869,17.151,34.24,17.697,34.555,18.279z"></path>		<path style="fill:#FFAB00;" d="M35.81,21.927l1.975-0.296c-0.108-0.754-0.323-1.508-0.646-2.369l-1.854,0.683			C35.514,20.583,35.691,21.244,35.81,21.927z"></path>		<path style="fill:#FFAB00;" d="M28.794,12.999l0.806-1.814c-0.646-0.323-1.4-0.538-2.154-0.754l-0.512,1.944			C27.576,12.536,28.199,12.74,28.794,12.999z"></path>		<path style="fill:#FFAB00;" d="M32.09,32.852l1.387,1.486c0.538-0.538,1.077-1.185,1.508-1.723l-1.572-1.179			C33.011,31.945,32.567,32.415,32.09,32.852z"></path>		<path style="fill:#FFAB00;" d="M34.556,29.721l1.721,0.956c0.431-0.646,0.754-1.4,0.862-2.262l-1.78-0.562			C35.14,28.501,34.877,29.128,34.556,29.721z"></path>		<path style="fill:#FFAB00;" d="M28.794,35.001l0.806,1.814c0.754-0.323,1.4-0.646,2.046-1.077l-1.092-1.688			C29.997,34.414,29.41,34.733,28.794,35.001z"></path>		<path style="fill:#FFAB00;" d="M36,24c0,0.668-0.068,1.319-0.173,1.957l1.957,0.412C37.892,25.615,38,24.862,38,24H36z"></path>		<path style="fill:#FFAB00;" d="M24.98,35.951L25.185,38c0.754-0.108,1.508-0.215,2.262-0.431l-0.512-1.944			C26.301,35.784,25.649,35.897,24.98,35.951z"></path>		<path style="fill:#FFAB00;" d="M17.446,34.05l-1.092,1.688c0.646,0.431,1.292,0.754,2.046,1.077l0.806-1.814			C18.59,34.733,18.003,34.414,17.446,34.05z"></path>		<path style="fill:#FFAB00;" d="M12.633,27.831l-1.879,0.692c0.323,0.754,0.646,1.508,0.969,2.154l1.7-1			C13.108,29.09,12.848,28.471,12.633,27.831z"></path>		<path style="fill:#FFAB00;" d="M14.548,31.385l-1.641,1.231c0.538,0.646,1.077,1.185,1.615,1.723l1.332-1.537			C15.383,32.365,14.945,31.893,14.548,31.385z"></path>		<path style="fill:#FFAB00;" d="M21.092,35.632l-0.538,2.045c0.754,0.108,1.508,0.215,2.262,0.323l0.215-2.046			c-0.007-0.001-0.015-0.002-0.022-0.004C22.352,35.897,21.714,35.786,21.092,35.632z"></path>		<path style="fill:#FFAB00;" d="M12,24h-2c0,0.754,0.108,1.615,0.215,2.261l1.959-0.294C12.069,25.326,12,24.672,12,24z"></path>	</g>	<g id="surface1_95_">		<polygon style="fill:#FFAB00;" points="26,17 26,31 23,31 23,20.71 20,21.66 20,19.16 25.72,17 		"></polygon>	</g></g></svg>
'."

	</div>

</div>";
	
	}
}

?>
