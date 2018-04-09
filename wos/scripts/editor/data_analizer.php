<?php
	 
	$location = '../../material/';
	$img_location = $location.'img/';
	$lections_location = $location.'lections/';
	$tests_location = $location.'tests/';

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
<?php include_once '../../menu.php' ?>
</header>
<div id='test_page'>
<div id='lections_list'>
<?php include_once '../lections_list.php'; 
show_list('$topic_name');
?>
</div>
<div id='main_headers'>
<h1>$topic_name</h1>
<h2>Тест</h2>
</div>
<div id='test_content'>
<?php 
include_once 'show_test.php';
show_test('$test_id');
?>
</div>
<div class='navigator'>
<?php include_once 'test_navigator.php';
show_navigator('$topic_name');
?>
</div>
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
<div id='lection_page'>
<div id='lections_list'>
<?php include_once '../lections_list.php'; 
show_list('$topic_name');
?>
</div>
<div id='main_headers'>
<h1>$topic_name</h1>
<h2>$lection_name</h2>
</div>
<div id='lection_content'>
$content
</div>
<div id='lection_test'>
</div>
<div id='lection_controller'>
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
		$result = $conn->query("SELECT password FROM sign_in WHERE user_id=1"); 
		if(!$result) die($conn->connect_error); 
		$row = $result->fetch_array(MYSQLI_NUM); 
		if(!hash_equals($row[0], crypt($pass, $row[0]))) { 
			die("Неверный пароль"); 
		} else { 
			return true; 
		} 
	} 	

       function delete_material($conn, $item, $text_item_type) {
		if($item != 'test') {
			if(isset($_POST[$item.'_selection'])) $del_item_id = fix_string($conn, $_POST[$item.'_selection']);
                	$query = "SELECT $item"."_name FROM $item"."s WHERE $item"."_id='$del_item_id'";
               	
			$result = $conn->query($query);
               		if(!$result) die($conn->connect_error);
               	 	$item_name = $result->fetch_row()[0];
                	$result->close();
		} else {
			$item_name = '';
			$del_item_id = $_POST[$item.'_id'];
		}
		if($item != 'test') {
			$action = 'editor.php';
		} else {
			$action = 'test_creator.php';
		}
                echo "<br><form action='$action' method='POST'>
                        <label style='color:#f00' for='force_delete_$item'>$text_item_type <b> $item_name </b> и весь входящий материал будут безвозвратно удалены!</label><br>
                        <input type='password' name='pass' placeholder='Ключ' required>
                        <input type='submit' name='force_delete_$item' value='Удалить'>
                        <input type='text' name='del_$item"."_id' value='$del_item_id' style='display:none'>
                        </form>";
                echo "<p>Если не знаете ключa, вы можете отправить запрос на удаление</p><br>
                        <form action='editor.php' method='POST'>
                                <label for='email'> Ваша электронная почта</label><br>
                                <input type='email' name='email' required><br>
                                <label for='message'>Причина удаления<label><br>
                                <textarea name='message' cols='50' rows='10' wrap='hard' required></textarea><br>
                                <input type='submit' name='send_del_message' value='Отправить запрос'>
                        </form>";
        }

?>
