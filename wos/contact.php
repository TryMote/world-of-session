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
	<div class="recovery">
	    <form action="scripts/signin.html" method="post">
			<legend>Задайте свои вопросы</legend>
			<input type="text" name="тфьу" placeholder="Введите свое имя" required><br/>
        	<input type="email" name="Email" placeholder="Введите свое  почту" required><br/>
        	<textarea name="mesage" rows="4" cols="55" wrap="virtual"> Текст по умолчанию </textarea> 
		</form>
	</div>
	<footer>
		<?php include_once "footer.php" ?>
	</footer>		
</body>
</html>	