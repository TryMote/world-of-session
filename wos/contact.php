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
	<div class="contact center-block-main ">
	<article>
    	<a href="/"><img src="assets/img/birds.png" alt="" class="birds"></a>
    	<h1>Свяжись с нами</h1>
        <h5>Cообщи о проблеме</h5>
        <h5>Задай свой вопрос</h5>	
        <h5>Подкинь свои идеи</h5>
    </article>
  </div>
</div>

	
  </div>
</div>
<div class="sent">
<div class="recovery center-block-main">
	<article>
    	<h2>Контакты</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam dictum lectus sit amet varius pulvinar. Proin vitae 
dui tincidunt nibh facilisis pellentesque.  Fusce tortor turpis, facilisis ut condimentum eu, sagittis at est.</p>
</article>
	        <form action="scripts/signin.html" method="post">
			<br><legend>Задайте свои вопросы</legend>
			<br><input type="text" name="user_name" placeholder="Введите свое имя" required><br/>
       			<input type="email" name="email" placeholder="Введите свое  почту" required><br/>
        		<br><textarea name="message" rows="4" cols="55" wrap="virtual"> Текст по умолчанию </textarea> <br>
			<br><label for='uploaded_file'>Выберите файл для загрузки :</label>
			<input type="file" name="uploaded_file" id="uploaded_file" ><br> 
  			<br><input type="submit" value="Отправить" name='submit'>
		</form><br>

	</div>		
    </div>

	<footer>
		<?php include_once "footer.php" ?>
	</footer>		
</body>
</html>	
