<?php 

function show_profile($id) {
	$conn = get_connection_object();
	$xp = get_first_select_array($conn, "SELECT user_xp FROM user_second_data WHERE user_id='$id'", MYSQLI_NUM)[0];
	$result = get_first_query_result($conn, "SELECT status_name FROM statuses WHERE status_xp<='$xp'");
	$max_range = $result->num_rows;
	$status = get_select_array($conn, "SELECT status_name FROM statuses WHERE status_xp<='$xp'", $max_range-1, MYSQLI_NUM)[0];
	echo "<p class='user_status'>$status</p>
	<p class='user_xp'>($xp)</p>";
	$result = get_first_query_result($conn, "SELECT test_id FROM users_results WHERE user_id='$id'");
	$row_number = $result->num_rows;
	for($i = 0; $i < $row_number; ++$i) {
		$test_id = get_select_array($conn, "SELECT test_id FROM users_results WHERE user_id='$id'", $i, MYSQLI_NUM)[0];
		$topic = get_first_select_array($conn, "SELECT topic_name, subject_id, topic_image FROM topics WHERE test_id='$test_id'", MYSQLI_NUM);
		$subject_name = get_first_select_array($conn, "SELECT subject_name FROM subjects WHERE subject_id='$topic[1]'", MYSQLI_NUM)[0];
		$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE test_id='$test_id'", MYSQLI_NUM)[0];
		$health_left = get_first_select_array($conn, "SELECT health_left FROM users_results WHERE user_id='$id' AND test_id='$test_id'", MYSQLI_NUM)[0];
		$questions = get_first_query_result($conn, "SELECT question_id FROM questions WHERE test_id='$test_id'");
		$questions = $questions->num_rows;
		$plus_health = floor(($questions+1)/2);
		$progress = ceil((100*$health_left)/($plus_health+5));
		echo "<section class='block4-section center-block-main'>
		<div class='block4-main'><article>
				<div class='block4-main-content'>
				<img src='http://localhost/wos/material/topic_img/$topic[2]' width='250px' height='173px' alt=''>
				<h2>$topic[0]</h2>
				<h4>$subject_name</h4>
				<p>$progress %</p>
				<p class='price-basket '>
				<span class='basket-btn'><a href='http://localhost/wos/material/tests/$test_link'><img src='../assets/img/ico-plus.jpg' alt=''></a></span>
				</p>
				</div>
				</article>
				</div>
				</section>";
	}
	if(isset($_SESSION['in']) && $_SESSION['in'] == 1 && $_SESSION['user_id'] == $id) {
		echo "<a href='http://localhost/wos/scripts/editor/'>Редактор лекций</a>";
	}
}
?>