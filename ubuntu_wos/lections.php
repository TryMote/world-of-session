<!DOCTYPE html>
<html>
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
    	<a href="/"><img width='100px' height='100px' src="assets/img/strong-girl.png" alt="" class="practic"></a>
    	<h1>Учи и изучай</h1>
	    <h5>Подойди к учебе с азартом</h5>
        <h5>Повышай уровень знаний</h5>	
        <h5>Возьми верх над ними</h5>
    </article>
  </div>
</div>
<section class="block4-section center-block-main">
    <h2>ВЫБЕРИ ЦЕЛЬ</h2>
    <div class="block4-main ">
	<?php 
		require_once 'scripts/selector.php';
		show_topic_selector($conn, 'l');
	?>    
	    </div>
</section>

	<footer>
		<?php include_once "footer.php" ?>
	</footer>
</body>
</html>
