<?php
	
	function open_file($conn, $location,  $filename, $file_is_opened) {
		if($file_is_opened == 0) {
			if(file_exists($location.$filename)) {
				echo "<p>Файл для данной лекции уже существует. В нем могу храниться данные о старой лекции, которая была уже удалена</p>
					<form action='editor.php' method='POST'>
						<input type='submit' name='rewrite' value='Перезаписать файл'>
						<input type='submit' name='continue' value='Продолжить редактирование в этом файле'>
						<input type='text' name='filename' value='$filename' style='display:none'>
					</form>";
			} else {
				$query = "UPDATE lections SET is_file_opened='1' WHERE lection_link='$filename'";
				$result = $conn->query($query);
				if(!$result) die($conn->connect_error);
				open_editor($location, $filename, 'w');
			}
		} else {
			open_editor($location, $filename, 'r');
		}
	}
	function open_editor($location, $filename, $mode) {
		if($mode == 'w') {
			$file = fopen($location.$filename, 'w');
			fwrite($file, "
					
	<div>

	</div>");
			fclose($file);	
		}
		$content = file_get_contents($location.$filename);
		$text = str_replace('<div class=\'lection_content\'>', '', $content);
		echo "<textarea cols='100' rows='50' name='content'>$text</textarea>
		<input type='submit' name='save' value='Сохранить'>
	</form>"; 		
	}	

?>
