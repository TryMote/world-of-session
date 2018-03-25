<?php
	 
	$location = '../../material/';
	$img_location = $location.'img/';
	$lections_location = $location.'lections/';

	function analize_file($file_type, $material_type, $material_index, $material_count_num) {
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
			default:
				$ext = '';
		}
		$filename = (!$ext)? 'default' : strtolower($material_index).'_'.$material_type.'_'.$material_count_num.$ext;
		return $filename;
	}	
?>
