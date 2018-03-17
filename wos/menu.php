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
			<input type="text" name="login" placeholder="Введите логин" required><br/>
			<input type="password" name="pass" placeholder="Введите пароль" required><br/>
			<a href="register.php">Регистрация</a><br>
			<a href="forgotpass.php">Забыли пароль?</a><br>
			<input type="submit" value="Войти">
		</form>
	</div>
</div>
