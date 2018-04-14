<?php 
	function start_test($test_id) {
			$_SESSION['test_id'] = $test_id;
			$_SESSION['done_index'] = 0; 
			$_SESSION['health'] = 5;
			$_SESSION['coins'] = 1;
	}

	function stop_test() {
		session_unset();
		session_destroy();
	}
?>
