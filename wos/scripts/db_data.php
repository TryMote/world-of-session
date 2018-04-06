<?php
	function get_db_data($location) {
		switch($location) {
			case 'editor':
			$location = '../';
			break;
			case 'tests':
			case 'lections':
			$location = '../../scripts/';
			case 'material':
			$location = '../../scripts/';
			break;
		}
		$data = file_get_contents($location.'.dt');
		$data = explode('|', $data);
		foreach($data as $key => $value) {
			$data[$key] = trim($value);
		}
		return $data;
	}	


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
		$result = str_replace('<script>', '', str_replace('</script>', '', $result));
		return htmlentities($result);
	}
?>
