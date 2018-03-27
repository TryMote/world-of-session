<!DOCTYPE html>
<html>
<head>
	<title>Создать тест</title>
	<meta charset='utf8'>
</head>
<body>
<?php 
	require_once '../db_data.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");

	$lection_id = fix_string($conn, $_POST['lection_selection']);
	if(isset($_POST['create_test'])) {
		echo "<fieldset>";
		echo "<form action='test_creator.php' method='POST'>
		<input type='hidden' name='lection_selection' value='$lection_id'>";
 
		echo "</fieldset>";
	}

	function q_block($q_index) {
		$q_index++;
		echo "<p>Вопрос номер $q_index</p>";
		echo "<label for='q'>Содержание вопроса</label>  
		<input type='text' name='q'>";
	}
	
	$conn->close();
?>
</body>
</html>
