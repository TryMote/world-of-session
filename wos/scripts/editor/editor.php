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
			echo "<form action='editor.php' method='GET'>";
		} 
		if(isset($_GET['just_delete
	?>
</body>
</html>

