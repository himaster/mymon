#!/usr/bin/php

<?php
include_once "functions.php";

$connection = mysqli_connect("188.138.234.38", "mymon", "eiGo7iek");
if (!$connection) die( "MySQL server unavailable." );
if (!mysqli_select_db($connection, "mymon")) die( "Can't use db." );
$servername = "mymon.pkwteile.de";
$query = "SELECT ip, servername, db, err, el FROM `mymon`.`stats`;";
$result = mysqli_query($connection, $query) or die("MySQL error :  " .mysqli_error($connection));
$i = 1;

while($array = mysqli_fetch_assoc($result)) {
    $pid = pcntl_fork();
    if ($pid == -1) {
	die("Child process can't be created");
    } elseif ($pid) {
		parent_();
    } else {
		child_();
		exit;
    }
}

mysqli_close($connection);

exit;



function parent_() {

}

function child_() {
	global $array;
	
	echo -n "PID:".getmypid();

	$connection1 = mysqli_connect("188.138.234.38", "mymon", "eiGo7iek");
	if (!$connection1) die( "MySQL server unavailable." );
	if (!mysqli_select_db($connection1, "mymon")) die( "Can't use db." );

	$serverip = $array["ip"];
	$errs = $array["err"];
	$elastic = $array["el"];
	$db = $array["db"];
	echo " - ".$serverip. " - started\n";

	$query = "UPDATE `mymon`.`stats` SET la='" .runtask("la", $serverip). "' WHERE ip='" .$serverip. "';";
	$result = mysqli_query($connection1, $query) or die($query.mysqli_error($connection1));

	if ($db == 1) $query = "UPDATE `mymon`.`stats` SET rep='" .runtask("rep", $serverip). "' WHERE ip='" .$serverip. "';";
	else $query = "UPDATE `mymon`.`stats` SET rep='' WHERE ip='" .$serverip. "';";
	$result = mysqli_query($connection1, $query) or die($query.mysqli_error($connection1));

	if ($errs == 1) $query = "UPDATE `mymon`.`stats` SET `500`='" .runtask("500", $serverip). "' WHERE ip='" .$serverip. "';";
	else $query = "UPDATE `mymon`.`stats` SET `500`='' WHERE ip='" .$serverip. "';";
	$result = mysqli_query($connection1, $query) or die($query.mysqli_error($connection1));

	if ($elastic == 1) $query = "UPDATE `mymon`.`stats` SET elastic='" .runtask("elastic", $serverip). "' WHERE ip='" .$serverip. "';";
	else $query = "UPDATE `mymon`.`stats` SET elastic='' WHERE ip='" .$serverip. "';";
	$result = mysqli_query($connection1, $query) or die($query.mysqli_error($connection1));

	mysqli_close($connection1);
	unset($result);

	echo "PID:".getmypid()." - ".$serverip. " - ended\n";

}


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
	$str = ssh2_return($connection, "/usr/bin/uptime");
	$la = substr(strstr($str, 'average:'), 9, strlen($str));
	$la = trim(preg_replace('/\s+/', ' ', $la));
	$la1 = substr($la, 0, strpos($la, ','));
	$la1 = intval($la1);
	$la1 = trim(preg_replace('/\s+/', ' ', $la1));

	$core = ssh2_return($connection, "grep -c processor /proc/cpuinfo");

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

	$str = ssh2_return($connection, "mysql -e 'show slave status\G'");

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

	$str = ssh2_return($connection, "cat /var/log/500err.log");
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
	
	$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
		curl -sS -o /dev/null -XGET http://`/sbin/ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`:9200/_cluster/health?pretty;
		date2=\$((\$(date +'%s%N') / 1000000));
		echo -n \$((\$date2-\$date1));");

	unset($connection);
	if ( $str == "Timeout" ) return "<font color=\"red\">" .$str. "</font>";
	else return "<font color=\"green\">" .$str. "</font>";
}

