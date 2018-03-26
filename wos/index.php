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
	<?php require_once "menu.php" ?>
	<div class="my-slider center-block-main">
		<nav>
		<ul>
			<li><h2>Если не сдашься <br> то победишь</h2>
	<img src="assets/img/math.png">

			</li>
			<li><h2>Если не сдашься <br> то победишь</h2>
	<img src="assets/img/math.png">

			</li>
			<li><h2>Если не сдашься <br> то победишь</h2>
	<img src="assets/img/math.png">

			</li>
		</ul>
		</nav>
	</div>
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
	<?php include_once "footer.php" ?>
</body>
</html>	
