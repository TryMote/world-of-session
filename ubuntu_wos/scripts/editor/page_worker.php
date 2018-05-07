<?php
	function create_lection_page($full_location, $topic_name, $lection_name, $content) {
		$page = "<!DOCTYPE html>
<html>
<head>
<title>$topic_name</title>
<meta charset='utf8'>
<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<header>
<?php include_once '../../menu.php' ?>
</header>
<div id='lection_page'>
<div id='lections_list'>
<?php include_once 'lections_list.php'; 
        show_list('$topic_name');
?>
</div>
<div id='main_headers'>
<h1>$topic_name</h1>
<h2>$lection_name</h2>
</div>
<div id='lection_content'>
$content
</div>
<div id='lection_test'>
</div>
<div id='lection_controller'>
<?php include_once 'lection_navigator.php';
        show_navigator('$lection_name');
?>
</div>
</div>
<footer>
<?php include_once '../../footer.php' ?>
</footer>
</body>
</html>";
		$file = fopen($full_location, 'w');
		fwrite($file, $page);
		fclose($file);
	}
?>
