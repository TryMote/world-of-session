<!DOCTYPE html>
<html>
<head>
	<title>Создать тест</title>
	<meta charset='utf8'>
</head>
<body>
<?php 

	function open_editor($conn, $filename, $topic_name) {
		echo "<fieldset>
		<form action='test_creator.php' method='POST'>";
		echo "<input type='submit' name='add_block' value='Добавить вопрос'>
		<input type='submit' name='show_page' value='Перейти на страницу теста'>
		<input type='hidden' value='$filename' name='filename'>
		<input type='hidden' name='topic_selection' value='".$_POST['topic_selection']."'>";
		echo "</form></fieldset>";
	}

	require_once '../db_data.php';
	require_once 'data_analizer.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");

	if(isset($_POST['topic_selection'])) {
		$topic_id = fix_string($conn, trim($_POST['topic_selection']));
		$query = "SELECT test_id, subject_id, topic_name FROM topics WHERE topic_id='$topic_id'";
		$result = $conn->query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$test_id = $row['test_id'];
		$subject_id = $row['subject_id'];
		$topic_name = $row['topic_name'];
		$filename = analize_file('.php', 'test', $subject_id.$topic_id, '');
		if($test_id == 0) {
			$location = $tests_location.$filename;
			$query = "INSERT INTO tests(test_link, topic_id) VALUES(?,?)";  
			$result = $conn->prepare($query);
			$result->bind_param('si', $filename, $topic_id);
			$result->execute();
			$query = "SELECT test_id FROM tests WHERE topic_id='$topic_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row = $result->fetch_array(MYSQLI_NUM);
			$test_id = $row[0];
			$query = "UPDATE topics SET test_id='$test_id' WHERE topic_id='$topic_id'";
			$conn->query($query);	
			create_test_page($location, $topic_name, ''); 
		}
		open_editor($conn, $filename, $topic_name);
	}

	if(isset($_POST['show_page'])) {
		header("Location: ".$tests_location.fix_string($conn, $_POST['filename']));
	}	
	
	$conn->close();
?>
</body>
</html>
