<?php 
	function show_navigator($topic_name) {
		$data = get_db_data('tests');	
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die($conn->connect_error);	
		$conn->query("SET NAMES 'utf8'");

		$result = $conn->query("SELECT topic_id FROM topics WHERE topic_name='$topic_name'");
		$row = $result->fetch_array(MYSQLI_NUM);
		$result = $conn->query("SELECT MAX(lection_id) FROM lections WHERE topic_id='$row[0]'");
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) {
			echo "...";
		} else {
			$result = $conn->query("SELECT lection_link FROM lections WHERE lection_id='$row[0]'");
			$row = $result->fetch_array(MYSQLI_NUM);
			echo "<a href='http://localhost/wos/material/lections/$row[0]'>Предыдущая лекция</a>";
		} 
	}	
?>