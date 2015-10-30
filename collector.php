#!/usr/bin/php

<?php
include_once "functions.php";

declare(ticks=1);

set_error_handler('errHandler');

pcntl_signal(SIGTERM, "sigHandler");

$connection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($connection->connect_errno."\n");
$result = $connection->query("SELECT ip, servername, db, err, el FROM `mymon`.`stats`;") or die($connection->error);

$i = 1;

while($array = $result->fetch_assoc()) {
    $pid = pcntl_fork();
    if ($pid == -1) die("Child process can't be created");
    elseif ($pid) parent_();
    else {
		child_();
		exit;
    }
}
$result->free();
$connection->close();

exit;



function parent_() {

}

function child_() {
	global $array;
	global $stop_server;

	$connection1 = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($connection->connect_errno."\n");

	$serverip = $array["ip"];
	$errs = $array["err"];
	$elastic = $array["el"];
	$db = $array["db"];
	common_log($serverip. " - started");
	while (!$stop_server) {
		$result = $connection1->query("UPDATE `mymon`.`stats` SET la='" .runtask("la", $serverip). "' WHERE ip='" .$serverip. "';");

		if ($db == 1) $result = $connection1->query("UPDATE `mymon`.`stats` SET rep='" .runtask("rep", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $connection1->query("UPDATE `mymon`.`stats` SET rep='' WHERE ip='" .$serverip. "';");

		if ($errs == 1) $result = $connection1->query("UPDATE `mymon`.`stats` SET `500`='" .runtask("500", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $connection1->query("UPDATE `mymon`.`stats` SET `500`='' WHERE ip='" .$serverip. "';");

		if ($elastic == 1) $result = $connection1->query("UPDATE `mymon`.`stats` SET elastic='" .runtask("elastic", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $connection1->query("UPDATE `mymon`.`stats` SET elastic='' WHERE ip='" .$serverip. "';");

		sleep(10);
	}

}

function runtask($task, $serverip) {
	$i = 1;
	start:
	if ($connection = ssh2_connect($serverip, 22)) {
		switch ($task) {
			case "la":
				return la($connection, $serverip);
				break;
			case "rep":
				return rep($connection, $serverip);
				break;
			case "500":
				return err500($connection, $serverip);
				break;
			case "elastic":
				return elastic($connection, $serverip);
				break;
			default:
				echo "Unknown task.";
		}
		unset($connection);
	} else {
		common_log($serverip. "Retry #".$i++);
		goto start;
	}
}

function la($connection, $serverip) {
	global $servername;
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "/usr/bin/uptime");
		$la = trim(preg_replace('/\s+/', ' ', substr(strstr($str, 'average:'), 9, strlen($str))));
		$la1 = trim(preg_replace('/\s+/', ' ', intval(substr($la, 0, strpos($la, ',')))));
		$core = ssh2_return($connection, "grep -c processor /proc/cpuinfo");
		if ($la1 < ($core/2)) {
			$fontcolor = "<font color=\"green\">";
		} elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) {
			$fontcolor = "<font color=\"#CAC003\">";
		} else {
			$fontcolor = "<font color=\"red\">";
		}
	} else {
		$fontcolor = "<font color=\"red\">";
		$la = "* * *";
	}
	return "<a title=\"Click to show processes\" 
			   href=\"https://" .$servername. "/index.php?task=top&serverip=" .$serverip. "\"
			   target=\"_blank\">" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
}

function rep($connection, $serverip) {
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "mysql -e 'show slave status\G'");
	    $sql = trim(preg_replace('/\s+/', ' ', substr(strstr($str, 'Slave_SQL_Running:'), 19, 3)));
	    $io = trim(preg_replace('/\s+/', ' ', substr(strstr($str, 'Slave_IO_Running:'), 18, 3)));
	    $delta = trim(preg_replace('/\s+/', ' ', substr(strstr($str, 'Seconds_Behind_Master:'), 23, 2)));

	    if ($sql == "Yes") $sqlfontcolor = "<font color=\"green\">";
	    else $sqlfontcolor = "<font color=\"red\">";

	    if ($io == "Yes") $iofontcolor = "<font color=\"green\">";
	    else $iofontcolor = "<font color=\"red\">";

	    if ($delta == 0) $deltafontcolor = "<font color=\"green\">";
	    else $deltafontcolor = "<font color=\"red\">";
	} else {
		$sql = $io = $delta = "***";
		$sqlfontcolor = $iofontcolor = $deltafontcolor = "<font color=\"red\">";
	}
    return "<a title=\"Click to restart replication\" 
    		   href=\"#\" 
    		   onclick=\"myAjax(\'" .$serverip. "\')\">
    		   SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    		   IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    		   Δ: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";
}

function err500($connection, $serverip) {
	global $servername;
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', ''))
	    $str = trim(preg_replace('/\s+/', ' ', ssh2_return($connection, "cat /var/log/500err.log")));
    else
    	$str = "***";
    return "<a title=\"Click to show 500 errors\" 
    		 href=https://". $servername. "/index.php?task=500err&serverip=" .$serverip. " 
    		 target=\"_blank\">" .$str. "\n</a>";
}

function elastic($connection, $serverip) {
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
										 curl -sS -o /dev/null -XGET http://`/sbin/ifconfig eth1 | 
										 grep 'inet addr:' | 
										 cut -d: -f2 | 
										 awk '{ print $1}'`:9200/_cluster/health?pretty;
										 date2=\$((\$(date +'%s%N') / 1000000));
										 echo -n \$((\$date2-\$date1));");
		if ( $str == "Timeout" ) {
			$str = "***";
			$fontcolor = "<font color=\"red\">";
		} else {
			$fontcolor = "<font color=\"green\">";
		}
	} else {
		$str = "***";
		$fontcolor = "<font color=\"red\">";
	}
	return $fontcolor.$str. "</font>";
}

function sigHandler($signo) {
	global $stop_server;
	global $connection;
	switch($signo) {
		case SIGTERM: {
			$stop_server = true;
			common_log("SIGTERM stop");
			break;
		}
		
		case SIGPIPE: {
			$stop_server = true;
			common_log("SIGPIPE stop");
			break;
		}

		default: {
			break;
		}
	}
}

function errHandler($errno, $errmsg, $filename, $linenum) {
	$date = date('Y-m-d H:i:s (T)');
	$f = fopen('/var/log/mymon/errors.txt', 'a');
	if (!empty($f)) {
		$filename  = str_replace($_SERVER['DOCUMENT_ROOT'],'',$filename);
		fwrite($f, "$date: PID:".getmypid()."  $errmsg - $filename - $linenum\r\n");
		fclose($f);
	}
}
