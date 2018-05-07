<?php 
	if(isset($_SESSION['in']) && $_SESSION['in'] != 1) {
		echo "<div class='subscribe'>
		<div class='center-block-main'>
    		<h2><strong>Регистрируйся</strong> чтобы соревноваться с другими участниками</h2>
		'<div class='subscribe-form'>
		<form action='register.php' method='POST'>
		<input type='email' name='email_footer' class='email-inpt' placeholder=trymote@mail.ru>
		<input type='submit' name='signup_footer' class='submit-inpt' value='Начать'>
		</form>
		</div>
		</div>
		</div>";
	}
?>
<footer class="ftr">
	<div class="block4-main center-block-main clearfix">
    	<article>
        	<h2>Социальные сети</h2>
            <div class="ftr-block-content">
		<div class='social'>
            	<a href='https://t.me/KirillTry'><img width='34px' height='34px' src='http://localhost/wos/assets/img/telegramm.png'></a>
		<a href='https://vk.com/gklutej'><img width='32px' height='32px' src='http://localhost/wos/assets/img/vk.png'></a>
            	<a href='https://trymote@mail.ru'><img width='29px' height='29px' src='http://localhost/wos/assets/img/mail.png'></a>
                </div>
            </div>
        </article>
        <article>
        	<h2>FAQ</h2>    
		<div class="ftr-block-content">
		<p class='support'>
		
		</p>
            </div>
        </article>
        <article>
        	<h2>Навигация</h2>
            <div class="ftr-block-content">
            	<p class="support">
		    <a href="http://localhost/wos/">Главная</a>	
                    <a href="http://localhost/wos/lections.php">Лекции</a>
				</p>
                <p class="support">
                    <a href="http://localhost/wos/practice.php">Практика</a>
                    <a href="http://localhost/wos/contact.php">Контакты</a>
                </p>
            </div>
        </article>
        <article>
        	<h2>Связь</h2>
            <div class="ftr-block-content">
		<?php include_once 'send_message_button.php' ?>            
            </div>
        </article>
    </div>
</footer>
