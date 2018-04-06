<?php 
	function show_list($topic_name) {
		require_once '../../scripts/db_data.php';
		$data = get_db_data('material');
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		
		$query = "SELECT topic_id FROM topics WHERE topic_name='$topic_name'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);;
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) die('Ошибка! Тема не найдена!');
		$topic_id = $row[0];
		
		$query = "SELECT lection_name, lection_link FROM lections WHERE topic_id='$topic_id'";
		$result = $conn->query($query); 	
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) {
			echo "...";
		} else {
			$row_number = $result->num_rows;
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$lection_name = $row['lection_name'];
				$lection_link = $row['lection_link'];
				echo "<a href='http://localhost/wos/material/lections/$lection_link'>$lection_name</a>";			
			}
		}
		$result = $conn->query("SELECT test_link FROM tests WHERE topic_id='$topic_id'");
		$row = $result->fetch_array(MYSQLI_NUM);
		if($row[0]) {
			echo "<a href='http://localhost/wos/material/tests/$row[0]'>Тест</a>";
		}  
		$result->close();
		$conn->close();
	}
?>
