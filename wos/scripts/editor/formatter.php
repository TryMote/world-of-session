<!DOCTYPE html>
<html>
<head>
	<title>Редактор лекций</title>
	<meta charset='utf8'>
</head>
<body>
<?php
	require_once '../db_data.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	$conn->query("SET NAMES 'utf8'");

	if(isset($_POST['lection_selection'])) {
		$lection_id = fix_string($conn, $_POST['lection_selection']);
		$query = "SELECT lection_link FROM lections WHERE lection_id='$lection_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_NUM);
		if(!$row[0]) {
			die("Ошибка, такой лекции не существует!");
		}
		$filename = $row[0];
		if(!strpos('.html', $filename)) {
			$filename .= '.html';
		}
		$filename = '../../lections/'.$filename;
		open_editor($filename);
	}
	
	function open_editor($filename) {
		if(!file_exists($filename)) {
			$file = fopen($filename, 'w');
			fclose($file);
		}
		$content = file_get_contents($filename);
		if(!$content) {
			echo "<p>В файл лекции еще ничего не записано</p><br>";
		} else {
			echo $content;
		}
	}	

?>
</body>
</html>
