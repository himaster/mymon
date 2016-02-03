<?php

#require_once "functions.php";
$connection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($connection->connect_errno."\n");

$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
									 mongo admin --quiet --eval 'printjson(db.serverStatus().connections.current)';
									 date2=\$((\$(date +'%s%N') / 1000000));
									 echo -n \$((\$date2-\$date1));");
	if ( $str == "Timeout" ) $fontcolor = "<font color=\"red\">";
	else $fontcolor = "<font color=\"green\">";
	return $fontcolor.$str. "</font>";