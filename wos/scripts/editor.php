<?php
	function generate_select_tag($select_name,$what_to_choose ,$query, $conn) {
		$result = $conn->query($query);
		if(!$result) die($conn->connect_error);
		$rows = $result->num_rows;
		echo "<select name='$select_name"."_name'>";
		for($i = 0; $i < $rows; $i++) {
			$result->data_seek($i);
			$row = $result->fetch_array(MYSQLI_NUM);
			echo "<option value='".$row[0]."'>".$row[1]."</option>";
		}
		echo "</select>";
		echo "<input type='submit' name='$select_name"."_selected' value='Выбрать ".$what_to_choose."'><br>";
		if(isset($_POST[$select_name.'_selected'])) {
			$new_query = "SELECT $select_name"."_name FROM $select_name"."s WHERE $select_name"."_id='".$_POST[$select_name.'_name']."'";
			$result = $conn->query($query);
			$selected = $result->fetch_array(MYSQLI_NUM);
			return $selected[1];
		}
	}

	function check_select($select_type, $last_select) {
		static $last_subject;
		$last_subject = (isset($_POST[$select_type.'_selected']))? $last_select : "";
		if($last_subject) {
			echo "<p> >>>>>".$last_subject."<<<<< </p>";
			return $last_subject;
		}
	}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
        <title>Lection Editor 1.0</title>
        <meta charset='utf8'>
        <link rel='stylesheets' href='../../assets/css/style.css'>
</head>
<body> 
        <form action='editor.php' method='POST'>
                <label for='sub_name'>Предмет:</label><br>
		<?php
			require 'db_data.php';
			$conn = new mysqli($hn, $un, $pw, $db); 
			$conn->query('SET NAMES "utf8"');
			if($conn->connect_error) die($conn->connect_error);
			$last_subject = generate_select_tag("subject", "предмет",  "SELECT * FROM subjects", $conn);
			$last_subject = check_select('subject', $last_subject);
			$last_topic = generate_select_tag("topic","тему", "SELECT * FROM topics WHERE subject_id='$last_subject'", $conn);
			generate_select_tag("lection", "лекцию", "SELECT * FROM lections WHERE topic_id='$last_topic'", $conn);
		?>
	
</body>
</html>
