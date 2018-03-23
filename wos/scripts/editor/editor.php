<!DOCTYPE html>
<html>
<head>
	<title>Lection editor 1.0</title>
	<meta charset='utf8'>
</head>
<body>
	<?php
		
 
		require_once "../db_data.php";
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error) die($conn->connect_error);
		$conn->query("SET NAMES 'utf8'");
		
		include_once "subject_selection.php";
		subject_page_work($conn);
		
		if(isset($_POST['select_subject'])) {
			generate_block($conn, 'topic', 'subject', $_POST['subject_selection'], 'предмет');
		}
		if(isset($_POST['select_topic']) && $_POST['topic_selection']) {
			generate_block($conn, 'lection',  'topic', $_POST['topic_selection'], 'тема');
		}
		if(isset($_POST['delete_subject'])) {
			delete_material($conn, 'subject', 'Предмет');		
		}
		if(isset($_POST['delete_topic'])) {
			delete_material($conn, 'topic', 'Тема');		
		}
		if(isset($_POST['delete_lection'])) {
			delete_material($conn, 'lection', 'Лекция');	
		}

		
		if(isset($_POST['force_delete_subject'])) {
			force_delete_material($conn, "subject");		 
		} 

		if(isset($_POST['force_delete_topic'])) {
			force_delete_material($conn, "topic");
		}
	
		if(isset($_POST['force_delete_lection'])) {
			force_delete_material($conn, "lection");
		}
		
		if(isset($_POST['send_del_message'])) {
			$email = trim(fix_string($conn, $_POST['email']));
			if(!preg_match('~.+@.+\..+~i', $email)) die("Неверная электронная почта");
			$message = trim(fix_string($conn, $_POST['message']));
			require_once '../sender.php';
			send_mail('trymote@mail.ru', $email, $message, 3); 
		}
		$conn->close();
		echo "<br><form action='editor.php' method='POST'>
			<input type='submit' value='Отменить'>	
		</form>";

	function generate_block($conn, $item, $pre_block_name, $pre_select_id, $pre_block_text_type) {
		$query = "SELECT $pre_block_name"."_name FROM $pre_block_name"."s WHERE $pre_block_name"."_id='$pre_select_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$pre_select_name = $result->fetch_array(MYSQLI_NUM);
		echo "<br><p>Выбран(-a) $pre_block_text_type '$pre_select_name[0]'<p><br>";
		
		$query = "SELECT * FROM $item"."s WHERE $pre_block_name"."_id='$pre_select_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if(!$row[$item.'_name'] && !$row[$item.'_id']) {
			switch($item) {
				case "topic": 
					echo "<p>Для данного предмета еще не добавлено тем</p><br>";
					break;
				case "lection":
					echo "<p>Для данной темы еще не добавлено лекций</p><br>";
					break;
			}
		} else {
			echo "<form action='editor.php' method='POST'>";
			$row_number = $result->num_rows;
			echo "<select name='$item"."_selection'>";
			for($i = 0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo "<option value='".$row[$item.'_id']."'>".$row[$item.'_name']."</option>";
			}
			echo "</select>
				<input type='submit' name='select_$item' value='Выбрать'>
				<input type='submit' name='delete_$item' value='Удалить'>
			</form>";
		}
		echo "<form action='$item"."_selection.php' method='POST'>";
				echo "<input type='text' name='chosen_$pre_block_name"."_name' value='$pre_select_name[0]' style='display:none'>
			<input type='text' name='chosen_$pre_block_name"."_id' value='$pre_select_id' style='display:none'>
			<input type='submit' name='create_$item' value='Добавить новую'>
		</form>";
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
		$del_item_id = $_POST[$item.'_selection'];
		$query = "SELECT $item"."_name FROM $item"."s WHERE $item"."_id='$del_item_id'";
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$item_name = $result->fetch_array(MYSQLI_NUM);
		$result->close();
		echo "<br><form action='editor.php' method='POST'>
			<label style='color:#f00' for='force_delete_$item'>$text_item_type <b> '$item_name[0]' </b> и весь входящий материал будут безвозвратно удалены!</label><br>
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


	function force_delete_material($conn, $item) {
		$is_right = check_admin($conn, fix_string($conn, trim($_POST['pass'])));
		if(!$is_right) die();
		$del_item_id = fix_string($conn, trim($_POST["del_$item"."_id"]));
		if($item === "subject") {
			$query = "SELECT topic_id FROM topics WHERE subject_id='$del_item_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row_number = $result->num_rows;
			for($i=0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_NUM);
				$query = "DELETE FROM lections WHERE topic_id='$row[0]'";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
				$query = "DELETE FROM topics WHERE topic_id='$row[0]'";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
			}
			$query = "DELETE FROM subjects WHERE subject_id='$del_item_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
		} elseif($item === "topic") {
			$query = "SELECT lection_id FROM lections WHERE topic_id='$del_item_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
			$row_number = $result->num_rows;
			for($i=0; $i < $row_number; ++$i) {
				$result->data_seek($i);
				$row = $result->fetch_array(MYSQLI_NUM);
				$query = "DELETE FROM lections WHERE topic_id=$del_item_id";
				$del_result = $conn->query($query);
				if(!$del_result) die($conn->connect_error);
			}
			$query = "DELETE FROM topics WHERE topic_id=$del_item_id";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
		} elseif($item === "lection") {
			$query = "DELETE FROM lections WHERE lection_id='$del_item_id'";
			$result = $conn->query($query);
			if(!$result) die($conn->connect_error);
		}
		echo "<p>Удаление прошло успешно!</p><br>";
	}
	?>
</body>
</html>

