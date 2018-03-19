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
		if(isset($_POST['subject_selected'])) {
			$query = "SELECT * FROM topics WHERE subject_id='".$_POST['subject_id']."'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$result->data_seek(0);
			$topic_row = $result->fetch_array(MYSQLI_NUM);
			if(!$topic_row[0]) {
				 echo "<p>Тем для данного предмета еще не создано</p>";
				 echo "<br/><form action='editor.php'><input type='submit' name='create_topic' value='Создать тему'>";
			} else {
				generate_select_tag('topic_id', $result);
			}
			$result->close();
			$conn->close();
		}
	 ?>
</body>
</html>

