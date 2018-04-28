<?php
	 
	$location = '../../material/';
	$http_location = 'http://localhost/wos/material/';
	$img_location = $location.'img/';
	$lections_location = $http_location.'lections/';
	$tests_location = $location.'tests/';
	$topic_img_location = $location.'topic_img/';

	function analize_file($file_type, $material_type, $material_index, $material_count_num) {
		$ext = analize_type($file_type);
		if(($material_type != 'lection' && $material_type != 'test') && $ext == '.php') die("Неверный тип файла"); 
		$filename = ($ext == 'default')? 'default' : strtolower($material_index).'_'.$material_type.'_'.$material_count_num.$ext;
		return $filename;
	}	

	function analize_type($file_type) {
		$ext = '';
		switch($file_type) {
			case 'image/jpeg':
			case 'image/jpg':
				$ext = '.jpg';
				break;
			case 'image/gif':
				$ext = '.gif';
				break;
			case 'image/tif':
				$ext = '.tif';
				break;
			case 'image/png':
				$ext = '.png';
				break;
			case 'image/svg':
				$ext = '.svg';
				break;
			case 'php':
			case '.php':
				$ext = '.php';
				break;
			case '':
				$ext = 'default';
				break;
			default:
				die("Неверный тип файла");
		}
		return $ext;
	}

	function get_lection_imgname($file_type, $img_filename, $lection_filename) {
		$ext = analize_type($file_type);
		$lection_filename = str_replace('.php', '', $lection_filename);
		$img_filename = substr(md5($img_filename), 0, 5);
		return $lection_filename."_".$img_filename.$ext;
	}

	function find_math($content) {
		return $content;
	}

	function create_test_page($location, $topic_name, $test_id) {
		$page = "
<!DOCTYPE html>
<html>
<head>
<title>$topic_name</title>
<meta charset='utf8'>
<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<header>
<?php
if(!session_start()) {
".'
session_start();
$_SESSION = array();
setcookie(session_name(), \'\', time() - 2592000, \'/\');
session_destroy();
session_start(); 
'."
}
include_once '../../menu.php' 
?>
</header>
<div class='lections_list'>
<?php include_once '../lections_list.php'; 
show_list('$topic_name');
?>
</div>
<section class='block4-section center-block-main'>
<div class='test_page'>
<div class='main_headers'>
<h1>$topic_name</h1>
</div>
<div class='test_content'>
<?php 
include_once 'show_test.php';
show_test('$test_id', '$topic_name');
?>
</div>
</div>
</section>
<div class='lection_controller'>
<?php include_once 'test_navigator.php';
show_navigator('$topic_name');
?>
</div>
<footer>
<?php include_once '../../footer.php' ?>
</footer>
</body>
</html>";
		file_put_contents($location, $page);
	}

        function create_lection_page($full_location, $topic_name, $lection_name, $content) {
                $content = find_math($content);
                $page = "<!DOCTYPE html>
<html>
<head>
<title>$topic_name</title>
<meta charset='utf8'>
<link rel='stylesheet' href='../../assets/css/styles.css'>
</head>
<body>
<header>
<?php include_once '../../menu.php' ?>
</header>
<div class='lections_list'>
<?php include_once '../lections_list.php'; 
show_list('$topic_name');
?>
</div>
<div class='lection_page'>
<section class='block4-section center-block-main'>
<div class='main_headers'>
<h1>$topic_name</h1>
<h1>$lection_name</h1>
</div>
<div class='lection_content'>
$content
</div>
</section>
<div class='lection_test'>
</div>
<div class='lection_controller'>
<?php include_once 'lection_navigator.php';
show_navigator('$lection_name');
?>
</div>
</div>
<footer>
<?php include_once '../../footer.php' ?>
</footer>
</body>
</html>";
                file_put_contents($full_location, $page);
        }


	function check_admin($conn, $pass) { 
		$db_pass = get_first_select_array($conn, "SELECT password FROM sign_in WHERE user_id=1", MYSQLI_NUM)[0];
		if(!hash_equals($db_pass, crypt($pass, $db_pass))) { 
			die("<br><p>Неверный пароль</p>"); 
		} else { 
			return true; 
		} 
	} 	

       function delete_material($conn, $item) {
		if($item != 'test' && $item != 'question') {
			$del_item_id = fix_string($conn, $_SESSION[$item.'_id']);
                	$item_name = get_first_select_array($conn, "SELECT $item"."_name FROM $item"."s WHERE $item"."_id='$del_item_id'", MYSQLI_NUM)[0];
		} else {
			$item_name = '';
		} 
		if($item == 'test' || $item == 'question') {
                	echo "<br><form action='test_creator.php' method='POST'>";
		}
                echo "<br><p style='color:red'><b>'$item_name'</b> и все входящие материалы, будут безвозвратное удалены!</p> 
			<br><input type='password' name='pass' placeholder='Ключ'>
                        <input type='submit' name='force_delete_$item' value='Удалить'>";
	//	if($item == 'question') {
          //       	echo "<input type='hidden' name='topic_selection' value='".fix_string($conn, $_POST['topic_selection'])."'>";       
	//	}
		echo "<br><p>Если не знаете ключa, вы можете отправить запрос на удаление</p><br>
                                <label for='email'> Ваша электронная почта</label><br>
                                <input type='email' name='email'><br>
                                <label for='message'>Причина удаления<label><br>
                                <textarea name='message' cols='50' rows='10' wrap='hard'></textarea><br>
                                <input type='submit' name='send_del_message' value='Отправить запрос'>";
        }

?>
