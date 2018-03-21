<?php
	function subject_page_work($conn) {
		echo "<form action='editor.php' method='GET'>
		<label for='subject_name'>Предмет:</label><br>
		<select name='subject_selection'>";
		$query = "SELECT * FROM subjects";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row_number = $result->num_rows;
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			echo "<option value='".$row['subject_id']."'>".$row['subject_name']."</option>";
		}
		echo "</select>
		<input type='submit' name='select_subject' value='Выбрать предмет'><br>
		<input type='submit' name='create_subject' value='Добавить новый предмет'>
		</form>";
	}
?>

