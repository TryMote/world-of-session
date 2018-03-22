<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
</head>
<body>
	<?php 
		require_once "../db_data.php";
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		
		include_once "subject_selection.php";
		subject_page_work($conn);
		
		if(isset($_GET['select_subject'])) {
			include_once "topic_selection.php";
			topic_page_work($_GET['subject_selection'], $conn);
		}
		if(isset($_GET['select_topic']) && $_GET['topic_selection']) {
			include_once "lection_selection.php";
			lection_page_work($_GET['topic_selection'], $conn);
		}
		if(isset($_GET['delete_subject'])) {
			$result = $conn->query("SELECT subject_name FROM subjects WHERE subject_id='".$_GET['subject_selection']."'");
			if(!$result) die($conn->connect_error);
			$subject_name = $result->fetch_array(MYSQLI_NUM);
			echo "<br><form action='editor.php' method='GET'>
				<label style='color:#f00' for='force_delete_subject'>Предмет <b>'$subject_name[0]'</b> и все входящие в него лекции и темы будут безвозвратно удалены!</label><br>
				<input type='submit' name='force_delete_subject' value='Удалить'>
				<input type='submit' name='cancel' value='Отменить'>
				</form>";
		} 
	?>
</body>
</html>

