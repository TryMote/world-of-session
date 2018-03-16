<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="assets/css/styles.css">
	<title>World of session</title>
</head>
<body>
	<header>
		<div class="main">
			
			<div id="logo">
				<a href="index.php">World of session</a>
			</div>

			<div id="main-menu">
				<a href="lections.php">Лекции</a>
				<a href="practice.php">Практика</a>
				<a href="contact.php">Контакты</a>
				<span>Вход</span>
			</div>

			<div id="vxod">
				<form action="scripts/signin.php" method="post">
					<input type="text" name="login" placeholder="Введите логин"><br/>
					<input type="pas" name="password" placeholder="Введите пароль"><br/>
					<a href="register.php">Регистрация</a><br>
					<a href="forgotpass.php">Забыли пароль?</a><br>
					<input type="submit" value="Войти">
				</form>
			</div>

		</div>
	</header>
	<footer>
		<div id="copy">
			Copyright 2018 &copy;
		</div>
	</footer>
</body>
</html>	
