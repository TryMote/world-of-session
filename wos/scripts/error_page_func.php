<?php
	$undefined_error = "непредвиденная ошибка, попробуйте перезагрузить страницу.";
	$ex_email_error = "пользователь с такой электронной почтой уже есть."; 
	$ex_login_error = "этот логин уже занят.";
	$wr_login_error = "некорректный логин. Пропробуйте использовать только симфолы латиницы и цифры."; 
	$toshot_pass_error = "слишком короткий пароль. Минимум 6 символов.";
	$wr_email_error = "не верно введена электронная почта.";
	$wr_photo_dir_error = "формат выбранного файла не поддерживается.";
	$wr_vk_error = "не верно введена ссылка ВКонтакте.";

	function error_page($error_log) {
		echo "
<html>
<head>
	<meta charset='utf-8'>
	<title>Ошибка</title>
</head>
<body>
	<div id='error'>
		<h1>Ошибка: $error_log</h1>
	</div>
</body>
</html>";	
	}
?>
