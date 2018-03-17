<?php
	$hn = 'localhost';
	$un = 'root';
	$pw = '';
	$db = 'wos';

	function fix_string($conn, $str) {
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		return $conn->real_escape_string($str);
	}
?>
