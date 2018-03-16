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
<div id="vxod">

				<form action="scripts/signup.php" method="post" enctype="multipart/form-data">
					<a href="register.php">Регистрация</a><br>
					<legend>Создания профиля</legend></form><br/>
					<input type="text" name="first_name" placeholder="Введите имя" required><br/>
					<input type="text" name="last_name" placeholder="Введите фамилию" required><br/>
					<input type="text" name="login" placeholder="Придумайте логин"><br/>
					<input type="email" name="email" placeholder="Введите свою почту"required><br/>
					<input type="password" name="pas" placeholder="Придумайте пароль" required><br/>
					<input type="submit" name='submit' value="Зарегистрироваться">
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
