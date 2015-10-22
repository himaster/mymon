<?php
function no_injection($str='') { 
    $str = stripslashes($str); 
    $str = mysql_real_escape_string($str); 
    $str = trim($str); 
    $str = htmlspecialchars($str); 
    return $str; 
} 

function get_data($task, $serverip) {
	$query = "SELECT " .$task. " FROM stats WHERE ip=\"{$serverip}\" LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row["$task"];
}
?>