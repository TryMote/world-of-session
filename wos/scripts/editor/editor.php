<?php
	require_once '../db_data.php';
	echo <<<END
	<!DOCTYPE html>
	<html>
	<head>
		<title>Lection Editor 1.0</title>
		<meta charset='utf-8'>
		<link rel='stylesheets' href='../../assets/css/style.css'>
	</head>
	<body> 
		<form action='sql_controll.php' method='POST'>
			<label for='sub_name'>Предмет:</label><br>
			<select name='sub_name' size='3'>
END;
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->error_connect) die($conn->error_connect);
	
	$query = "SELECT * FROM subjects";
	$result = $conn->query($query);
	if(!$result) die($conn->error_connect);
	$rows = $result->num_rows;
	for($i = 0; $i < $rows; $i++) {
		$result->data_seek($i);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		echo "<option value='".$row['subject_id']."'>".$row['subject_name']."</option>";
	}
	
		echo $row['subject_name'];
	echo <<<END
		</select><br>
		<input type='submit' name='choose_subject' value='Выбрать предмет'>
		</form>
	</body>
	</html>
END;
?>
