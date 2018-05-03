<?php 

function show_profile($id) {
	$conn = get_connection_object();
	$xp = get_first_select_array($conn, "SELECT user_xp FROM user_second_data WHERE user_id='$id'", MYSQLI_NUM)[0];
	$result = get_first_query_result($conn, "SELECT status_name FROM statuses WHERE status_xp<='$xp'");
	$max_range = $result->num_rows;
	$status = get_select_array($conn, "SELECT status_name FROM statuses WHERE status_xp<='$xp'", $max_range-1, MYSQLI_NUM)[0];
	echo "<p class='user_status'>$status</p>
	<p class='user_xp'>($xp)</p>";
	$result = get_first_query_result($conn, "SELECT test_id FROM users_results WHERE user_id='$id'");
}

?>
