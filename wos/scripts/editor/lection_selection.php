<?php 
	function lection_page_work($topic_selection, $conn) {
		echo "<form action='editor.php' method='GET'>";
		$selected_topic_id = "";	
		$selected_topic_id = $topic_selection;
		$query = "SELECT topic_name FROM topics WHERE topic_id='$selected_topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$selected_topic_name = $result->fetch_array(MYSQLI_NUM);
		echo "<br><p>Выбрана тема '$selected_topic_name[0]'<p><br>";
			
		echo "<select name='lection_selection'>";
		$query = "SELECT * FROM lections WHERE topic_id='$selected_topic_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row_number = $result->num_rows;
		for($i = 0; $i < $row_number; ++$i) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			echo "<option value='".$row['lection_id']."'>".$row['lection_name']."</option>";
		}
		echo "</select>
			<input type='submit' name='select_lection' value='Выбрать лекцию'>
		</form>
		<form action='editor.php' method='GET'>
			<input type='submit' name='create_lection' value='Добавить новую лекцию'>
		</form>";
	}
?>
