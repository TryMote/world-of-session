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
    <h2>POPULAR PRODUCTS</h2>
    <div class="block4-main ">
    	<?php 
		require_once 'scripts/selector.php';
		show_topic_selector('m');	
	?>
    </div>
</section>

<div class="about center-block-main ">
	<article>
    	<h2>About</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam dictum lectus sit amet varius pulvinar. Proin vitae 
dui tincidunt nibh facilisis pellentesque.  Fusce tortor turpis, facilisis ut condimentum eu, sagittis at est.</p>

<p>Praesent egestas posuere urna a egestas. Maecenas facilisis orci vitae ante  tempor accumsan. Aenean aliquam 
justo ac sagittis vehicula. Nam mattis pretium odio sit amet vulputate.</p>

<p>Quisque non lobortis orci. Morbi augue mauris, ultrices at fermentum ac, consequat vitae magna. Pellentesque 
non cursus mi, eu cursus nunc. Nullam  et odio tristique, volutpat urna vitae, dignissim orci. Fusce eu nulla urna.
This template was created by Erik Padamans.</p>

<p>Praesent egestas posuere urna a egestas. Maecenas facilisis orci vitae ante  tempor accumsan. Aenean aliquam 
justo ac sagittis vehicula. Nam mattis pretium odio sit amet vulputate.</p>
 
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
