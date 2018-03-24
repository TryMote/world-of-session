<?php
function create_lection_page() {
$page = "
<!DOCTYPE html>
<html>
<head>
	<title>$topic_name</title>
	<meta charset='utf8'>
</head>
<body>
	<header>
		<?php include_once '$menu_php' ?>
	</header>
	<div id='lection_page'>
		<div id='lections_list'>
			<?php include_once '$lection_list_php' ?>
		</div>
		<h1>$topic_name</h1>
		<div id='lection_content'>
			$content
		</div>
		<div id='lection_controller'>
			<?php include_once '$lection_controller_php' ?>
		</div>
	</div>
	<footer>
		<?php include_once '$footer_php' ?>
	</footer>
</body>
</html>";
return $page;
}
?>
