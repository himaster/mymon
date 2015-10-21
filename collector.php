#!/bin/env php

<?php

#if (isset($argv[1])) $task = $argv[1];
#else die("Task not set.");

#if (isset($argv[2])) $serverip = $argv[2];
#else die("Server IP not set.\n");

$file = fopen("./servers.conf", "r");

while(! feof($file)) {
    $line = fgets($file);
    if ($line[0] == '#') {
		continue;
	}
    $array = explode(" ", $line);

    $serverip = $array[0];
    $errs = $array[2];
    $elastic = $array[3];
    $db = $array[4];

	runtask("la", $serverip);
	if (isset($db)) runtask("rep", $serverip);
	if ($errs == 1) runtask("500", $serverip);
	if ($elastic == 1) runtask("elastic", $serverip);
}

return(0);





function runtask($task, $serverip) {
	switch ($task) {
		case "la":
			echo la($serverip);
			break;
		case "rep":
			rep($serverip);
			break;
		case "500":
			err500($serverip);
			break;
		case "elastic":
			elastic($serverip);
			break;
		default:
			echo "Unknown task.";
	}
}

function la($serverip) {
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
		$fontcolor = "<font color='green'>";
	} elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) {
		$fontcolor = "<font color='#CAC003'>";
	} else {
		$fontcolor = "<font color='red'>";
	}
	unset($connection);
	return "<a title=\"Click to show processes\" 
		href=\"https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/index.php?task=top&serverip=" .$serverip. "\"
		target='_blank'>" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
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

    if ($sql == "Yes") $sqlfontcolor = "<font color='green'>";
    else $sqlfontcolor = "<font color='red'>";

    if ($io == "Yes") $iofontcolor = "<font color='green'>";
    else $iofontcolor = "<font color='red'>";

    if ($delta == 0) $deltafontcolor = "<font color='green'>";
    else $deltafontcolor = "<font color='red'>";
    
    echo "<a title=\"Click to restart replication\" 
    		 href=\"#\" 
    		 onclick=\"myAjax('" .$serverip. "')\">
    		 SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    		 IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    		 Δ: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";

    unset($connection);
}

function err500($serverip) {
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die("<font color=\"red\">* * *</font>");
	}

	$stream = ssh2_exec($connection, "cat /var/log/500err.log");
    
    stream_set_blocking($stream, true);
    $str = stream_get_contents($stream);
    $str = trim(preg_replace('/\s+/', ' ', $str));
    
    echo "<a title=\"Click to show 500 errors\" 
    		 href=https://". $SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/index.php?task=500err&serverip=" .$serverip. " 
    		 target='_blank'>" .$str. "\n</a>";
    
    unset($connection);
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

	if (empty($error_output)) $elasticoutput = "<font color='green'>" .$output. "</font>";
	else $elasticoutput = "<font color='red'>Timeout</font>";

	echo $elasticoutput;
}
