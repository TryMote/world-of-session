<div id="signup">
	<form action="scripts/signup.php"  method="post" enctype="multipart/form-data">
		<legend>Создания профиля</legend><br/>
		<input type="text" name="first_name" placeholder="Имя" required><br/>
		<input type="text" name="last_name" placeholder="Фамилия" required><br/>
		<input type="text" name="login" placeholder="Логин" required><br/>
		<input type="email" name="email" placeholder="Электронная почта"required><br/>
		<input type="password" maxlength='30' name="pass" placeholder="Пароль" required><br/>
		<input type="password" maxlength='30' name="r_pass" placeholder="Пароль еще раз" required><br/>
		<label for="gender">Пол:</label>
		<p>Мужчина:</p>
		<input type="radio" name="gender" value="male">
		<p>Женщина:</p>
		<input type="radio" name="gender" value="female"><br/>
		<!--    <label for="image">Выбери фото профиля:</label>
		<input type="file" name="image"><br/> -->
		<input type="submit" name='submit' value="Зарегистрироваться">
	</form>
</div>

