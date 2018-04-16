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
			case 'scripts':
			$location = '';
			break;
			default:
			$location = 'scripts/';
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

	function get_connection_object($place) {
		$data = get_db_data($place);
		$conn = new mysqli($data[0], $data[1], $data[2], $data[3]);
		if($conn->connect_error) die("Произошла ошибка подключения. Попробуйте обновить страницу.");
		$conn->query("SET NAMES 'utf8'");
		return $conn;
	}


	function fix_string($conn, $str) {
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		$result = $conn->real_escape_string($str);
		$result = str_replace('<script>', '', str_replace('</script>', '', $result));
		return htmlentities($result);
	}

	function fix_content($content) {
		$content = str_replace('<script>', '', str_replace('</script>', '', $content));
		$content = str_replace("\r\n", '<br>', $content);
		return $content;
	}


	function get_clear_content($content) {
		return str_replace('<br>', "\n", $content);
	}	

	function get_query_result($conn, $query, $row_index) {
		$result = $conn->query($query);
		if(!$result) die("Произошла непредвиденная ошибка. Попробуйте перезагрузить страницу.");
		if($row_index != 0) $result->data_seek($row_index);
		return $result;
	}

	function get_first_query_result($conn, $query) {
		return get_query_result($conn, $query, 0);
	}

	function get_select_array($conn, $query, $row_index, $mode) {
		$result = get_query_result($conn, $query, $row_index);
		$row = $result->fetch_array($mode);
		$result->close();
		return $row;
	}

	function get_first_select_array($conn, $query, $mode) {
		return get_select_array($conn, $query, 0, $mode);
	}
?>
