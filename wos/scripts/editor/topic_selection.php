<?php 
	function topic_page_work($get_subject_selection, $conn) {
		echo "<form action='editor.php' method='GET'>";
	
		$selected_subject_id = $get_subject_selection;
		$query = "SELECT subject_name FROM subjects WHERE subject_id='$selected_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$selected_subject_name = $result->fetch_array(MYSQLI_NUM);
		echo "<br><p>Выбран предмет '$selected_subject_name[0]'<p><br>";
		
		echo "<select name='topic_selection'>";
		$query = "SELECT * FROM topics WHERE subject_id='$selected_subject_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row_number = $result->num_rows;
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			echo "<option value='".$row['topic_id']."'>".$row['topic_name']."</option>";
		}
		echo "</select>
			<input type='submit' name='select_topic' value='Выбрать тему'>
		</form>
		<form action='editor.php' method='GET'>
			<input type='submit' name='create_topic' value='Добавить новую тему'>
		</form>";
	}
?>

