<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/unslider.css">
    <link rel="stylesheet" href="assets/css/unslider-dots.css">
	<title>World of session</title>
</head>
<body>
	<header>
	<?php require_once "menu.php" ?>
	</header>
	<div class="middle">
<div class="my-slider center-block-main">
	<nav>
	<ul>
		<li><h2> <br></h2>
<img src="assets/img/math.png">

		</li>
		<li><h2> <br> </h2>
<img src="assets/img/math.png">

		</li>
		<li><h2><br> </h2>
<img src="assets/img/math.png">

		</li>
	</ul>
	</nav>
</div>
</div>
<section class="block4-section center-block-main">
    <h2>Новинки</h2>
    <div class="block4-main ">
    	<?php 
		require_once 'scripts/selector.php';
		show_topic_selector($conn, 'm');	
	?>
    </div>
</section>

<div class="about center-block-main ">
	<article>
    	<h2>О том, что здесь происходит...</h2>
	<p>Подготовиться к сессии, повторить изученный материал, быть на готове к любому вопросу на экзамене. 
	Для этого тебе не потребуется терять много драгоценного времени. Необходимый багаж знаний в краткой и понятной форме ждет тебя тут, в мире новых открытий. 
	<br>Нет, здесь не простые лекции и тесты... Здесь все, что 
	даст тебе опору для саморазвития. Погрузись в игровой мир знаний, и уровень за уровенем с легкостью переходи все к более сложным темам.</p>
	
    </article>
  </div>
</div>
	<?php include_once "footer.php" ?>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/unslider-min.js"></script>
<script>
		jQuery(document).ready(function($) {
			$('.my-slider').unslider({
				autoplay:true,

				arrows:false
			});
		});
</script>

<script>
	function setEqualHeight(columns) { 
		var tallestcolumn = 0; 
		columns.each( function() { 
			currentHeight = $(this).height(); 
			if(currentHeight > tallestcolumn) { tallestcolumn = currentHeight; } } ); 
			columns.height(tallestcolumn); 
		} 
		
		$(document).ready(function() { setEqualHeight($(".block4-main-content")); });
</script>

</body>
</html>	
