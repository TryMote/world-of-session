
<!DOCTYPE html>
<html>
<head>
<title>Пределы</title>
<meta charset='utf8'>
<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<header>
<?php
require_once '../../menu.php' 
?>
</header>
<div class='lections_list'>
<?php include_once '../lections_list.php'; 
show_list('Пределы');
?>
</div>
<section class='block4-section center-block-main'>
<div class='test_page'>
<div class='main_headers'>
<h1>Пределы</h1>
</div>
<div class='test_content'>
<?php 
include_once 'show_test.php';
show_test('1', 'Пределы');
?>
</div>
</div>
</section>
<div class='lection_controller'>
<?php include_once 'test_navigator.php';
show_navigator('Пределы');
?>
</div>
<footer>
<?php include_once '../../footer.php' ?>
</footer>
</body>
</html>
