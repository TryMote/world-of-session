<?php

	function find_math($content) {
		
		preg_match_all('~[(\~)(.*?)(\~)]~', $content, $part);


		return $content;
	}

?>
