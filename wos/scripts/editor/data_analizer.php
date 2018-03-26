<?php
	 
	$location = '../../material/';
	$img_location = $location.'img/';
	$lections_location = $location.'lections/';

	function analize_file($file_type, $material_type, $material_index, $material_count_num) {
		$ext = analize_type($file_type);
		if($material_type != 'lection' && $ext == '.php') die("Неверный тип файла"); 
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
				$ext = '.git';
				break;
			case 'image/tif':
				$ext = '.tif';
				break;
			case 'image/png':
				$ext = '.png';
				break;
			case 'php':
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
?>
