<?php 
	function send_mail($to, $from, $message, $code) {
		$headers = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'X-Mailer: PHP/'.phpversion()."\r\n";
		$headers .= 'To: '.$to."\r\n";
		$headers .= 'From: '.$from."\r\n";
		if($code === 1) {
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$message = "Подтвердите свою электронную почту, перейдя по ссылке http://localhost/wos/editor/ver_email.php \n
				Eсли вы не регистрировались на сайте http:/localhost/wos/index.php, то пожалуйста не принимайте никак действий";
			$subject = "Подтверждение электронной почты";
		} elseif($code === 2) {
			$subject = 'Контакт';
		} elseif($code === 3) {
			$subject = 'Запрос на удаление';
		}
		$validation = mail($to, $subject, $message, $headers);
		if(!$validation) die("Ошибка! Сообщение не было отправлено!");
	}
?>
