<?php
	$hn = 'localhost';
	$un = 'root';
	$pw = '';
	$db = 'wos';
        $upd = 'user_primary_data';
        $usd = 'user_second_data';
        $s_i = 'sign_in';
        $u_s = 'user_subjects';
        $top = 'topics';
        $teach = 'teachers';
        $sub = 'subjects';
        $stat = 'statuses';
        $lect = 'lections';

	function fix_string($conn, $str) {
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		$result = $conn->real_escape_string($str);
		return htmlentities($result);
	}
?>
