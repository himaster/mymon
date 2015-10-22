#!/bin/env php

<?php
include_once "functions.php";

$dblocation = "localhost";
$dbname = "mymon";
$dbuser = "mymon";
$dbpasswd = "eiGo7iek";
$connection = mysqli_connect($dblocation,$dbuser,$dbpasswd);
if (!$connection) {
	echo( "<P> В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно. </P>" );
	exit();
}
if (!mysqli_select_db($connection, $dbname)) {
	echo( "<P> В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно. .</P>" );
	exit();
}


$servername = "mymon.pkwteile.de";
$query = "SELECT ip, servername, db, err, el FROM `mymon`.`stats`;";
$result = mysqli_query($connection, $query) or die("ERROR!!! :  " .mysqli_error($connection));
while($array = mysqli_fetch_assoc($result)) {
    $serverip = $array["ip"];
    echo $serverip. "\n";
    $errs = $array["err"];
    $elastic = $array["el"];
    $db = $array["db"];

	$query = "UPDATE `mymon`.`stats` SET la='" .runtask("la", $serverip). "' WHERE ip='" .$serverip. "';";
	$sql = mysqli_query($connection, $query) or die(mysqli_error());

	if ($db == 1) {
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

mysqli_free_result($result);
unset($result);

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
    echo "<a title=\"Click to restart replication\"";

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

	return "<font color=\"green\">" .$str. "</font>";
}

?>
