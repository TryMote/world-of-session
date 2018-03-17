<?php
	include_once 'user_data.php';
/*	$undefined_error = "непредвиденная ошибка, попробуйте перезагрузить страницу.";
	$ex_email_error = "пользователь с такой электронной почтой уже есть."; 
	$ex_login_error = "этот логин уже занят.";
	$wr_login_error = "некорректный логин. Пропробуйтe использовать только буквы и цифры"; 
	$toshot_pass_error = "слишком короткий пароль. Минимум 6 символов.";
	$wr_email_error = "не верно введена электронная почта.";
	$wr_photo_dir_error = "формат выбранного файла не поддерживается.";
	$wr_vk_error = "не верно введена ссылка ВКонтакте.";
	$wr_r_pass_error = "пароли не совпадают.";	
*/
	
	function error_page($error_code) {
		echo "
<html>
<head>
	<meta charset='utf-8'>
	<title>Ошибка</title>
</head>
<body>
	<div id='error'>
		<h1>Ошибка: данные введены не верно</h1>
		<h4>Код ошибки: $error_code</h4>
	</div>
</body>
</html>";
		die();
	}
?>
