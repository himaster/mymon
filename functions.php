<?php
function no_injection( $str='' ) { 
    $str = stripslashes( $str ); 
    $str = trim( $str ); 
    $str = htmlspecialchars( $str ); 
    return $str; 
} 

function get_data( $task, $serverip ) {
	public $dbconnection;
	$query = "SELECT `stats`.`" .$task. "` FROM stats WHERE ip=\"{$serverip}\" LIMIT 1";
	$result = $dbconnection->query($query) or die($dbconnection->error());
	$row = $dbconnection->fetch_assoc( $result );
	return $row["$task"];
}

function ssh2_return( $connection, $query ) {
	$stream = ssh2_exec( $connection, $query );

    $error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );

	stream_set_blocking( $error_stream, TRUE );
	$error_output = stream_get_contents( $error_stream );

	stream_set_blocking( $stream, TRUE );
	$output = stream_get_contents( $stream );

	if (!empty($error_output)) return "Timeout";
	else return $output;
}