<?php

function show_topic_selector($conn, $mode) {
	include_once 'scripts/editor/data_analizer.php';
	$result = get_first_query_result($conn, "SELECT subject_id, subject_name FROM subjects");
	$row_number = $result->num_rows;
	if($row_number > 4 && $mode == 'm') {
		$row_number = 4;
	}
	for($i = 0; $i < $row_number; ++$i) {
		$result->data_seek($i);
		$subject = $result->fetch_array(MYSQLI_NUM);
		if($mode != 'm') {
			echo "<p class='subject_name'>$subject[1]</p>";
		}
		$result_topic = get_first_query_result($conn, "SELECT topic_id, topic_name, topic_image FROM topics WHERE subject_id='$subject[0]'", MYSQLI_NUM);
		$row_number_topic = $result_topic->num_rows;
		if($row_number > 4 && $mode == 'm') {
			$row_number_topic = 4;
		}
		for($j = 0; $j < $row_number_topic; ++$j) {
			$result_topic->data_seek($j);
			$row = $result_topic->fetch_array(MYSQLI_NUM);
			$lection_link = get_first_select_array($conn, "SELECT lection_link FROM lections WHERE topic_id='$row[0]'", MYSQLI_NUM)[0];
			if($row[0]) {
				if(!$lection_link) {
					$lection_link = 'lections.php';
				} else {
					$lection_link = 'http://localhost/wos/material/lections/'.$lection_link;
				}
				echo "<article>
				<div class='block4-main-content'>
				<img src='http://localhost/wos/material/topic_img/$row[2]' width='250px' height='173px' alt=''>
				<h2>$row[1]</h2>
				<h4></h4>
				<p></p>
				<p class='price-basket '>
				<span class='basket-btn'><a href='$lection_link'><img src='assets/img/ico-plus.jpg' alt=''></a></span>
				</p>
				</div>
				</article>";
			}
		}
	}	
}

function show_tests_selector($conn, $mode) {
	$result = get_first_query_result($conn, "SELECT subject_id, subject_name FROM subjects");
	$row_number = $result->num_rows;
	if($row_number > 4 && $mode == 'm') {
		$row_number = 4;
	}
	for($i = 0; $i < $row_number; ++$i) {
		$result->data_seek($i);
		$subject = $result->fetch_array(MYSQLI_NUM);
		if($mode != 'm') {
			echo "<p class='subject_name'>$subject[1]</p>";
		}
		$result_topic = get_first_query_result($conn, "SELECT topic_id, topic_name, topic_image FROM topics WHERE subject_id='$subject[0]'", MYSQLI_NUM);
		$row_number_topic = $result_topic->num_rows;
		if($row_number > 4 && $mode == 'm') {
			$row_number_topic = 4;
		}
		for($j = 0; $j < $row_number_topic; ++$j) {
			$result_topic->data_seek($j);
			$row = $result_topic->fetch_array(MYSQLI_NUM);
			$test_link = get_first_select_array($conn, "SELECT test_link FROM tests WHERE topic_id='$row[0]'", MYSQLI_NUM)[0];
			if($row[0]) {
				if(!$test_link) {
					$test_link = 'practice.php';
				} else {
					$test_link = 'http://localhost/wos/material/tests/'.$test_link;
				}
				echo "<article>
				<div class='block4-main-content'>
				<img src='http://localhost/wos/material/topic_img/$row[2]' width='250px' height='173px' alt=''>
				<h2>$row[1]</h2>
				<h4></h4>
				<p></p>
				<p class='price-basket '>
				<span class='basket-btn'><a href='$test_link'><img src='assets/img/ico-plus.jpg' alt=''></a></span>
				</p>
				</div>
				</article>";
			}
		}
	}	


}

?>
