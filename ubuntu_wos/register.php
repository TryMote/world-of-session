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
		<div class="signup">
			<form action="scripts/signup.php"  method="POST" enctype="multipart/form-data">
				<legend>Создание профиля</legend><br/>
				<input type="text" name="first_name" placeholder="Имя" required><br/>
				<input type="text" name="last_name" placeholder="Фамилия" required><br/>
				<input type="text" name="login" placeholder="Логин" required><br/>
				<?php 
					echo "<input type='email' name='email' placeholder='Электронная почта' required ";
					if(isset($_POST['signup_footer'])) {
						echo "value='".$_POST['email_footer']."'";
					} 
					echo ">";
				?>
				<br><input type="password" maxlength='30' name="pass" placeholder="Пароль" required><br/>
				<input type="password" maxlength='30' name="r_pass" placeholder="Пароль еще раз" required><br/>
				<br><label for="gender">Пол:</label>
				<p>Мужчина:</p>
				<input type="radio" name="gender" value="male">
				<p>Женщина:</p>
				<input type="radio" name="gender" value="female"><br/>
				<!--    <label for="image">Выбери фото профиля:</label>
				<input type="file" name="image"><br/> -->
				<br><input type="submit" name='submit' value="Зарегистрироваться">
			</form>
		</div>
	</div>
	<footer>
		<?php include_once "footer.php" ?>
	</footer>
</body>
</html>	
