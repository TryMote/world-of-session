<?php

function show_topic_selector($mode) {
	require_once 'db_data.php';
	include_once 'scripts/editor/data_analizer.php';
	$conn = get_connection_object('index');
	$result = get_first_query_result($conn, "SELECT topic_id FROM topics");
	$row_number = $result->num_rows;
	if($row_number > 4 && $mode == 'm') {
		$row_number = 4;
	}
	for($i = 0; $i < $row_number; ++$i) {
		$row = get_select_array($conn, "SELECT topic_id, topic_name, topic_image FROM topics", $i, MYSQLI_NUM);
		$lection_link = get_first_select_array($conn, "SELECT lection_link FROM lections WHERE topic_id='$row[0]'", MYSQLI_NUM)[0];
		if($row[0]) {
			if(!$lection_link) {
				$lection_link = 'lections.php';
			} else {
				$lection_link = 'http://localhost/wos/material/lections/'.$lection_link;
			}
			echo "<article>
			<img src='http://localhost/wos/material/topic_img/$row[2]' width='250px' height='173px' alt=''>
			<div class='block4-main-content'>
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

function show_tests_selector($mode) {
	require_once 'db_data.php';
	$conn = get_connection_object('index');
	$result = get_first_query_result($conn, "SELECT test_id FROM tests");
	$row_number = $result->num_rows;
	if($row_number > 4 && $mode == 'm') {
		$row_number = 4;
	}
	for($i = 0; $i < $row_number; ++$i) {
		$row = get_select_array($conn, "SELECT test_id, test_link FROM tests", $i, MYSQLI_NUM);
		$topic_row = get_first_select_array($conn, "SELECT topic_name, topic_image FROM topics WHERE test_id='$row[0]'", MYSQLI_NUM);
		if($row[0]) {
			$test_link = '#';
			if(!$row[1]) {
				$test_link = 'practice.php';
			} else {
				$test_link = 'http://localhost/wos/material/tests/'.$row[1];
			}
			echo "<article>
			<img src='http://localhost/wos/material/topic_img/$topic_row[1]' width='250px' height='173px' alt=''>
			<div class='block4-main-content'>
			<h2>$topic_row[0]</h2>
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

?>
