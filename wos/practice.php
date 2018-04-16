<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="assets/css/styles.css">
	<title>World of session</title>
</head>
<body>
	<header>
		<?php require_once "menu.php" ?>	
	</header>
	<div class="practic center-block-main ">    
	<article>
    	<a href="/"><img src="assets/img/practic.png" alt="" class="practic"></a>
    	<h1>Практикуйся</h1>
        <h5>Тренируйся перед экзаменами</h5>
        <h5>Закрипляй свои знания</h5>	
        <h5>Получай бонусы</h5>
    </article>
  </div>
</div>
<section class="block4-section center-block-main">
    <h2>ВЫБЕРИ ТЕСТ ПО НУЖНОЙ ТЕМЕ</h2>
    <div class="block4-main ">
    	<?php 
		require_once 'scripts/selector.php';
		show_tests_selector('p');
	?> 
    </div>
</section>

	<?php include_once "footer.php" ?>
</body>
</html>	
