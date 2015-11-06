#!/usr/bin/php

<?php
include_once "functions.php";

declare(ticks=1);
set_error_handler('errHandler');
pcntl_signal(SIGTERM, "sigHandler");
$connection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($connection->connect_errno."\n");
$result = $connection->query("SELECT ip, servername, db, mysql, err, el FROM `mymon`.`stats`;") or die($connection->error);
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
	global $servername;
	$serverip = $array["ip"];
	$servername = $array["servername"];
	$errs = $array["err"];
	$elastic = $array["el"];
	$db = $array["db"];
	$mysql = $array["mysql"];
	common_log($servername. " - started");
	while (!$stop_server) {
		$ssh_conname = "ssh_".$servername;
		if ((!$$ssh_conname = ssh2_connect($serverip, 22)) or (!ssh2_auth_pubkey_file($$ssh_conname, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', ''))) {
			common_log("SSH connection or authorisation failed for ".$servername);
			die();
		}
		$mysql_conname = "mysql_".$servername;
		$$mysql_conname = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($$mysql_conname->connect_errno."\n");
		$result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET la='" .la($$ssh_conname, $serverip). "' WHERE ip='" .$serverip. "';");
		if (!isset($result)) common_log($servername." - LA not updated!");
		unset($result);
		if ($db == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET rep='" .runtask("rep", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET rep='' WHERE ip='" .$serverip. "';");
		if (!isset($result)) common_log($servername." - REP not updated!");
		unset($result);
		if ($errs == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `500`='" .runtask("500", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `500`='' WHERE ip='" .$serverip. "';");
		if (!isset($result)) common_log($servername." - 500 not updated!");
		unset($result);
		if ($elastic == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET elastic='" .runtask("elastic", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET elastic='' WHERE ip='" .$serverip. "';");
		if (!isset($result)) common_log($servername." - ELASTIC not updated!");
		unset($result);
		if ($mysql == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET locks='" .runtask("locks", $serverip). "' WHERE ip='" .$serverip. "';");
		else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET locks='' WHERE ip='" .$serverip. "';");
		if (!isset($result)) common_log($servername." - LOCKS not updated!");
		unset($result);
		$$mysql_conname->close();
		sleep(10);
	}
}

function runtask($task, $serverip) {
	global $servername;
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
			case "locks":
				return locks($connection, $serverip);
				break;
			default:
				common_log($servername." - unknown task.");
		}
		unset($connection);
	} else {
		common_log($servername." - retry #".$i++);
		goto start;
	}
}

function la($connection, $serverip) {
	global $servername;
	global $hostname;
	$la = substr(strrchr(ssh2_return($connection, "/usr/bin/uptime"),":"),1);
	$la1 = intval(array_map("trim",explode(",",$la))[0]);
	$core = ssh2_return($connection, "grep -c processor /proc/cpuinfo");
	if ($la1 < ($core/2)) $fontcolor = "<font color=\"green\">";
	elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) $fontcolor = "<font color=\"#CAC003\">";
	else $fontcolor = "<font color=\"red\">";
	return "<a title=\"Click to show processes\" 
			   href=\"https://" .$hostname. "/index.php?task=top&serverip=" .$serverip. "\"
			   target=\"_blank\">" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
}

function rep($connection, $serverip) {
	$data = array();
	$sql = $io = $delta = "***";
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "mysql -e 'show slave status\G'");
		foreach (explode("\n", $str) as $cLine) {
			list ($cKey, $cValue) = explode(':', $cLine, 2);
			$data[trim($cKey)] = trim($cValue);
		}
	    if ($data["Slave_SQL_Running"] == "Yes") {
	    	$sqlfontcolor = "<font color=\"green\">";
	    	$sql = "✓";
	    } else {
	    	$sqlfontcolor = "<font color=\"red\">";
	    	$sql = "x";
	    }
	    if ($data["Slave_IO_Running"] == "Yes") {
	    	$iofontcolor = "<font color=\"green\">";
	    	$io = "✓";
	    } else {
	    	$iofontcolor = "<font color=\"red\">";
	    	$io = "x";
	    }
	    if ($data["Seconds_Behind_Master"] == "0") $deltafontcolor = "<font color=\"green\">";
	    else $deltafontcolor = "<font color=\"red\">";
	} else {
		common_log($servername." - ssh2_auth_pubkey_file error!");
		$sqlfontcolor = $iofontcolor = $deltafontcolor = "<font color=\"red\">";
	}
    return "<a title=\"Click to restart replication\" 
    		   href=\"#\" 
    		   onclick=\"myAjax(\'" .$serverip. "\')\">
    		   SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    		   IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    		   Δ: " .$deltafontcolor. "<b>" .$data["Seconds_Behind_Master"]. "</b></font>\n</a>";
}

function err500($connection, $serverip) {
	global $servername;
	global $hostname;
	$str = "***";
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', ''))
	    $str = trim(ssh2_return($connection, "cat /var/log/500err.log"));
	else common_log($servername." - ssh2_auth_pubkey_file error!");
    return "<a title=\"Click to show 500 errors\" 
    		 href=https://". $hostname. "/index.php?task=500err&serverip=" .$serverip. " 
    		 target=\"_blank\">" .$str. "\n</a>";
}

function elastic($connection, $serverip) {
	$str = "***";
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
										 curl -sS -o /dev/null -XGET http://`/sbin/ifconfig eth1 | 
										 grep 'inet addr:' | 
										 cut -d: -f2 | 
										 awk '{ print $1}'`:9200/_cluster/health?pretty;
										 date2=\$((\$(date +'%s%N') / 1000000));
										 echo -n \$((\$date2-\$date1));");
		if ( $str == "Timeout" ) $fontcolor = "<font color=\"red\">";
		else $fontcolor = "<font color=\"green\">";
	} else {
		common_log($servername." - ssh2_auth_pubkey_file error!");
		$fontcolor = "<font color=\"red\">";
	}
	return $fontcolor.$str. "</font>";
}

function locks($connection, $serverip) {
	if (ssh2_auth_pubkey_file($connection, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', '')) {
		$str = ssh2_return($connection, "mysql -Ne \"SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE state LIKE '%lock%' AND time > 30\" | wc -l");
	    if (trim($str) == "0") $fontcolor = "<font color=\"green\">";
	    else $fontcolor = "<font color=\"red\">";
	} else {
		common_log($servername." - ssh2_auth_pubkey_file error!");
		$fontcolor = "<font color=\"red\">";
	}
    return $fontcolor.trim($str). "</font>";
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
