<?php

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	if(strpos("@", $email)) {
		echo "Wrong email! Try again!";
		$email = "";
	}
?>
