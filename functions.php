<?php

$hostname = "mymon.pkwteile.de";

function no_injection( $str='' ) { 
    $str = stripslashes( $str ); 
    $str = trim( $str ); 
    $str = htmlspecialchars( $str ); 
    return $str; 
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

function common_log($logmsg) {
	$date = date('Y-m-d H:i:s (T)');
	$f = fopen('/var/log/mymon/common.txt', 'a');
	if (!empty($f)) {
		fwrite($f, "$date: PID:".getmypid()."  $logmsg\r\n");
		fclose($f);
	}
}

function console_log($data) {
    if (is_array($data)) $output = "<script>console.log('console log: " .implode(',', $data). "');</script>";
    else $output = "<script>console.log('console log: " .$data. "');</script>";
    echo $output;
}