<!DOCTYPE html>
<html>
<head>
	<title>Cake</title>
	<meta charset='utf-8'>
</head>
<body>
	<h1>Input all you need, now!</h1>
	<form action='connect.php' method='POST' enctype='myltipart/form-data'>
	<fieldset>
		<label for='first_name'>Input first name:</label><br>
		<input type='text' name='first_name'required><br>
		<label for='last_name'>Input last name:</label><br>
		<input type='text' name='last_name' required><br>
		<label for='email'>You email please:</label><br>
		<input type='email' name='email' required><br>
		<label for='gender'>What is you gender?</label><br>
		<p>Male</p>
		<input type='radio' name='gender' value='Male'>
		<p>Female</p>
		<input type='radio' name='gender' value='Female'>
	</fieldset>
	<fieldset>
		<input type='submit' name='submit' value='OK'>
	</fieldset>
	</form>
</body>
</html>
