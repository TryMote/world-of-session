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
				<a href="register.php">Регистрация</a><br>
				<form action="scripts/signup.php" method="post" enctype="multipart/form-data">
					<legend>Создания профиля</legend><br/>
					<input type="text" name="first_name" placeholder="Имя" required><br/>
					<input type="text" name="last_name" placeholder="Фамилия" required><br/>
					<input type="text" name="login" placeholder="Логин" required><br/>
					<input type="email" name="email" placeholder="Электронная почта"required><br/>
					<input type="password" name="pass" placeholder="Пароль" required><br/>
					<input type="password" name="r_pass" placeholder="Пароль еще раз" required><br/>
					<label for="gender">Пол:</label>
					<p>Мужчина:</p>
					<input type="radio" name="gender" value="male">
					<p>Женщина:</p>
					<input type="radio" name="gender" value="female"><br/>
					<input type="text" name="vk" placeholder="Твоя ссылка ВКонтакте"><br/>
					<label for="photo">Выбери фото профиля:</label>
					<input type="file" name="photo"><br/>
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
