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
	<div class="center-block-main">
		<div class="recovery">
        	<form action="scripts/signin.html" method="post">
				<legend>Востановление пароля</legend>
				<input type="text" name="login" placeholder="Введите почту" required><br/>
			</form>
		</div>		
	</div>
<footer>
	<?php require_once "footer.php" ?>
</footer>
</body>
</html>	