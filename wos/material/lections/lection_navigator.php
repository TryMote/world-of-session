<?php

	function show_navigator($lection_name) {
		require_once '../../scripts/db_data.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
	
		$query = "SELECT lection_id, topic_id FROM lections WHERE lection_name='$lection_name'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$lection_id = $row['lection_id'];
		$topic_id = $row['topic_id'];
		$query = "SELECT lection_id FROM lections WHERE lection_id<'$lection_id' AND topic_id='$topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row_number = $result->num_rows;
		$result->data_seek($row_number - 1);
		$row = $result->fetch_array(MYSQLI_NUM);
		$query = "SELECT lection_link FROM lections WHERE lection_id='$row[0]'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if($row[0]) {
			echo "<div class='prev_lection'><a href='$row[0]'>Предыдущая</a></div>";
		}
		$query = "SELECT lection_id FROM lections WHERE lection_id>'$lection_id' AND topic_id='$topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$result->data_seek(0);
		$row = $result->fetch_array(MYSQLI_NUM);
		$query = "SELECT lection_link FROM lections WHERE lection_id='$row[0]'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if($row[0]) {
			echo "<div class='next_lection'><a href='$row[0]'>Следующая</a></div>"; 
		} else {
			$result = $conn->query("SELECT test_link FROM tests WHERE topic_id='$topic_id'");
			if($result) {
				$row = $result->fetch_array(MYSQLI_NUM);
				echo "<div class='next_lection'><a href='http://localhost/wos/material/tests/$row[0]'>Тест</a></div>";
			} 
		}
		$result->close();
		$conn->close();
	}

?>
