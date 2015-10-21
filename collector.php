#!/bin/env php

<?php

include("connect.php");


$servername = "mymon.pkwteile.de";
while (true) {
	$query = "SELECT ip, servername, db, err, el FROM `mymon`.`stats`;";
	$sql = mysqli_query($connection, $query) or die(mysql_error());
	while($array = mysqli_fetch_array($sql)) {
	    $serverip = $array["ip"];
	    echo $serverip;
	    $errs = $array["err"];
	    $elastic = $array["el"];
	    $db = $array["db"];

		$query = "UPDATE `mymon`.`stats` SET la='" .runtask("la", $serverip). "' WHERE ip='" .$serverip. "';";
		$sql = mysqli_query($connection, $query) or die(mysqli_error());

		if (isset($db)) {
			$query = "UPDATE `mymon`.`stats` SET rep='" .runtask("rep", $serverip). "' WHERE ip='" .$serverip. "';";
			$sql = mysqli_query($connection, $query) or die(mysqli_error());
		}
		if ($errs == 1) {
			$query = "UPDATE `mymon`.`stats` SET `500`='" .runtask("500", $serverip). "' WHERE ip='" .$serverip. "';";
			$sql = mysqli_query($connection, $query) or die(mysqli_error());
		}
		if ($elastic == 1) {
			$query = "UPDATE `mymon`.`stats` SET elastic='" .runtask("elastic", $serverip). "' WHERE ip='" .$serverip. "';";
			$sql = mysqli_query($connection, $query) or die(mysqli_error());
		}
	}
	mysqli_close($db);

}
return(0);





function runtask($task, $serverip) {
	switch ($task) {
		case "la":
			return la($serverip);
			break;
		case "rep":
			return rep($serverip);
			break;
		case "500":
			return err500($serverip);
			break;
		case "elastic":
			return elastic($serverip);
			break;
		default:
			echo "Unknown task.";
	}
}

function la($serverip) {
	global $servername;
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die("<font color=\"red\">* * *</font>");
	}
	$stream = ssh2_exec($connection, "/usr/bin/uptime");
	stream_set_blocking($stream, true);
	$str = stream_get_contents($stream);
	$la = substr(strstr($str, 'average:'), 9, strlen($str));
	$la = trim(preg_replace('/\s+/', ' ', $la));
	$la1 = substr($la, 0, strpos($la, ','));
	$la1 = intval($la1);
	$la1 = trim(preg_replace('/\s+/', ' ', $la1));

	$stream = ssh2_exec($connection, "grep -c processor /proc/cpuinfo");
	stream_set_blocking($stream, true);
	$core = stream_get_contents($stream);

	if ($la1 < ($core/2)) {
		$fontcolor = "<font color=\"green\">";
	} elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) {
		$fontcolor = "<font color=\"#CAC003\">";
	} else {
		$fontcolor = "<font color=\"red\">";
	}
	unset($connection);
	return "<a title=\"Click to show processes\" 
		href=\"https://" .$servername. "/index.php?task=top&serverip=" .$serverip. "\"
		target=\"_blank\">" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
}

function rep($serverip) {
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die("<font color=\"red\">* * *</font>");
	}

	$stream = ssh2_exec($connection, "mysql -e 'show slave status\G'");

    stream_set_blocking($stream, true);
    $str = stream_get_contents($stream);

    $sql = substr(strstr($str, 'Slave_SQL_Running:'), 19, 3);
    $sql = trim(preg_replace('/\s+/', ' ', $sql));

    $io = substr(strstr($str, 'Slave_IO_Running:'), 18, 3);
    $io = trim(preg_replace('/\s+/', ' ', $io));

    $delta = substr(strstr($str, 'Seconds_Behind_Master:'), 23, 2);
    $delta = trim(preg_replace('/\s+/', ' ', $delta));

    if ($sql == "Yes") $sqlfontcolor = "<font color=\"green\">";
    else $sqlfontcolor = "<font color=\"red\">";

    if ($io == "Yes") $iofontcolor = "<font color=\"green\">";
    else $iofontcolor = "<font color=\"red\">";

    if ($delta == 0) $deltafontcolor = "<font color=\"green\">";
    else $deltafontcolor = "<font color=\"red\">";

    unset($connection);
    
    return "<a title=\"Click to restart replication\" 
    		 href=\"#\" 
    		 onclick=\"myAjax(\'" .$serverip. "\')\">
    		 SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    		 IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    		 Δ: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";
}

function err500($serverip) {
	global $servername;
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die("<font color=\"red\">* * *</font>");
	}

	$stream = ssh2_exec($connection, "cat /var/log/500err.log");
    
    stream_set_blocking($stream, true);
    $str = stream_get_contents($stream);
    $str = trim(preg_replace('/\s+/', ' ', $str));
    
    unset($connection);

    return "<a title=\"Click to show 500 errors\" 
    		 href=https://". $servername. "/index.php?task=500err&serverip=" .$serverip. " 
    		 target=\"_blank\">" .$str. "\n</a>";
}

function elastic($serverip) {
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die("<font color=\"red\">* * *</font>");
	}
	
	$stream = ssh2_exec($connection, "date1=\$((\$(date +'%s%N') / 1000000));
		curl -sS -o /dev/null -XGET http://`/sbin/ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`:9200/_cluster/health?pretty;
		date2=\$((\$(date +'%s%N') / 1000000));
		echo -n \$((\$date2-\$date1));");
	
	$error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );
	stream_set_blocking( $error_stream, TRUE );
	$error_output = stream_get_contents( $error_stream );

	stream_set_blocking( $stream, TRUE );
	$output = stream_get_contents( $stream );

	if (empty($error_output)) $elasticoutput = "<font color=\"green\">" .$output. "</font>";
	else $elasticoutput = "<font color=\"red\">Timeout</font>";

	unset($connection);

	return $elasticoutput;
}

?>
