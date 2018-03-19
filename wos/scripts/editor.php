<?php
	function generate_select_tag($select_name, $query_result) {
		echo "<select name='$select_name'>";
		$rows = $query_result->num_rows;
		for($i = 0; $i < $rows; $i++) {
			$query_result->data_seek($i);
			$row = $query_result->fetch_array(MYSQLI_NUM);
			echo "<option value='".$row[0]."'>".$row[1]."</option>";
		}
		echo "</select>";
	}
	function generate_next_block($submit_name,$value_name, $key_name, $create_what, $name_of_what, $no_what, $conn ) {
		if(isset($_POST[$submit_name])) {
			$query = "SELECT * FROM ".$value_name."s WHERE $key_name='".$_POST[$key_name]."'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row = $result->fetch_array(MYSQLI_NUM);
			echo "<p>Если хотите создать новую $create_what, введите в поле название и нажмите 'Создать новую $create_what'</p>";
			echo "<form action='editor.php' method='POST'>";
			echo "<input type='text' name='".$value_name."_name' placeholder='Название $name_of_what'>";
			echo "<input type='submit' name='create_$value_name' value='Создать новую $create_what'></form>";	
			if(!$row[0]) {
				echo "<p>$no_what для данного предмета еще не создано</p><br>";	
			} else {
				echo "<form action='editor.php' method='POST'>";
			 	generate_select_tag('topic_id', $result);
				echo "<input type='submit' name='".$value_name."_selected' value='Выбрать $create_what'></form>";
			}
			$result->close();
		}
	}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
        <title>Lection Editor 1.0</title>
        <meta charset='utf8'>
        <link rel='stylesheets' href='../../assets/css/style.css'>
</head>
<body> 
        <form action='editor.php' method='POST'>
                <label for='sub_name'>Предмет:</label><br>
		<?php
			require 'db_data.php';
			$conn = new mysqli($hn, $un, $pw, $db); 
			$conn->query('SET NAMES "utf8"');
			if($conn->connect_error) die($conn->connect_error);

			$query = "SELECT * FROM subjects";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			generate_select_tag('subject_id', $result);	
			$result->close();
			$conn->close();
	  	 ?>
                <input type='submit' name='subject_selected' value='Выбрать предмет'>
        </form>
        <?php
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query('SET NAMES "utf8"');
		generate_next_block('subject_selected','topic','subject_id', 'тему', 'темы', 'тем', $conn);
		generate_next_block('topic_selected', 'lection', 'topic_id', 'лекцию', 'лекции', 'лекций', $conn);	
		$conn->close();
	 ?>
	
</body>
</html>

